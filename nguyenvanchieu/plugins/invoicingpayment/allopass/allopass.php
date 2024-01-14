<?php
/**
 * @package		Invoicing
 * @copyright	Copyright (C) 2010-2011 JoomPROD.com. All rights reserved.
 * @license GNU General Public License version 3, or later
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

require_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/assets/paymentplugin.php');
require_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/log.php');

class plgInvoicingpaymentAllopass extends InvoicingAbstractPaymentPlugin
{
	public function __construct(&$subject, $config = array())
	{
		if(version_compare(JVERSION, '1.6', 'ge')) {
			$defaultimg = JURI::root().'plugins/invoicingpayment/allopass/allopass/allopass.jpg';
		} else {
			$defaultimg = JURI::root().'plugins/invoicingpayment/allopass/allopass.jpg';
		}	
		
		$config = array_merge($config, array(
			'ppName'		=> 'allopass',
			'ppKey'			=> 'PLG_INVOICINGPAYMENT_ALLOPASS_TITLE',
			'ppImage'		=> $defaultimg)
				);
		
		parent::__construct($subject, $config);
	}
	
	function onInvoicingPaymentDisplay($paymentmethod,$data)
	{	
		if($paymentmethod != $this->ppName) return false;
		
		$imodel = InvoicingModelInvoices::getInstance('Invoices', 'InvoicingModel');
		$item = $imodel->getItem($data->invoicing_invoice_id);
		$item->processor = 'allopass';
		$imodel->update($item);
		
		$match = $this->params->get('productmatch','');
		$matches = $this->parseMatching($match);
		
		if (!isset($matches[$item->gross_amount])) {
			echo "Error: no allopass product for this gross amount";exit();
		}
		
		$this->product_id = $matches[$item->gross_amount]['product_id'];
		
		$this->site_id = $this->params->get('siteid',0);
		
		$this->merchant_transaction_id = $item->invoicing_invoice_id;
		
		$this->lang = strtolower(substr(\JFactory::getLanguage()->getTag(), 0, 2));
		
		$this->baseurl = JURI::base();
		
		ob_start();
		include(dirname(__FILE__)."/allopass/payment.php");
		$html = ob_get_clean();
		return $html;
	}
	
	private function parseMatching($rawData)
	{
		if(empty($rawData)) return array();
	
		$ret = array();
	
		// Just in case something funky happened...
		$rawData = str_replace("\\n", "\n", $rawData);
		$rawData = str_replace("\r", "\n", $rawData);
		$rawData = str_replace("\n\n", "\n", $rawData);
	
		$lines = explode("\n", $rawData);
	
		foreach($lines as $line) {
			$line = trim($line);
			$parts = explode('=', $line, 2);
			if(count($parts) != 2) continue;
	
			$price = trim($parts[0]);
	
			$rawAlloPass = $parts[1];
			if(stristr($parts[1], ':'))
			{
				// Legacy parameter handling
				$alloPass = explode(':', $rawAlloPass);
			}
			else
			{
				$alloPass = explode('/', $rawAlloPass);
			}
			if(empty($alloPass)) continue;
			if(count($alloPass) < 2) continue;
	
			$siteId = trim($alloPass[0]);
			if (empty($siteId))
			{
				continue;
			}
	
			$productId = trim($alloPass[1]);
			if (empty($productId))
			{
				continue;
			}
	
			$other_id = trim($alloPass[2]);
	
			$pricePoint = array(
					'site_id'		=> $siteId,
					'product_id'	=> $productId,
					'other_id'		=> $other_id
			);
	
			$ret[$price] = $pricePoint;
		}
	
		return $ret;
	}
	
	public function onInvoicingPaymentNotification($paymentmethod, $data)
	{
		// Check if we're supposed to handle this
		if($paymentmethod != $this->ppName) return false;
		
		InvoicingHelperLog::log("Allopass Begin");
		
		$RECALL=$data["RECALL"];
		$id = $data["merchant_transaction_id"];
		$imodel = InvoicingModelInvoices::getInstance('Invoices', 'InvoicingModel');
		$invoice = $imodel->getItem($id);
		
		$isValid = true;
		
		$isValid = $this->isValidIPN($data);
		
		if ($data['action'] != 'success' && $data['action'] != 'payment-confirm') {
			InvoicingHelperLog::log("Not Supported Action : ".$data['action']);
			$isValid = false;
		}
		
		if ($isValid) {
			$match = $this->params->get('productmatch','');
			$matches = $this->parseMatching($match);
			$alloPass = @$matches[$invoice->gross_amount];
			if (!isset($alloPass)) {
				$isValid = false;
				InvoicingHelperLog::log("Invalid Amount : ".$invoice->gross_amount);
			}
			
			if ($isValid) { 
				$auth = implode('/', array_values($alloPass));
			
				$recall = urlencode($data['code']);
				$auth = urlencode($auth);
				
				InvoicingHelperLog::log("http://www.allopass.com/check/vf.php4?CODE=".$recall."&AUTH=$auth");
				$r=@file("http://www.allopass.com/check/vf.php4?CODE=".$recall."&AUTH=$auth");
					
				if ( substr($r[0],0,2) != "OK" )
				{
					$isValid = false;
					InvoicingHelperLog::log("Invalid Allopass check return : ".substr($r[0],0,2));
				} else {
					InvoicingHelperLog::log("Check OK");
				}
			}
		}
			
		if ($isValid) {		
			if ($this->checkValidity($id,$invoice->gross_amount,true) == true) {
				$this->validPayment($id,$paymentmethod);
			} else {
				$isValid = false;
			}
		}
		
		// Fraud attempt? Do nothing more!
		if(!$isValid) return false;
	}
	
	/**
	 * Validates the incoming data.
	 */
	private function isValidIPN($data)
	{
		$secretKey = $this->params->get('skey','');
	
		$apiHash = 'sha1';
		if(!empty($data['api_hash'])) {
			$apiHash = $data['api_hash'];
		}
		$apiSig = $data['api_sig'];
	
		$ignore = array('api_sig', 'Itemid', 'option', 'view', 'method', 'task', 'lang');
	
		ksort($data);
		$string2compute = '';
		foreach($data as $name => $val) {
			if (in_array($name, $ignore))
			{
				continue;
			}
	
			$string2compute .= $name . $val;
		}
	
		if($apiHash == 'sha1')
		{
			$hash = sha1($string2compute . $secretKey);
		}
		elseif($apiHash == 'md5')
		{
			$hash = md5($string2compute . $secretKey);
		}
		else
		{
			$hash = '';
		}
		InvoicingHelperLog::log("$hash == $apiSig");
	
		return $hash == $apiSig;
	}
}	
