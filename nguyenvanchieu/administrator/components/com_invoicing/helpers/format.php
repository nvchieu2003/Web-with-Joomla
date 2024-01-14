<?php
/**
 *  @package Invoicing
 *  @copyright Copyright (c)2012 JoomPROD
 *  @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/cparams.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/models/invoices.php');
include_once('load.php');
include_once('select.php');

define("ORDER",1);
define("INVOICE",2);
define("QUOTE",3);

class InvoicingHelperFormat
{	
	/**
	 * 
	 * @param int $id
	 * @param string $format symbol or code
	 * @return string
	 */
	public static function formatCurrency($id,$format="symbol")
	{
		static $currencies;
		
		if (!in_array($format,array("symbol","code"))) {
			$format = "symbol";
		}
		
		if(empty($currencies)) {
			$db = \JFactory::getDbo();
			$db->setQuery("SELECT * FROM #__invoicing_currencies");
			$currenciesList = $db->loadObjectList();
			$currencies = array();
			if(!empty($currenciesList)) foreach($currenciesList as $currency) {
				$currencies[$currency->invoicing_currency_id] = array("symbol"=>$currency->symbol,"code"=>$currency->code);
			}
		}
		
		if(array_key_exists($id, $currencies)) {
			return $currencies[$id][$format];
		} else {
			return '&mdash;&mdash;&mdash;';
		}
	}

    public static function formatMonths($name,$selectvalue=null) {
		$months = self::returnMonthsName(1,12);
		echo '<select name="'.$name.'">',"\n";
		$selected ='';
		for($i=1; $i<=12; $i++)
		{ 
			if ($i == $selectvalue)
				$selected = ' selected="selected"';
			 echo "\t",'<option value="', $i ,'"', $selected ,'>', $months[$i] ,'</option>',"\n";
			$selected = ''; 
		}
		 echo '</select>',"\n"; 
	}

	public static function returnMonthsName ($start,$end) {
		for ($i=$start;$i<=$end;$i++) {
			$i = (int)$i;
			$names[$i] = date("F",mktime(0,0,0,$i,22,2013));
		}
		return $names;
	}
	
	public static function formatYears($name,$selectvalue=null) {
		
		$db = \JFactory::getDbo();
		$db->setQuery("SELECT * FROM #__invoicing_invoices");
		$invoices = $db->loadObjectList();
		
		if (isset($invoices[0])) {
			$refdate = date($invoices[0]->created_on);
			foreach ($invoices as $invoice) {
				if ($invoice->created_on < $refdate)
					$refdate = date($invoice->created_on);
			}
			$year = date('Y',strtotime($refdate));
			$actualyear = date('Y',strtotime(date('Y-m-d')));
		}
		else {
			$actualyear = date("Y");
			$year = date("Y",strtotime("-1 year"));
		}
			$selected = '';
			echo '<select name="'.$name.'">',"\n";
			for($i=$year; $i<=$actualyear; $i++)
			{
				if ($i == $selectvalue)
					$selected = ' selected="selected"';
				echo "\t",'<option value="', $i ,'"', $selected ,'>', $i,'</option>',"\n";
				$selected = '';
			}
			echo '</select>',"\n";
		
	}
	


	public static function formatCoupon($id,$format="code") {
		static $coupons;
		
		if (!in_array($format,array("code","title"))) {
			$format = "code";
		}
		
		if(empty($coupons)) {
			$db = \JFactory::getDbo();
			$db->setQuery("SELECT * FROM #__invoicing_coupons");
			$couponsList = $db->loadObjectList();
			$coupons = array();
			if(!empty($couponsList)) foreach($couponsList as $coupon) {
				$coupons[$coupon->invoicing_coupon_id] = array("code"=>$coupon->code,"title"=>$coupon->title);
			}
		}
		
		if(array_key_exists($id, $coupons)) {
			return $coupons[$id][$format];
		} else {
			return '&mdash;&mdash;&mdash;';
		}
		
	}
		
	public static function formatUser($id,$format="businessname") {
		static $users;
		
 		if (!in_array($format,array("businessname"))) {
			$format = "businessname";
		} 
		
		if(empty($users)) {
			$db = \JFactory::getDbo();
			$db->setQuery("SELECT * FROM #__invoicing_users");
			$usersList = $db->loadObjectList();
			$users = array();
			if(!empty($usersList)) foreach($usersList as $user) {
				$users[$user->invoicing_user_id] = array("businessname"=>$user->businessname." /".@$user->username);
			}
		}

		if(array_key_exists($id, $users)) {
			return $users[$id][$format];
		} else {
			return '&mdash;&mdash;&mdash;';
		}
		
	}
	
	public static function formatProcessor($processor_name)
	{
		static $processors = null;
		if ($processors == null) {
			jimport('joomla.plugin.helper');
			\JPluginHelper::importPlugin('invoicingpayment');
			$app = \JFactory::getApplication();
			$processors = $app->triggerEvent('onInvoicingPaymentGetIdentity');
		}
		
		foreach($processors as $processor)
		{
			if ($processor_name == $processor->name) {
				return \JText::_($processor->title) ;
			}
		}
		return $processor_name;
	}
	
	public static function formatVendor($id,$format="contact_name") {
		static $vendors;

		if (!in_array($format,array("contact_name"))) {
			$format = "contact_name";
		}
		if(empty($vendors)) {
			$db = \JFactory::getDbo();
			$db->setQuery("SELECT * FROM #__invoicing_vendors");
			$vendorsList = $db->loadObjectList();
			$vendors = array();
			if(!empty($vendorsList)) foreach($vendorsList as $vendor) {
				$vendors[$vendor->invoicing_vendor_id] = array("contact_name"=>$vendor->contact_name.'('.$vendor->company_name.')');

			}
		}

		if(array_key_exists($id, $vendors)) {
			return $vendors[$id][$format];
		} else {
			return '&mdash;&mdash;&mdash;';
		}
		
	}
	
	public static function formatInvoiceStatus($status) 
	{
		return \JText::_('INVOICING_INVOICE_'.$status);
	}
	
	public static function formatNumber($invoice) {
		if(@$invoice->status == "PAID") {
			return self::formatInvoiceNumber($invoice);
		} else {
			return self::formatOrderNumber($invoice);
		}
	}
	
	public static function formatInvoiceNumber($invoice)
	{
		$invoice_number = '';
		if(@$invoice->status == "PAID") {
			$invoice_number_format = InvoicingHelperCParams::getParam('invoice_number_format','{invoice_number}');
			$invoice_number = str_replace('{invoice_number}',$invoice->invoice_number,$invoice_number_format);
			$invoice_number = strftime($invoice_number,strtotime($invoice->created_on));
		}
		return $invoice_number;
	}
	
	public static function formatOrderNumber($invoice)
	{
		jimport('joomla.application.component.helper');
		$params = \JComponentHelper::getParams('com_invoicing');
		$order_number_format = $params->get('order_number_format', '{order_number}');//InvoicingHelperCParams::getParam('order_number_format','{order_number}');
		@$order_number = str_replace('{order_number}',$invoice->order_number,$order_number_format);
		@$order_number = strftime($order_number,strtotime($invoice->created_on));
		return $order_number;
	}
    
    public static function formatQuoteNumber($invoice)
	{
		$quote_number_format = InvoicingHelperCParams::getParam('quote_number_format','{quote_number}');
		@$quote_number = str_replace('{quote_number}',$invoice->quote_number,$quote_number_format);
		@$quote_number = strftime($quote_number,strtotime($invoice->created_on));
		return $quote_number;
	}
	
	public static function formatCurrencyStatus($status)
	{
		return ($status > 0 ? \JText::_('JENABLED') : \JText::_('JDISABLED'));
	}
	
	public static function formatYesNo($status)
	{
		return ($status > 0 ? \JText::_('JYES') : \JText::_('JNO'));
	}
	
	/* Return the substring of date before the separator in a string */
	public static function getConfigFormatDate()
	{
		jimport('joomla.application.component.helper');
		$params = \JComponentHelper::getParams('com_invoicing');
		$format =  $params->get('date_format', '%Y-%m-%d');//InvoicingHelperCparams::getParam('date_format','%Y-%m-%d');
		return $format;
	}
	
	public static function formattedDate($date) {
		
		$format = self::getConfigFormatDate();
		return strftime($format, strtotime($date));
	}
	
	
	public static function formatPrice($price,$currency_id) 
	{
		static $currencies;
		
		if(empty($currencies)) {
			$db = \JFactory::getDbo();
			$db->setQuery("SELECT * FROM #__invoicing_currencies");
			$currenciesList = $db->loadObjectList();
			$currencies = array();
			if(!empty($currenciesList)) { 
				foreach($currenciesList as $currency) {
					$currencies[$currency->invoicing_currency_id] = $currency;
				}
			}
		}
		
		$currency = @$currencies[$currency_id];
		if ($currency) {
			$number = number_format($price,$currency->number_decimals,$currency->decimal_separator,$currency->thousand_separator);
			if ($currency->symbol_position == "after") {
				return $number."&nbsp;".$currency->symbol;
			} else {
				return $currency->symbol."&nbsp;".$number;
			}	
		}
		return "";
	}
	
	public static function displayTags() {
	$substitutions = array(
					"{vendor_contact_name}",
					"{vendor_company_name}",
					"{vendor_company_email}",
					"{vendor_company_url}",
					"{vendor_company_phone}",
					"{vendor_address1}",
					"{vendor_address2}",
					"{vendor_notes}",
					"{vendor_city}",
					"{vendor_zip}",
					"{vendor_country}",
					"{vendor_logo}",
					"{url_site}",
					
					"{customer_id}",					
					"{customer_name}",
			        "{customer_firstname}",
					"{customer_lastname}",
					"{customer_businessname}",
					"{customer_mobile}",
					"{customer_landline}",
					"{customer_occupation}",
					"{customer_address1}",
					"{customer_address2}",
					"{customer_notes}",
					"{customer_city}",
					"{customer_country}",					
					"{customer_email}",
					"{customer_zip}",
					
					"{url_invoice}",
					"{url_invoices}",
					"{url_payment}",
					"{invoice_order_number}",
					"{invoice_number}",
					"{invoice_subject}",
					"{invoice_date}",
					"{invoice_due_date}",
					"{invoice_status}",
					"{invoice_net_discount_amount}",
					"{invoice_gross_discount_amount}",
					"{invoice_tax_discount_amount}",
					"{invoice_gross_amount}",
					"{invoice_tax_amount}",
					"{invoice_net_amount}",
					"{invoice_gross_subamount}",
					"{invoice_tax_subamount}",
					"{invoice_net_subamount}",
					"{invoice_notes}",
					"{invoice_processor}",
					"{invoice_external_ref}",
					"{invoice_coupon}",
					"{invoice_external}",
					
					'{item_name}',
					"{item_description}",
					'{item_ref}',
					"{item_gross_amount}",
					"{item_gross_unit_price}",
					"{item_net_amount}",
					"{item_net_unit_price}",
					"{item_tax}",
					"{item_quantity}",
			
					"{quote_number}",
					"{quote_subject}",
					"{quote_date}",
					"{quote_due_date}",
					"{quote_net_discount_amount}",
					"{quote_gross_discount_amount}",
					"{quote_tax_discount_amount}",
					"{quote_gross_amount}",
					"{quote_tax_amount}",
					"{quote_net_amount}",
					"{quote_gross_subamount}",
					"{quote_tax_subamount}",
					"{quote_net_subamount}",
					"{quote_notes}",
					"{quote_processor}",
					"{quote_external_ref}",
					"{quote_coupon}",
					"{quote_coupon_title}",
					"{quote_external}",	
					
					"{coupon_start}",
					"{coupon_end}",
					"{item_start}",
					"{item_end}");
					
		foreach ($substitutions as $tag)
			echo $tag . "<br/>";
			
	}
	
	public static function replaceTags($body,$invoice) {
		static $substitutions;
		$substitutions = array();

		$invoiceModel = InvoicingModelInvoices::getInstance('Invoices', 'InvoicingModel');
		$invoice = $invoiceModel->getItem($invoice->invoicing_invoice_id);

		if (!isset($substitutions[$invoice->invoicing_invoice_id])) {
			if (($invoice->status == 'PAID')||($invoice->status == 'CANCELLED')) {
				$number = InvoicingHelperFormat::formatInvoiceNumber($invoice);
			} else {
				$number = InvoicingHelperFormat::formatOrderNumber($invoice);
			}
			if ((isset($invoice->coupon))&&($invoice->discount_value != 0)) {
				if ($invoice->discount_type == "value")
					$invoice->coupon = \JText::_('INVOICING_CUSTOM_DISCOUNT')." ".InvoicingHelperFormat::formatPrice($invoice->discount_value,$invoice->currency_id);
				else
					$invoice->coupon = \JText::_('INVOICING_CUSTOM_DISCOUNT')." ".$invoice->discount_value."%";
			}

			if($invoice->vendor->filename == '') {
				$invoice->vendor->filename = "blank.png";
			}
			
			if (($invoice->buyer->firstname != null)||($invoice->buyer->lastname != null)) {
				$name = $invoice->buyer->firstname." ".$invoice->buyer->lastname;
			} else {
				$name = $invoice->buyer->businessname;
			}
            
			$substitutions[$invoice->invoicing_invoice_id] = array(
					"{vendor_contact_name}" => $invoice->vendor->contact_name,
					"{vendor_company_name}" => $invoice->vendor->company_name,
					"{vendor_company_email}" => $invoice->vendor->company_email,
					"{vendor_company_url}" => $invoice->vendor->company_url,
					"{vendor_company_phone}" => $invoice->vendor->company_phone,
					"{vendor_address1}" => $invoice->vendor->address1,
					"{vendor_address2}" => $invoice->vendor->address2,
					"{vendor_notes}" => $invoice->vendor->notes,
					"{vendor_city}" => $invoice->vendor->city,
					"{vendor_zip}" => $invoice->vendor->zip,
					"{vendor_country}" => InvoicingHelperSelect::formatCountry($invoice->vendor->country,""),
					"{vendor_logo}" => \JURI::root()."/media/com_invoicing/images/vendor/".$invoice->vendor->filename,
					"{url_site}" => \JURI::root(),
					
					"{customer_id}" => $invoice->buyer->invoicing_user_id,					
					"{customer_name}" => $name,
					"{customer_firstname}" => $invoice->buyer->firstname,
					"{customer_lastname}" => $invoice->buyer->lastname,
					"{customer_businessname}" => $invoice->buyer->businessname,
					"{customer_mobile}" => $invoice->buyer->mobile,
					"{customer_landline}" => $invoice->buyer->landline,
					"{customer_occupation}" => $invoice->buyer->occupation,
					"{customer_address1}" => $invoice->buyer->address1,
					"{customer_address2}" => $invoice->buyer->address2,
					"{customer_notes}" => $invoice->buyer->notes,
					"{customer_city}" => $invoice->buyer->city,
					"{customer_country}" => InvoicingHelperSelect::formatCountry($invoice->buyer->country,""),					
					"{customer_email}"=>@$invoice->buyer->email,
					"{customer_mail}"=>@$invoice->buyer->email,
					"{customer_zip}"=>$invoice->buyer->zip,
					
					"{url_invoice}" => \JURI::root()."index.php?option=com_invoicing&view=invoice&id=".$invoice->invoicing_invoice_id,
					"{url_invoices}" => \JURI::root()."index.php?option=com_invoicing&view=invoices",
					"{url_payment}" => \JURI::root()."index.php?option=com_invoicing&view=invoice&id=".$invoice->invoicing_invoice_id."&layout=payment&key=".self::key($invoice),
					"{invoice_order_number}" => InvoicingHelperFormat::formatOrderNumber($invoice),
					"{invoice_invoice_number}" => InvoicingHelperFormat::formatInvoiceNumber($invoice),
					"{invoice_number}" => $number,
					"{invoice_date}" => InvoicingHelperFormat::formattedDate($invoice->created_on),
					"{invoice_due_date}" => InvoicingHelperFormat::formattedDate($invoice->due_date),
					"{invoice_status}" =>  InvoicingHelperFormat::formatInvoiceStatus($invoice->status),
					"{invoice_net_discount_amount}" => InvoicingHelperFormat::formatPrice($invoice->net_discount_amount,$invoice->currency_id),
					"{invoice_gross_discount_amount}" => InvoicingHelperFormat::formatPrice($invoice->gross_discount_amount,$invoice->currency_id),
					"{invoice_tax_discount_amount}" => InvoicingHelperFormat::formatPrice($invoice->tax_discount_amount,$invoice->currency_id),
					"{invoice_gross_amount}" => InvoicingHelperFormat::formatPrice($invoice->gross_amount,$invoice->currency_id),
					"{invoice_tax_amount}" => InvoicingHelperFormat::formatPrice($invoice->tax_amount,$invoice->currency_id),
					"{invoice_net_amount}" => InvoicingHelperFormat::formatPrice($invoice->net_amount,$invoice->currency_id),
					"{invoice_gross_subamount}" => InvoicingHelperFormat::formatPrice($invoice->gross_subamount,$invoice->currency_id),
					"{invoice_tax_subamount}" => InvoicingHelperFormat::formatPrice($invoice->tax_subamount,$invoice->currency_id),
					"{invoice_net_subamount}" => InvoicingHelperFormat::formatPrice($invoice->net_subamount,$invoice->currency_id),
					"{invoice_notes}" => $invoice->notes,
					"{invoice_processor}" => InvoicingHelperFormat::formatProcessor($invoice->processor),
					"{invoice_external_ref}" => $invoice->generator_key,
					"{invoice_coupon}" => @$invoice->coupon,
					"{invoice_coupon_title}" => @$invoice->coupon_title,
					"{invoice_external}" => "",
					"{invoice_subject}" => $invoice->subject);
		}
		
		$substitution = $substitutions[$invoice->invoicing_invoice_id];
		
		if (isset($invoice->coupon)) {
			$regex_coupon    = '/{coupon_start}.*{coupon_end}/s';
			if (preg_match( $regex_coupon, $body, $matches)){
				$textToReplace = $matches[0];
				$body =  str_replace($textToReplace,"",$body);
			}
		} else {
			$body =  str_replace(array("{coupon_start}","{coupon_end}"),"",$body);
		}
	
		$regex_item    = '/{item_start}(.*){item_end}/s';
	
		if (preg_match( $regex_item, $body, $matches)){
			$itemHTML =  $matches[1];
			$textToReplace = $matches[0];
	
	
			$newItemHTML = "";
			foreach ($invoice->items as $item) {
				$substitutionItemRules = array (
						'{item_quantity}' => $item->quantity,
						'{item_name}' => $item->name,
						'{item_description}' => $item->description,
						'{item_ref}' => @$item->source_key,
						'{item_net_unit_price}' => InvoicingHelperFormat::formatPrice($item->net_unit_price,$invoice->currency_id),
						'{item_gross_unit_price}' => InvoicingHelperFormat::formatPrice($item->gross_unit_price,$invoice->currency_id),
						'{item_tax}' => $item->tax."%",
						'{item_net_amount}' => InvoicingHelperFormat::formatPrice($item->net_amount,$invoice->currency_id),
						'{item_gross_amount}' => InvoicingHelperFormat::formatPrice($item->gross_amount,$invoice->currency_id));
	
				$newItemHTML .= str_replace(array_keys($substitutionItemRules),array_values($substitutionItemRules),$itemHTML);
			}
			$body =  str_replace($textToReplace,$newItemHTML,$body);
		}
		
		$content = str_replace(array_keys($substitution),array_values($substitution), $body);
		
		$content = self::replaceBrackets($content);
		
		return $content;
	}
	
	
	public static function replaceTagsQuotes($body,$quote) {
		static $substitutions;
		$substitutions = array();
		
		if (!isset($substitutions[$quote->invoicing_quote_id])) {
				$number = InvoicingHelperFormat::formatQuoteNumber($quote);
			if ((isset($quote->coupon))&&($quote->discount_value != 0)) {
				if ($quote->discount_type == "value")
					$quote->coupon = \JText::_('INVOICING_CUSTOM_DISCOUNT')." ".InvoicingHelperFormat::formatPrice($quote->discount_value,$quote->currency_id);
				else
					$quote->coupon = \JText::_('INVOICING_CUSTOM_DISCOUNT')." ".$quote->discount_value."%";
			}
			
            if($quote->vendor->filename == '') {
            	$quote->vendor->filename = "blank.png";
            }
            
            if (($quote->buyer->firstname != null)||($quote->buyer->lastname != null)) {
            	$name = $quote->buyer->firstname." ".$quote->buyer->lastname;
            } else {
            	$name = $quote->buyer->businessname;
            }
                  
			$substitutions[$quote->invoicing_quote_id] = array(
					"{vendor_contact_name}" => $quote->vendor->contact_name,
					"{vendor_company_name}" => $quote->vendor->company_name,
					"{vendor_company_email}" => $quote->vendor->company_email,
					"{vendor_company_url}" => $quote->vendor->company_url,
					"{vendor_company_phone}" => $quote->vendor->company_phone,
					"{vendor_address1}" => $quote->vendor->address1,
					"{vendor_address2}" => $quote->vendor->address2,
					"{vendor_notes}" => $quote->vendor->notes,
					"{vendor_city}" => $quote->vendor->city,
					"{vendor_zip}" => $quote->vendor->zip,
					"{vendor_country}" => InvoicingHelperSelect::formatCountry($quote->vendor->country),
					"{vendor_logo}" => JURI::root()."/media/com_invoicing/images/vendor/".$quote->vendor->filename,
					"{url_site}" => JURI::root(),
					
					"{customer_id}" => $quote->buyer->invoicing_user_id,					
					"{customer_name}" => $name,
					"{customer_firstname}" => $quote->buyer->firstname,
					"{customer_lastname}" => $quote->buyer->lastname,
					"{customer_businessname}" => $quote->buyer->businessname,
					"{customer_mobile}" => $quote->buyer->mobile,
					"{customer_landline}" => $quote->buyer->landline,
					"{customer_occupation}" => $quote->buyer->occupation,
					"{customer_address1}" => $quote->buyer->address1,
					"{customer_address2}" => $quote->buyer->address2,
					"{customer_notes}" => $quote->buyer->notes,
					"{customer_city}" => $quote->buyer->city,
					"{customer_country}" => InvoicingHelperSelect::formatCountry($quote->buyer->country),					
					"{customer_email}"=>@$quote->buyer->email,
					"{customer_mail}"=>@$quote->buyer->email,
					"{customer_zip}"=>$quote->buyer->zip,
					
					"{url_quote}" => JURI::root()."index.php?option=com_invoicing&view=quote&id=".$quote->invoicing_quote_id,
					"{url_quotes}" => JURI::root()."index.php?option=com_invoicing&view=quotes",
					"{url_payment}" => JURI::root()."index.php?option=com_invoicing&view=quote&id=".$quote->invoicing_quote_id."&layout=payment&key=".self::keyQuote($quote),
					"{quote_number}" => InvoicingHelperFormat::formatQuoteNumber($quote),
					"{quote_date}" => InvoicingHelperFormat::formattedDate($quote->created_on),
					"{quote_due_date}" => InvoicingHelperFormat::formattedDate($quote->due_date),
					"{quote_net_discount_amount}" => InvoicingHelperFormat::formatPrice($quote->net_discount_amount,$quote->currency_id),
					"{quote_gross_discount_amount}" => InvoicingHelperFormat::formatPrice($quote->gross_discount_amount,$quote->currency_id),
					"{quote_tax_discount_amount}" => InvoicingHelperFormat::formatPrice($quote->tax_discount_amount,$quote->currency_id),
					"{quote_gross_amount}" => InvoicingHelperFormat::formatPrice($quote->gross_amount,$quote->currency_id),
					"{quote_tax_amount}" => InvoicingHelperFormat::formatPrice($quote->tax_amount,$quote->currency_id),
					"{quote_net_amount}" => InvoicingHelperFormat::formatPrice($quote->net_amount,$quote->currency_id),
					"{quote_gross_subamount}" => InvoicingHelperFormat::formatPrice($quote->gross_subamount,$quote->currency_id),
					"{quote_tax_subamount}" => InvoicingHelperFormat::formatPrice($quote->tax_subamount,$quote->currency_id),
					"{quote_net_subamount}" => InvoicingHelperFormat::formatPrice($quote->net_subamount,$quote->currency_id),
					"{quote_notes}" => $quote->notes,
					"{quote_processor}" => InvoicingHelperFormat::formatProcessor($quote->processor),
					"{quote_external_ref}" => $quote->generator_key,
					"{quote_coupon}" => @$quote->coupon,
					"{quote_coupon_title}" => @$quote->coupon_title,
					"{quote_external}" => "",
					"{quote_subject}" => $quote->subject
					);
		}
		
		$substitution = $substitutions[$quote->invoicing_quote_id];
		
		if (isset($quote->coupon)) {
			$regex_coupon    = '/{coupon_start}.*{coupon_end}/s';
			if (preg_match( $regex_coupon, $body, $matches)){
				$textToReplace = $matches[0];
				$body =  str_replace($textToReplace,"",$body);
			}
		} else {
			$body =  str_replace(array("{coupon_start}","{coupon_end}"),"",$body);
		}
	
		$regex_item    = '/{item_start}(.*){item_end}/s';
	
		if (preg_match( $regex_item, $body, $matches)){
			$itemHTML =  $matches[1];
			$textToReplace = $matches[0];
	
	
			$newItemHTML = "";
			foreach ($quote->items as $item) {
				$substitutionItemRules = array (
						'{item_quantity}' => $item->quantity,
						'{item_name}' => $item->name,
						'{item_description}' => $item->description,
						'{item_ref}' => @$item->source_key,
						'{item_net_unit_price}' => InvoicingHelperFormat::formatPrice($item->net_unit_price,$quote->currency_id),
						'{item_gross_unit_price}' => InvoicingHelperFormat::formatPrice($item->gross_unit_price,$quote->currency_id),
						'{item_tax}' => $item->tax."%",
						'{item_net_amount}' => InvoicingHelperFormat::formatPrice($item->net_amount,$quote->currency_id),
						'{item_gross_amount}' => InvoicingHelperFormat::formatPrice($item->gross_amount,$quote->currency_id));
	
				$newItemHTML .= str_replace(array_keys($substitutionItemRules),array_values($substitutionItemRules),$itemHTML);
			}
			$body =  str_replace($textToReplace,$newItemHTML,$body);
		}
		
		$content = str_replace(array_keys($substitution),array_values($substitution), $body);
		
		$content = self::replaceBrackets($content);
		
		return $content;
	}
	
	public static function replaceBrackets($text2translate) {
		$regex_item    = '#\[([\w/]+)\]#';
        
		if (preg_match_all( $regex_item, $text2translate, $matches)){
			foreach($matches[0] as $key => $textToReplace) {
				$define =  $matches[1][$key];
				$text2translate = str_replace($textToReplace,\JText::_($define),$text2translate);
			}
		}
			
		return $text2translate;
	}
	
	/**
	 * Returns HTML code of generated InvoiceView to build pdf view
	 */
	public static function formatInvoiceHTML($invoice) {
			$lang = \JFactory::getLanguage();
			$tag = $lang->getTag();
			if ($tag != $invoice->language) {
				InvoicingHelperLoad::loadLanguage($invoice->language);
			}
			//$body = InvoicingHelperCparams::getParam('invoicehtmlformat','');
			$tModel = InvoicingModelTemplates::getInstance('Templates', 'InvoicingModel');
			$body = $tModel->getItem(INVOICE)
			->htmlcontent;
		
            $return = self::replaceTags($body,$invoice);
            
		if ($tag != $invoice->language) {
			InvoicingHelperLoad::loadLanguage($tag);
		}

	return($return);
	
	}	
	
	public static function formatInvoicePDF($invoice) {
			InvoicingHelperLoad::loadLanguage($invoice->language);
			$db = \JFactory::getDbo();
			$db->setQuery("SELECT * FROM #__invoicing_templates WHERE invoicing_template_id = ".INVOICE);
			$body = $db->loadObject()->pdfcontent;

	return(self::replaceTags($body,$invoice));
	
	}	
	
	public static function formatOrderHTML($invoice) {
	$lang = \JFactory::getLanguage();
			$tag = $lang->getTag();
			if ($tag != $invoice->language) {
				InvoicingHelperLoad::loadLanguage($invoice->language);
			}
			$db = \JFactory::getDbo();
			$db->setQuery("SELECT * FROM #__invoicing_templates WHERE invoicing_template_id = ".ORDER);
			$body = $db->loadObject()->htmlcontent;
		
      $return = self::replaceTags($body,$invoice);
            
			//$body = InvoicingHelperCparams::getParam('orderhtmlformat','');
			if ($tag != $invoice->language) {
				InvoicingHelperLoad::loadLanguage($tag);
			}

	return($return);
	
	}	
	
	public static function formatOrderPDF($invoice) {
		InvoicingHelperLoad::loadLanguage($invoice->language);
		$db = \JFactory::getDbo();
		$db->setQuery("SELECT * FROM #__invoicing_templates WHERE invoicing_template_id = ".ORDER);
		$body = $db->loadObject()->pdfcontent;
		//$body = InvoicingHelperCparams::getParam('orderpdfformat','');


		return(self::replaceTags($body,$invoice));
	
	}
    
    /**
	 * Returns HTML code of generated InvoiceView to build pdf view
	 */
	public static function formatQuoteHTML($quote) {
			$lang = \JFactory::getLanguage();
			$tag = $lang->getTag();
			if ($tag != $quote->language) {
				InvoicingHelperLoad::loadLanguage($quote->language);
			}
			$db = \JFactory::getDbo();
			$db->setQuery("SELECT * FROM #__invoicing_templates WHERE invoicing_template_id = ".QUOTE);
			$body = $db->loadObject()->htmlcontent;
			$return = self::replaceTagsQuotes($body,$quote);
            
		if ($tag != $quote->language) {
			InvoicingHelperLoad::loadLanguage($tag);
		}
	

		return($return);
	
	}	
	
	public static function formatQuotePDF($invoice) {
			InvoicingHelperLoad::loadLanguage($invoice->language);
			$db = \JFactory::getDbo();
			$db->setQuery("SELECT * FROM #__invoicing_templates WHERE invoicing_template_id = ".QUOTE);
			$body = $db->loadObject()->pdfcontent;
			return(self::replaceTagsQuotes($body,$invoice));
	}	

	/**
     *  Generate a key to be able to protect payment without account identification
     */
	static function key($invoice) {
		return md5($invoice->buyer->invoicing_user_id.$invoice->invoicing_invoice_id.$invoice->order_number);
	}
	
	static function keyQuote($quote) {
		return md5($quote->buyer->invoicing_user_id.$quote->invoicing_quote_id.$quote->quote_number);
	}
}
