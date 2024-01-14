<?php
/**
 * @package		PaidSystem
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

require_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/assets/paymentplugin.php');
require_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/format.php');
require_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/log.php');

class plgInvoicingpaymentHipay extends InvoicingAbstractPaymentPlugin
{
    
    public function __construct(&$subject, $config = array())
	{
		if(version_compare(JVERSION, '1.6', 'ge')) {
			$defaultimg = JURI::root().'plugins/invoicingpayment/hipay/hipay/hipay.jpg';
		} else {
			$defaultimg = JURI::root().'plugins/invoicingpayment/hipay/hipay.jpg';
		}	
		
		$config = array_merge($config, array(
			'ppName'		=> 'hipay',
			'ppKey'			=> 'PLG_INVOICINGPAYMENT_HIPAY_TITLE',
			'ppImage'		=> $defaultimg)
				);
		
		parent::__construct($subject, $config);
	}
    
    function onInvoicingPaymentDisplay($paymentmethod,$data)
	{	
		if($paymentmethod != $this->ppName) return false;
		
		$imodel = InvoicingModelInvoices::getInstance('Invoices', 'InvoicingModel');
		$item = $imodel->getItem($data->invoicing_invoice_id);
		$item->processor = 'hipay';
		$imodel->update($item);
		        
		$app = \JFactory::getApplication();
		$db =\JFactory::getDbo();
		
		$this->order_id = $data->id;
		$baseurl = JURI::base();

		ob_start();
		require_once(dirname(__FILE__).'/hipay/mapi_package.php');
		$params = new HIPAY_MAPI_PaymentParams();
		// Param�tres de connexion � la plateforme Hipay. Attention, il ne s'agit pas du login et mot de passe utilis� pour se connecter
		// au site Hipay, mais du login et mot de passe propre � la connexion � la passerelle. Le login est l'id du compte associ� au site, 
		// le mot de passe est le � mot de passe marchand � associ� au site.
		$params->setLogin($this->params->get('loginID',''),$this->params->get('password',''));
		// Les sommes seront cr�dit�es sur le compte 59118, sauf les taxes qui seront cr�dit�es sur le compte 59119
		$params->setAccounts($this->params->get('account',''));
		// L'interface de paiement sera, par d�faut, en fran�ais international
		$params->setLocale($this->params->get('locale',''));
		// L'interface sera l'interface Web
		$params->setMedia('WEB');
		// Le contenu de la commande s'adresse � un public au moins �g� de 16 ans.
		$params->setRating($this->params->get('rating',''));
		// Il s'agit d'un paiement simple
		$params->setPaymentMethod(HIPAY_MAPI_METHOD_SIMPLE);
		// La capture sera imm�diate
		$params->setCaptureDay(HIPAY_MAPI_CAPTURE_IMMEDIATE);
		// Les montants sont donn�s en euros, la devise associ�e au compte du site.
		$params->setCurrency($this->params->get('currency',''));
		// L'identifiant au choix du commer�ant pour cette commande est REF6522 
		$params->setIdForMerchant('REF6522');
		// Deux donn�es du type cl�=valeur sont d�clar�es et seront retourn�es au commer�ant apr�s le paiement dans les
		// flux de notification.
		$params->setMerchantDatas('order_id', $data->invoicing_invoice_id);
		// Cette commande se rapporte au site web qu'a d�clar� le marchand dans la plateforme Hipay et qui a l'identifiant 9
		$params->setMerchantSiteId($this->params->get('siteID',''));
		// Si le paiement est accept�, le client est redirig� vers la page success.html
		$params->setUrlOk($baseurl.'index.php?option=com_invoicing&amp;view=message&amp;layout=complete');
		// Si le paiement est refus�, le client est redirig� vers la page refused.html
		$params->setUrlNok($baseurl.'index.php?option=com_invoicing&amp;view=message&amp;layout=cancel');
		// Si le paiement est annul� par le client, il est redirig� vers la page cancel.html
		$params->setUrlCancel($baseurl.'index.php?option=com_invoicing&amp;view=message&amp;layout=cancel');
		// L'email de notification du marchand post� en parall�le des notifications http sur l'url de ack
		// cf chap 19 : R�ception de notification d'un paiement par le marchand
		$params->setEmailAck($this->params->get('emailAck',''));
		// Le site du marchand est notifi� automatiquement du r�sultat du paiement par un appel au script "listen_hipay_notification.php"
		// cf chap 19 : R�ception de notification d'un paiement par le marchand
		$params->setUrlAck($baseurl.'index.php?option=com_invoicing&amp;view=payment&amp;task=process&amp;method=hipay');
		$t=$params->check();
		if (!$t)
		{
			echo "Erreur de cr�ation de l'objet paymentParams";
			exit;
		}
		
		// Premier produit : 2 exemplaires d'un livre � 12.50 euros l'unit� sur (les taxes $tax3 et $tax2)
		
		$item1 = new HIPAY_MAPI_Product();
		$item1->setName(InvoicingHelperFormat::formatOrderNumber($data));
		$item1->setInfo('');
		$item1->setquantity(1);
		//$item1->setRef('JV005');
		$item1->setCategory($this->params->get('categoryId',''));
		
		
		$item1->setPrice($data->gross_amount);
		$t=$item1->check();
		if (!$t)
		{
			echo "Erreur de cr�ation de l'objet product";
			exit;
		}
		
		$order = new HIPAY_MAPI_Order();
		// Titre et informations sur la commande
		$order->setOrderTitle(InvoicingHelperFormat::formatOrderNumber($data));
		$order->setOrderInfo('');
		// La cat�gorie de la commande est 3
		// cf annexe 7 pour savoir comment obtenir la liste des cat�gories disponibles pour votre site
		$order->setOrderCategory($this->params->get('categoryId',''));
		$t=$order->check();
		if (!$t)
		{
			echo "Erreur de cr�ation de l'objet order";
			exit;
		}
		
		try {
			$commande = new HIPAY_MAPI_SimplePayment($params, $order, array($item1));
		}
		catch (Exception $e) {
			echo " Error " .$e->getMessage();
		}
		
		$xmlTx = $commande->getXML();
		//var_dump($xmlTx);
		$output = HIPAY_MAPI_SEND_XML::sendXML($xmlTx);
		
		
		$r=HIPAY_MAPI_COMM_XML::analyzeResponseXML($output, $url, $err_msg);
		if ($r===true) {
			// On renvoie l'internaute vers l'url indiqu�e par la plateforme Hipay
			header('Location: '.$url) ;
			// echo $url;
		} else {
			// Une erreur est intervenue
			echo $err_msg;
			
			//header('Location: '.$url_error) ;
		}
		echo \JText::_('PLG_INVOICINGPAYMENT_PAYPAL_PLEASE_WAIT_UNTIL_REDIRECTION');
		$html = ob_get_clean();
		return $html;
	}
    
    public function onInvoicingPaymentNotification($paymentmethod, $data)
	{
		// Check if we're supposed to handle this
		if($paymentmethod != $this->ppName) return false;
		
		InvoicingHelperLog::log("Hipay Begin");
		
		require_once(dirname(__FILE__).'/hipay/mapi_package.php');
		
		$input = \JFactory::getApplication()->input;
		$xml = $input->get('xml', '', 'post', 'string', ALLOWRAW );//JREQUEST_ALLOWRAW
		
		InvoicingHelperLog::log($xml);
		
		$isValid = false;
		
		// R�ception de la notification depuis la plateforme Hipay
		// Le flux XML[C] est envoy� par POST, dans le champ " xml ".
		// La fonction analyzeNotificationXML traite le flux XML en provenance de la plateforme Hipay.
		$r = HIPAY_MAPI_COMM_XML::analyzeNotificationXML($xml, $operation, $status, $date, $time, $transid, $amount,
								 $currency, $idformerchant, $merchantdatas, $emailClient, $subscriptionId, $refProduct);
		// Une erreur s'est produite
		if ($r===false)
		{
			// Log de l'erreur dans un fichier texte sur le serveur
			InvoicingHelperLog::log("Error");
		}
		else
		{
			InvoicingHelperLog::log("Ok");
			// Le flux a �t� trait�
			// Le marchand peut ici mettre à jour sa base de donn�es de commandes et effectuer d'autres traitements.
			$log =  "operation=$operation\n
					 status=$status\n
					 date=$date\n
					 time=$time\n
					 transaction_id=$transid\n
					 amount=$amount\n
					 currency=$currency\n
					 idformerchant=$idformerchant\n
					 merchantData=". print_r($merchantdatas,true)."\n
					 emailClient=$emailClient\n
					 subscriptionId=$subscriptionId\n
					 refProduct=".print_r($refProduct,true);
			InvoicingHelperLog::log($log);
			
			$order_id = $merchantdatas['order_id'];
			
			if ($this->checkValidity($order_id,floatval($amount),true) == true) {
				$this->validPayment($order_id,$paymentmethod);
				$isValid = true;
			} 
		}
		
		// Fraud attempt? Do nothing more!
		if(!$isValid) return false;
	}
}