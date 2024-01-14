<?php
/**
 * @package		Invoicing
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license GNU General Public License version 3, or later
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

require_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/assets/paymentplugin.php');
require_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/format.php');
require_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/log.php');

class plgInvoicingpaymentAuthorizenet extends InvoicingAbstractPaymentPlugin
{
	public function __construct(&$subject, $config = array())
	{
		$config = array_merge($config, array(
				'ppName'		=> 'authorizenet',
				'ppKey'			=> 'PLG_INVOICINGPAYMENT_AUTHORIZENET_TITLE',
				'ppImage'		=> JURI::root().'plugins/invoicingpayment/authorizenet/authorizenet/authorizenet.jpg')
		);
	
		parent::__construct($subject, $config);
	}
	
	function onInvoicingPaymentDisplay($paymentmethod,$data)
	{	
		if($paymentmethod != $this->ppName) return false;
		
		$app = \JFactory::getApplication();
		$db =\JFactory::getDbo();
		$user = \JFactory::getUser();
		
		$this->baseurl = JURI::base();

		$loginID		= $this->params->get('api_login_id','');
		$transactionKey = $this->params->get('transactionKey','');
		
		if ($this->params->get('sandbox',0) == 1) {
			$url = "https://test.authorize.net/gateway/transact.dll";
			$testMode = "TRUE";
		} else {
			$url = "https://secure.authorize.net/gateway/transact.dll";
			$testMode = "FALSE";
		}
		
		// an invoice is generated using the date and time
		$invoice	= $data->invoicing_invoice_id;
		// a sequence number is randomly generated
		$sequence	= rand(1, 1000);
		// a timestamp is generated
		$timeStamp	= time();
		
		$this->item_name = InvoicingHelperFormat::formatOrderNumber($data);
		
		// The following lines generate the SIM fingerprint.  PHP versions 5.1.2 and
		// newer have the necessary hmac function built in.  For older versions, it
		// will try to use the mhash library.
		if( phpversion() >= '5.1.2' )
			{ $fingerprint = hash_hmac("md5", $loginID . "^" . $sequence . "^" . $timeStamp . "^" . $data->gross_amount . "^", $this->config->transactionKey); }
		else
			{ $fingerprint = bin2hex(mhash(MHASH_MD5, $loginID . "^" . $sequence . "^" . $timeStamp . "^" . $data->gross_amount . "^", $this->config->transactionKey)); }

		include(dirname(__FILE__)."/authorizenet/payment.php");
	}

	function onInvoicingPaymentNotification($paymentmethod, $data) {

		// Check if we're supposed to handle this
		if($paymentmethod != $this->ppName) return false;
		
		InvoicingHelperLog::log("Process Auth.Net");
		
		$app = \JFactory::getApplication();
		$input = \JFactory::getApplication()->input;
		$db =\JFactory::getDBO();
		
		$output = "";
	
		$x_response_code = intval($input->get('x_response_code', '3', 'String'));
		switch($x_response_code)
		{
			case 1: $x_response_text=\JText::_("This transaction has been approved. ");break;
			case 2: $x_response_text=\JText::_("This transaction has been declined. "); break;
			case 3: $x_response_text=\JText::_("There has been an error processing this transaction. ");break;
			case 4: $x_response_text=\JText::_("This transaction is being held for review.");break;
		
			default: $x_response_text = "Other=$x_response_code";
		}
		
		$x_description = $input->get('x_description', '', 'String');
		$x_amount = $input->get('x_amount', '', 'String');
		$x_MD5_Hash =$input->get('x_MD5_Hash', '', 'String');
		$x_trans_id = $input->get('x_trans_id', '', 'String');
		$x_invoice_num = $input->get('x_invoice_num', '', 'String');
		
		//$out = "Transaction: userid=".@$x_cust_id.",date=".date("Y/m/d-h:i:s")."<br/>";
		//$out .= "PackId=@$item_number: ".$x_response_text."<br/>";
		$out = "Transaction: ".$x_description.", price=$x_amount<br/>";
		$out .= print_r($input->get->post->getArray(),true);

		InvoicingHelperLog::log($out);
		
		$amount = ($x_amount ? $x_amount : "0.00");
		$md5_setting = $config->md5hash;
        $hash = strtoupper(md5($md5_setting . $config->api_login_id . $x_trans_id . $amount));
        
        $out = $hash."=".$x_MD5_Hash;
        $this->_save_log($out);

        if ($x_MD5_Hash != '' && $hash == $x_MD5_Hash) {
        		$currency = null;
        		$isNetAmount = true;
	        	if ($this->checkValidity($x_invoice_num,$amount,$isNetAmount,$currency) == true) {
	        		$this->validPayment($x_invoice_num,$paymentmethod);
	        		$url = 'index.php?option=com_invoicing&view=message&layout=complete';
	        	} else {
					$url = 'index.php?option=com_invoicing&view=message&layout=error';
				}
		} else {
			$url = 'index.php?option=com_invoicing&view=message&layout=error';
		}
		
		echo "<strong>".\JText::_('Transaction Status')."</strong><br/>";
		echo $x_response_text;
		?>
		<br/>
		<div align='center'>
		<a href="<?php echo JURI::root().'/'.$url?>"><?php echo \JText::_('Back to merchant website')?></a>
		</div>
		<?php 
		exit();
	}
}