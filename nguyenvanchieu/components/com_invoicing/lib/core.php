<?php
/**
 * @package		Invoicing
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die( 'Restricted access' );

//include_once(JPATH_LIBRARIES.'/fof/include.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/cparams.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/select.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/format.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/mail.php');

if(is_file(JPATH_ROOT.'/libraries/juloalib/Lib.php')){
    include_once(JPATH_ROOT.'/libraries/juloalib/Lib.php');
}

// Merge the language overrides
$paths = array(JPATH_ADMINISTRATOR, JPATH_ROOT);
$jlang = \JFactory::getLanguage();
$jlang->load("com_invoicing", $paths[0], 'en-GB', true);
$jlang->load("com_invoicing", $paths[0], null, true);
$jlang->load("com_invoicing", $paths[1], 'en-GB', true);
$jlang->load("com_invoicing", $paths[1], null, true);

class Invoicing {
	
	public static function getVendors() {
		
	}
	
    public static function formatPrice($price, $currency){
    	if ($currency == null)
    		return $price;
    	
        $number = number_format($price,$currency->number_decimals,$currency->decimal_separator,$currency->thousand_separator);    
            
        if($currency->symbol != ''){
            if ($currency->symbol_position == "after") {
                return $number."&nbsp;".$currency->symbol;
            } else {
                return $currency->symbol."&nbsp;".$number;
            }
        } else {
            $currencyLabel = $currency->code;
            if ($currency->symbol_position == "after") {
                return $number."&nbsp;".$currencyLabel;
            } else {
                return $currencyLabel."&nbsp;".$number;
            }
        }
        
		return "";
    }
    
	public static function getCurrencies($currencyId = 0) {
		if($currencyId == 0)
            return false;
        
        $db = \JFactory::getDbo();
        
        $db->setQuery(" SELECT * FROM #__invoicing_currencies "
				     ." WHERE invoicing_currency_id='$currencyId'");
		$currency = $db->loadObject();
        
        return $currency;
	}
	
	public static function getCurrency($currencyId = 0) {
		if($currencyId == 0)
			return false;
	
		$db = \JFactory::getDbo();
	
		$db->setQuery(" SELECT * FROM #__invoicing_currencies "
				." WHERE invoicing_currency_id='$currencyId'");
				$currency = $db->loadObject();
	
				return $currency;
	}
	
	public static function getTax($taxId = 0) {
		if($taxId == 0)
			return 0;
	
		$db = \JFactory::getDbo();
	
		$db->setQuery(" SELECT taxrate FROM #__invoicing_taxes "
				." WHERE invoicing_tax_id='$taxId'");
		$tax = $db->loadResult();
	
		return $tax;
	}
	
	public static function createInvoice($user_id,$items,$generator,$generator_key,
										 $currency_id=null,$notes=null,$vendor_id=null,
										 $language=null,$status='PENDING') {

		jimport('joomla.application.component.helper');
		$params = \JComponentHelper::getParams('com_invoicing');
											
		$db = \JFactory::getDbo();
		
		$db->setQuery(" SELECT invoicing_invoice_id,order_number FROM #__invoicing_invoices "
				     ." WHERE generator='$generator' AND generator_key='$generator_key' AND status = 'PENDING'");
		$invoice = $db->loadObject();
		if ($invoice != null) {
			$existing_invoice_id = $invoice->invoicing_invoice_id;
			$existing_order_number = $invoice->order_number;
		}else{
            $existing_invoice_id = false;
            $existing_order_number = null;
        }
		if ($existing_invoice_id != false) {
			$db->setQuery( "DELETE FROM #__invoicing_invoice_items WHERE invoice_id = $existing_invoice_id");
			$db->execute();
		}
		
		jimport('joomla.utilities.date');
		$jNow = new \JDate();
		
		$gross_amount = 0;
		$net_amount= 0;
		foreach($items as $key => $item) {
			$item = (object) $item;
			$items[$key] = (object)$items[$key];
			if (isset($item->net_unit_price)) {
				$items[$key]->gross_unit_price = $item->net_unit_price * ((100 + $item->tax) / 100);
			} else if (isset($item->gross_unit_price)) {
				$items[$key]->net_unit_price = $item->gross_unit_price / ((100 + $item->tax) / 100);
			}
				
			$items[$key]->net_amount = $item->quantity * $items[$key]->net_unit_price;
			$items[$key]->gross_amount = $items[$key]->net_amount * (100 + $item->tax) / 100;
			
			$gross_amount += $items[$key]->gross_amount;
			$net_amount += $items[$key]->net_amount;
		}
		$tax_amount = $gross_amount - $net_amount;
		
		$ip = $_SERVER['REMOTE_ADDR'];
		
		if ($language == null) {
			$lang = \JFactory::getLanguage();
			$language = $lang->getTag();
		}
		
		if ($vendor_id == null) {
			$db->setQuery("SELECT * FROM #__invoicing_vendors ORDER BY invoicing_vendor_id ASC LIMIT 0,1");
			$item = $db->loadObject();
			$vendor_id = $item->invoicing_vendor_id;
		}

		if ($currency_id == null) {
			$db->setQuery("SELECT * FROM #__invoicing_currencies ORDER BY invoicing_currency_id ASC LIMIT 0,1");
			$item = $db->loadObject();
			$currency_id = $item->invoicing_currency_id;
		}

		$sql = "SELECT invoicing_user_id FROM #__invoicing_users WHERE user_id = ".(int)$user_id;
		$db->setQuery($sql);
		$invoicing_user_id = $db->loadResult();
		if($invoicing_user_id == null) {
			$db =\JFactory::getDBO();
			$sql = "SELECT * FROM #__users WHERE id = ".(int)$user_id;
			$db->setQuery($sql);
			$user = $db->loadObject();
			$obj = new \stdClass();
			$obj->user_id = $user_id;
			$obj->businessname = $user->username;
			$db->insertObject('#__invoicing_users', $obj);

			$sql = "SELECT invoicing_user_id FROM #__invoicing_users WHERE user_id = ".(int)$user_id;
			$db->setQuery($sql);
			$invoicing_user_id = $db->loadResult();
		} 
	
        if ($existing_order_number != null) {
			$order_number = $existing_order_number;
		} else {
			$order_number = $params->get('order_number_counter', 1);
			//$order_number = InvoicingHelperCparams::getParam('order_number_counter',1);
		}
		
        if(version_compare(JVERSION, '1.6.0', 'ge')) {
            $dateToSql = $jNow->toSql();
        }else{
            $dateToSql = $jNow->toMySql();
        }
        
		$user = \JFactory::getUser();
		$invoice = (object)array(
				"order_number"    => $order_number,
				"user_id"   => $invoicing_user_id,
				"vendor_id" => $vendor_id,
				
				"status" => $status,
				
				"created_on" => $dateToSql,
				"created_by" => $user->id,
				"due_date" => $dateToSql,
				
				"notes" => $notes,
				
				"processor" => '',
				"processor_key" => '',
				
				"gross_amount" => $gross_amount,
				"tax_amount" => $tax_amount,
				"net_amount" => $net_amount,
				"gross_subamount" => $gross_amount,
				"tax_subamount" => $tax_amount,
				"net_subamount" => $net_amount,
				"gross_discount_amount" => 0,
				"net_discount_amount" => 0,
				"tax_discount_amount" => 0,
				"coupon_id" => 0,
				"discount_type" => '', 
        			"discount_value" => 0,

				"currency_id" => $currency_id,
				
				"language" => $language,
				
				"ip_address" => $ip,
				
				"generator" => $generator,
				"generator_key" => $generator_key
		);


		
		if ($existing_invoice_id != false) {
			$invoice->invoicing_invoice_id = $existing_invoice_id;
			$db->updateObject('#__invoicing_invoices', $invoice, 'invoicing_invoice_id');
			$invoice_id = $existing_invoice_id;
		} else {
			$db->insertObject('#__invoicing_invoices', $invoice);
			$invoice_id = $db->insertid();
		}
		if ($existing_order_number == null) {
			$order_number++;
			$db->setQuery("SELECT extension_id,params FROM #__extensions WHERE name='invoicing'");
			$extension = $db->loadObject();
			$setParams = json_decode($extension->params);
			if ($setParams == null) {
				$setParams = new \stdClass();
			}
			
			$setParams->order_number_counter = $order_number;
			$newconfig = new \stdClass();
			$newconfig->extension_id = $extension->extension_id;
			$newconfig->params =  json_encode($setParams);
			$db->updateObject('#__extensions', $newconfig,'extension_id');
			//InvoicingHelperCparams::setParam($order_number,'order_number_counter');
		}
		
		$ordering = 0;
		foreach($items as $item) {		
			if ($item->name == "") {
				$item->name = $item->description;
			} 
			
			$invoiceitem = (object)array(
					"invoice_id" => $invoice_id,
					"name" => $item->name,
					"description" => $item->description,
					"quantity" => $item->quantity,
					
					"net_unit_price" => $item->net_unit_price,
					"tax" => $item->tax,
					"gross_unit_price" => $item->gross_unit_price,
					
					"gross_amount" => $item->gross_amount,
					"net_amount" => $item->net_amount,
					
					"source" => $item->source,
					"source_key" => @$item->source_type,
					
					"ordering" => $ordering);
			$ordering++;
			$db->insertObject('#__invoicing_invoice_items', $invoiceitem, 'invoicing_invoice_item_id');
		}
		
		if ($status == "PENDING") {	
			$db->setQuery("SELECT * FROM #__invoicing_invoices WHERE invoicing_invoice_id = ".(int)$invoice_id);
			$invoice = $db->loadObject();
			//InvoicingHelperMail::sendMailByStatus($invoice);
		}
		return $invoice_id;
	}
	
	function redirectToPayment($invoice_id) {
		$invoiceItemid = (int)InvoicingHelperCparams::getParam('itemid_invoice',0);
        //check if Itemid for the invoice exist, if yes we add it to the url
        if($invoiceItemid > 0) {
            $itemid = "&Itemid=".$invoiceItemid;
        } else {
            $itemid = "";
        }

		$app = \JFactory::getApplication();
        $uri	= \JURI::getInstance();
		$base	= $uri->toString( array('scheme', 'host', 'port'));
        //Le chemin doit petre complet $base.\JRoute car cette fonction peut être
        //appelé depuis une autre page que index.php (voir Buy Button)
		$app->redirect( $base.\JRoute::_('index.php?option=com_invoicing&view=invoice&layout=payment&id='.$invoice_id.$itemid), 200 );
	}
}
