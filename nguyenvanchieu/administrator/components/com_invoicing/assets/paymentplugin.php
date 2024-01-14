<?php
/**
 * @package		invoicing
 * @copyright	Copyright (c)2010-2012 JoomPROD
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die();

include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/log.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/format.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/cparams.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/models/invoices.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/models/currencies.php');

/**
 * Invoicing abstract payment plugin class
 */
abstract class InvoicingAbstractPaymentPlugin extends \JPlugin
{
	/** @var string Name of the plugin, returned to the component */
	protected $ppName = 'abstract';
	
	/** @var string Translation key of the plugin's title, returned to the component */
	protected $ppKey = 'PLG_INVOICING_ABSTRACT_TITLE';
	
	/** @var string Image path, returned to the component */
	protected $ppImage = '';
	
	public function __construct(&$subject, $config = array())
	{
		if(version_compare(JVERSION, '1.6', 'ge')) {
            if(!is_object($config['params'])) {
                jimport('joomla.registry.registry');
                $config['params'] = new \JRegistry($config['params']);
            }
        }
		
		parent::__construct($subject, $config);
		
		if(array_key_exists('ppName', $config)) {
			$this->ppName = $config['ppName'];
		}
		
		if(array_key_exists('ppImage', $config)) {
			$this->ppImage = $config['ppImage'];
		}
		
		$name = $this->ppName;
		
		if(array_key_exists('ppKey', $config)) {
			$this->ppKey = $config['ppKey'];
		} else {
			$this->ppKey = "PLG_INVOICING_{$name}_TITLE";
		}
		
		
		// Load the language files
		$jlang = \JFactory::getLanguage();
		$jlang->load('plg_invoicingpayment_'.$name, JPATH_ADMINISTRATOR, 'en-GB', true);
		$jlang->load('plg_invoicingpayment_'.$name, JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
		$jlang->load('plg_invoicingpayment_'.$name, JPATH_ADMINISTRATOR, null, true);
	}
	
	public final function onInvoicingPaymentGetIdentity()
	{
		$title = $this->params->get('title','');
		if(empty($title)) $title = \JText::_($this->ppKey);
		
		$image = trim($this->params->get('image',''));
		if(empty($image)) {
			$image = $this->ppImage;
		}
		
		$ret = array(
			'name'		=> $this->ppName,
			'title'		=> $title,
			'image'		=> $image
		);
		
		return (object)$ret;
	} 
	
	/**
	 * Returns the payment form to be submitted by the user's browser. The form must have an ID of
	 * "paymentForm" and a visible submit button.
	 * 
	 * @param string $paymentmethod Check it against $this->ppName
	 * @param array $data Input data
	 * @return string
	 */
	abstract public function onInvoicingPaymentDisplay($paymentmethod, $data);
	
	/**
	 * Processes a callback from the payment processor
	 * 
	 * @param string $paymentmethod Check it against $this->ppName
	 * @param array $data Input data
	 */
	abstract public function onInvoicingPaymentNotification($paymentmethod, $data);
		
	protected final function checkValidity($id,$amount,$isGrossAmount=true,$currency=null)
	{
		$invoice = null;
		if($id <= 0) {
			$isValid = false;
			InvoicingHelperLog::log('The referenced Invoice ID is invalid');
			return false;
		}
		$imodel = InvoicingModelInvoices::getInstance('Invoices', 'InvoicingModel');
		$invoice = $imodel->getItem($id);
		if(is_null($invoice)) {
			$isValid = false;
			InvoicingHelperLog::log('The referenced Invoice ID is invalid');
			return false;
		}
		if (($invoice->status == 'PAID')||($invoice->status=='CANCELLED')) {
			$isValid = false;
			InvoicingHelperLog::log('Already Paid or cancelled');
			return false;
		}
	
		if ($isGrossAmount == true) {
			$referenceAmount = $invoice->gross_amount;
		} else {
			$referenceAmount = $invoice->net_amount;
		}

		if ($referenceAmount != $amount) {
			$isValid = false;
			InvoicingHelperLog::log('Invalid Amount !');
			return false;
		}
		
		if ($currency != null) {
			$currency_code = trim(InvoicingHelperFormat::formatCurrency($invoice->currency_id,"code"));
			$currency = trim($currency);
			if (($currency != $currency_code)&&($currency != $invoice->currency_id)) {
				$isValid = false;
				InvoicingHelperLog::log("1=(".$currency .") 2=(".$currency_code.") 3=(".$invoice->currency_id.")");
				InvoicingHelperLog::log('Invalid Currency !');
				return false;
			}
		}
		
		return true;
	}
	
	protected final function validPayment($id) {
		$model = InvoicingModelInvoices::getInstance('Invoices', 'InvoicingModel');
		$invoice = $model->getItem($id);
		
		if(is_null($invoice)) {
			return false;
		}
		
		$invoice->status = 'PAID';
		$invoice->processor = $this->ppName;
		
		//Need to get a new model to cancel cache !!!!!
		$model = InvoicingModelInvoices::getInstance('Invoices', 'InvoicingModel');
		$model->update($invoice);
	
		InvoicingHelperLog::log("Payment OK ".print_r($invoice,true));
	}
    
    public function onInvoicingPaymentSelection($item) {
		$processor = $this->onInvoicingPaymentGetIdentity();
		
        $url = \JRoute::_('index.php?option=com_invoicing&view=payment&id='.$item->invoicing_invoice_id.'&method='.$processor->name);
        if (InvoicingHelperCparams::getParam('use_paymentimage',1)==1) {
            return '<div class="paymentMeth"><a href="'.$url.'" class="paymentbutton"><div class="paymentImg "><img class="img-responsive" src="'.htmlspecialchars($processor->image).'" /></div><div class="paymentTxt">'.htmlspecialchars(\JText::_($processor->title)).'</div></a></div>';
        }
        else {
            return '<div class="paymentMeth"><a href="'.$url.'" class="paymentbutton"><div class="paymentTxt">'.htmlspecialchars(\JText::_($processor->title)).'</div></a></div>';
        }
    }
}
