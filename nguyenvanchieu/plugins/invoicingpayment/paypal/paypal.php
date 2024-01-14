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

class plgInvoicingpaymentPaypal extends InvoicingAbstractPaymentPlugin
{
	public function __construct(&$subject, $config = array())
	{
		if(version_compare(JVERSION, '1.6', 'ge')) {
			$defaultimg = JURI::root().'plugins/invoicingpayment/paypal/paypal/paypal.jpg';
		} else {
			$defaultimg = JURI::root().'plugins/invoicingpayment/paypal/paypal.jpg';
		}	
		
		$config = array_merge($config, array(
			'ppName'		=> 'paypal',
			'ppKey'			=> 'PLG_INVOICINGPAYMENT_PAYPAL_TITLE',
			'ppImage'		=> $defaultimg)
				);
		
		parent::__construct($subject, $config);
	}
	
	function onInvoicingPaymentDisplay($paymentmethod,$data)
	{	
		if($paymentmethod != $this->ppName) return false;
		
		$imodel = InvoicingModelInvoices::getInstance('Invoices', 'InvoicingModel');
		$item = $imodel->getItem($data->invoicing_invoice_id);
		$item->processor = 'paypal';
		$imodel->update($item);
		
		$app = \JFactory::getApplication();
		$db = \JFactory::getDbo();
		
		$this->amount = $data->gross_amount;
		//$this->tax = $data->tax_amount;
		include_once(JPATH_ROOT.'/administrator/components/com_invoicing/helpers/format.php');
		$this->item_name = InvoicingHelperFormat::formatOrderNumber($data);
		$this->item_number = $data->invoicing_invoice_id;
		
		$this->config = new \stdClass();
		$this->config->sandbox = $this->params->get('sandbox',0);
		$this->config->sandbox_merchant = $this->params->get('sandbox_merchant','');
		$this->config->merchant = $this->params->get('merchant','');

		$currencyModel = InvoicingModelCurrencies::getInstance('Currencies', 'InvoicingModel');
		$currency = $currencyModel->getItem($data->currency_id);
		$this->currency_code = $currency->code;
		
		$this->baseurl = JURI::base();
		
		ob_start();
		include(dirname(__FILE__)."/paypal/payment.php");
		$html = ob_get_clean();
		return $html;
	}
	
	public function onInvoicingPaymentNotification($paymentmethod, $data)
	{
		// Check if we're supposed to handle this
		if($paymentmethod != $this->ppName) return false;
		
		InvoicingHelperLog::log("Paypal Begin");
	
		// Check IPN data for validity (i.e. protect against fraud attempt)
		$isValid = $this->isValidIPN($data);
		if(!$isValid) {
			InvoicingHelperLog::log('PayPal reports transaction as invalid');
		}
	
		$logData = array();
		
		// Check txn_type; we only accept web_accept transactions with this plugin
		if($isValid) {
			$validTypes = array('web_accept','recurring_payment','subscr_payment');
			$isValid = in_array($data['txn_type'], $validTypes);
			if(!$isValid) {
				InvoicingHelperLog::log("Transaction type ".$data['txn_type']." can't be processed by this payment plugin.");
			} else {
				$recurring = ($data['txn_type'] != 'web_accept');
			}
		}
	
		// Check that receiver_email / receiver_id is what the site owner has configured
		if($isValid) {
			$receiver_email = $data['receiver_email'];
			$receiver_id = $data['receiver_id'];
			$valid_id = $this->getMerchantID();
			$isValid =
			($receiver_email == $valid_id)
			|| (strtolower($receiver_email) == strtolower($receiver_email))
			|| ($receiver_id == $valid_id)
			|| (strtolower($receiver_id) == strtolower($receiver_id))
			;
			if(!$isValid) InvoicingHelperLog::log('Merchant ID does not match receiver_email or receiver_id');
		}
		
		if ($isValid) {
			if ($data['payment_status'] != "Completed") {
				$isValid = false;
			}
			//$isValid = true;
			if(!$isValid) InvoicingHelperLog::log('payment_status='.$data['payment_status'].' not supported');
		}
				
		if($isValid) {
			$id = array_key_exists('item_number', $data) ? (int)$data['item_number'] : -1;
			$amount = floatval($data['mc_gross']);
			$isGrossAmount = true;
			$currency = strtoupper($data['mc_currency']);
			
			if ($this->checkValidity($id,$amount,$isGrossAmount,$currency) == true) {
				$this->validPayment($id,$paymentmethod);
			} else {
				$isValid = false;
			}
		}
		
		// Fraud attempt? Do nothing more!
		if(!$isValid) return false;
	}
	
	/**
	 * Gets the form action URL for the payment
	 */
	private function getPaymentURL()
	{
		$sandbox = $this->params->get('sandbox',0);
		if($sandbox) {
			return 'https://www.sandbox.paypal.com/cgi-bin/webscr';
		} else {
			return 'https://www.paypal.com/cgi-bin/webscr';
		}
	}
	
	/**
	 * Gets the PayPal Merchant ID (usually the email address)
	 */
	private function getMerchantID()
	{
		$sandbox = $this->params->get('sandbox',0);
		if($sandbox) {
			return $this->params->get('sandbox_merchant','');
		} else {
			return $this->params->get('merchant','');
		}
	}
	
	/**
	 * Validates the incoming data against PayPal's IPN to make sure this is not a
	 * fraudelent request.
	 */
	private function isValidIPN($data)
	{
		$sandbox = $this->params->get('sandbox',0);
		$hostname = $sandbox ? 'www.sandbox.paypal.com' : 'www.paypal.com';
	
		$url = 'ssl://'.$hostname;
		$port = 443;
	
		$req = 'cmd=_notify-validate';
		foreach($data as $key => $value) {
			$value = urlencode($value);
			$req .= "&$key=$value";
		}
		$header = '';
		$header .= "POST /cgi-bin/webscr HTTP/1.1\r\n";
		$header .= "Host: $hostname:$port\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "Content-Length: " . strlen($req) . "\r\n";
		$header .= "Connection: Close\r\n\r\n";
	
	
		$fp = fsockopen ($url, $port, $errno, $errstr, 30);
	
		if (!$fp) {
			// HTTP ERROR
			return false;
		} else {
			fputs ($fp, $header . $req);
			while (!feof($fp)) {
				$res = fgets ($fp, 1024);
				if (stristr($res, "VERIFIED")) {
					return true;
				} else if (stristr($res, "INVALID")) {
					return false;
				}
			}
			fclose ($fp);
		}
	}
}
