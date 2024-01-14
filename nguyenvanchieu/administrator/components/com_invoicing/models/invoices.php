<?php
/**
 * @package Invoicing
 * @copyright Copyright (c)203 Juloa.com
 * @license GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/mail.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/cparams.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/models/coupons.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/models/invoices.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/models/users.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/models/default.php');

class InvoicingModelInvoices extends InvoicingModelDefault {
	protected $_tableName = "invoices";
	protected $_fieldId = "invoicing_invoice_id";
	protected $_filters = array('search', 'dateFilterFrom', 'dateFilterTo', 'coupon_id', 'processor', 'status', 'vendor_id', 'user_id');

	protected function getSQLFilters($filters = array()) {
		$isFiltered = parent::getSQLFilters($filters);

		if($isFiltered == '') return '';

		$where = ' WHERE ';
		$conditions = array();

		$alreadyDidDate = false;
		foreach($this->_filters as $filter) {
			if(!isset($filters[$filter]) || $filters[$filter] == '') {
				continue;
			}

			$queryVendors = $this->_db->getQuery(true)
			->select('invoicing_vendor_id')
			->from('#__invoicing_vendors');

			if($filter == 'search') {
				$cb = InvoicingHelperCparams::getParam('cb',0);
				$sql_search = '%'.$filters[$filter].'%';
				if ($cb == 1) {
					$cb_name = InvoicingHelperCparams::getParam('cb_name','name');
					if ($cb_name == "name") {
						$cbname = "u.".$cb_name;
						$cb_name = "u.".$cb_name." as cb_businessname";
					} else {
						$cbname = "c.".$cb_name;
						$cb_name = "c.".$cb_name." as cb_businessname";
					}
					$queryUsers = "SELECT i.invoicing_user_id FROM #__invoicing_users as i LEFT JOIN #__comprofiler as c ON c.user_id = i.user_id LEFT JOIN #__users as u ON i.user_id = u.id WHERE $cbname LIKE ".$this->_db->q($sql_search);
				} else {
					$queryUsers = $this->_db->getQuery(true)
									->select('invoicing_user_id')
						->from('#__invoicing_users')
						->where('businessname LIKE '.$this->_db->q($sql_search).'');
				}

				$conditions[] = '(invoice_number = '.(int)$filters[$filter].' 
								OR order_number = '.(int)$filters[$filter].' 
								OR subject LIKE '.$this->_db->q('%'.$filters[$filter].'%').'
								OR user_id = '.(int)$filters[$filter].'
								OR vendor_id = '.(int)$filters[$filter].'
								OR vendor_id IN ('.$queryVendors->where('contact_name LIKE '.$this->_db->q($filters[$filter]).'').') 
								OR user_id IN ('.$queryUsers.') 
								OR '.$this->_fieldId.' = '.(int)$filters[$filter].')';
			} elseif($filter == 'dateFilterFrom' || $filter == 'dateFilterTo') {
				if($alreadyDidDate) continue;

				$alreadyDidDate = true;

				$dateFrom = $filters['dateFilterFrom'];
				$dateTo = $filters['dateFilterTo'];

				if($dateFrom != "" || $dateTo != "") {
					$whereClause = "";
					
					if($dateFrom != "") {
						$whereClause .= 'created_on >= '.$this->_db->q($dateFrom);	
					} 
					
					if($dateFrom != "" && $dateTo != "") {
						//echo "Les deux sont remplis";
						$whereClause .= ' AND ';
					}
					
					if($dateTo != "") {
						$whereClause .= 'created_on <= '.$this->_db->q($dateTo." 23:59:59");		
					} 
					
					$conditions[] = '('.$whereClause.')';
				}
			} elseif($filter == 'vendor_id' || $filter == 'user_id' || $filter == 'coupon_id') {
				$conditions[] = $filter.' = '.(int)$filters[$filter];
			} elseif($filter == 'processor') {
				$conditions[] = $filter.' = '.$this->_db->q($filters[$filter]);
			} else {
				$conditions[] = $filter.' LIKE '.$this->_db->q('%'.$filters[$filter].'%');
			}
		}
		
		if(empty($conditions)) return '';

		return $where.implode(' AND ', $conditions);
	}

	protected function initEmptyEntry() {
		$item = new stdClass;

		$item->invoicing_invoice_id = null;
		$item->order_number = null;
		$item->invoice_number = null;
		$item->user_id = null;
		$item->vendor_id = null;
		$item->subject = '';
		$item->status = 'NEW';
		$item->created_on = date('Y-m-d H:i:s');
		$item->due_date = date('Y-m-d H:i:s');
		$item->notes = '';
		$item->processor = '';
		$item->processor_key = '';
		$item->net_subamount = 0;
		$item->tax_subamount = 0;
		$item->gross_subamount = 0;
		$item->custom_discount = 0;
		$item->net_discount_amount = 0;
		$item->gross_discount_amount = 0;
		$item->tax_discount_amount = 0;
		$item->coupon_id = null;
		$item->coupon_type = null;
		$item->discount_type = '';
		$item->discount_value = 0;
		$item->net_amount = 0;
		$item->tax_amount = 0;
		$item->gross_amount = 0;
		$item->currency_id = null;
		$item->language = '*';
		$item->ip_address = '';
		$item->generator = '';
		$item->generator_key = '';
		$item->params = '';
		$item->items = array();

		return $item;
    }
	
	public function onAfterSave(&$post) {		
		//From $params
		$order_number_counter = InvoicingHelperCParams::getParam('order_number_counter',5);
		$invoice_number_counter = InvoicingHelperCParams::getParam('invoice_number_counter',5);
		
		$tmp_order_number = null;
		$tmp_invoice_number = null;
		
		$db =\JFactory::getDBO();

		if(isset($post->order_number)) {
			$tmp_order_number = $post->order_number;
		}
		if(isset($post->invoice_number)) {
			$tmp_invoice_number = $post->invoice_number;
		}

		$invoice_id = $post->invoicing_invoice_id;

		$input = \JFactory::getApplication()->input;

		$itemids = $input->getCmd('itemid',array());
		if (count($itemids) > 0) {			
			$quantities = $input->get('quantity',array());
			$names = $input->get('name',array());
			$source_keys = $input->get('source_key',array());
			$descriptions = $input->get('description',array());
			$gross_unit_prices = $input->get('gross_unit',array());
			$net_unit_prices = $input->get('net_unit',array());
			$taxes = $input->get('tax',array());
			

			$keepitems = array();
			foreach($itemids as $key =>$itemid) {
				if ($itemid != "") {
					$keepitems[] = $itemid;
				}
			}
			if (count($keepitems)>0) {
				$db->setQuery("DELETE FROM #__invoicing_invoice_items WHERE invoice_id = $invoice_id AND invoicing_invoice_item_id NOT IN(".implode(',',$keepitems).")");
			} else {
				$db->setQuery("DELETE FROM #__invoicing_invoice_items WHERE invoice_id = $invoice_id");
			}
			$db->execute();
	
			foreach($itemids as $key =>$itemid) {
				$obj = new \stdClass();
				$obj->quantity = $quantities[$key];
				$obj->name = $names[$key];
				$obj->source_key = $source_keys[$key];
				$obj->description = $descriptions[$key];
				$obj->gross_unit_price = $gross_unit_prices[$key];
				$obj->net_unit_price = $net_unit_prices[$key];
				$obj->net_amount = $obj->quantity * $obj->net_unit_price;
				$obj->gross_amount = $obj->quantity * $obj->gross_unit_price;
				$obj->tax = $taxes[$key];
				$obj->ordering = $key;
				$obj->invoice_id = $invoice_id;
				if ($itemid != "") {
					$obj->invoicing_invoice_item_id = $itemid;
					$db->updateObject('#__invoicing_invoice_items', $obj,'invoicing_invoice_item_id');
				} else {
					$db->insertObject('#__invoicing_invoice_items', $obj);
				}
			}
		}
		
		if ($post->order_number == null) {
			$post->order_number = $order_number_counter;
			$order_number_counter++;
			InvoicingHelperCparams::setParam($order_number_counter,'order_number_counter');
			$db->updateObject('#__invoicing_invoices', $post,'invoicing_invoice_id');
		}
		
		if (($post->status=="PAID")&&($post->invoice_number == null )) {
			$post->invoice_number = $invoice_number_counter;
			$invoice_number_counter++;
			InvoicingHelperCparams::setParam($invoice_number_counter,'invoice_number_counter');	
			$db->updateObject('#__invoicing_invoices', $post,'invoicing_invoice_id');

            $invoice = $post;
            
			jimport('joomla.plugin.helper');
			JPluginHelper::importPlugin('invoicinggenerator');
            $app = \JFactory::getApplication();
			$jResponse = $app->triggerEvent('onInvoicingPaymentValidation',array($invoice));
		} else {
            $invoice = $post;
				}
				
		$input = \JFactory::getApplication()->input;
		$sendMailToClient = $input->getInt('sendMailToClient', -1);
		if ($post->status =="PENDING") {
			if (($sendMailToClient == 1) || (($sendMailToClient == -1) && ($tmp_order_number == null))) {
				$email = InvoicingHelperMail::sendMailByStatus($invoice);
			}
		} else if ($post->status =="PAID") {
			if (($sendMailToClient == 1) || (($sendMailToClient == -1) && ($tmp_invoice_number == null))) {
				$email = InvoicingHelperMail::sendMailByStatus($invoice);
			}
		}
	}
	
	public function onAfterGetItem(&$record) {

		$id = $record->invoicing_invoice_id;
		if ($id != null) {
			$db =\JFactory::getDBO();
			$db->setQuery("SELECT * FROM #__invoicing_invoice_items WHERE invoice_id=".$id." ORDER BY ordering ASC");
			$list = $db->loadObjectList();
			$record->items = $list;
		
			$db->setQuery("SELECT * FROM #__invoicing_vendors WHERE invoicing_vendor_id=".(int)$record->vendor_id);
			$vendor = $db->loadObject();
			$record->vendor = $vendor;

			$record->buyer = InvoicingModelUsers::getInstance('Users', 'InvoicingModel')->getItem($record->user_id);
			
			$db->setQuery("SELECT code as coupon,title as coupon_title FROM #__invoicing_coupons WHERE invoicing_coupon_id=".(int)$record->coupon_id);
			$c = $db->loadObject();
            if($c != null) {
                $record->coupon = $c->coupon;
                $record->coupon_title = $c->coupon_title;
            }
		} else {
			$record->number= InvoicingHelperCParams::getParam('invoice_number_counter',1);
			$record->created_on = date("Y-m-d");
			$record->coupon = "";
			$record->buyer = new \stdClass();
			$record->vendor = new \stdClass();
			$record->items = null;
		}
	}
	
	/**
	 * Apply Coupon to Invoice (check item types, coupon type,etc..)
	 * @param Coupon $coupon
	 * @param Invoice $invoice
	 */
	public function setCoupon($coupon,$invoice) {
		$newinvoice = new \stdClass();
		$newinvoice->invoicing_invoice_id = $invoice->invoicing_invoice_id;
		$coupontypes = explode (",",$coupon->apply_on);
		
		if (in_array("0",$coupontypes)) {
			$alltypes = true;	
		} else {
			$alltypes = false;
		}
		
		$result = false;
		foreach($invoice->items as $i) 
		{
				if ($alltypes || (in_array($i->source,$coupontypes))) {
					$result = true;
				} 
		}
		
		if ($coupon->valuetype == "percent") {
			$discount_netamount = 0;
			$discount_grossamount = 0;
			
			foreach($invoice->items as $i) 
			{
				if ($alltypes || (in_array($i->source,$coupontypes))) {
					$discount_netamount += ($i->quantity * $i->net_unit_price * $coupon->value / 100);
					$discount_grossamount += ($i->quantity * $i->gross_unit_price * $coupon->value / 100);
				} 
			}
			$discount_taxamount = $discount_grossamount-$discount_netamount;
		} else {
			$discount_netamount = 0;
			$discount_grossamount = 0;
			
			$available_gross_discount = $coupon->value;
			foreach($invoice->items as $i)
			{
				if ($alltypes || (in_array($i->source,$coupontypes))) {
					$itemprice = $i->quantity * $i->gross_unit_price;
					if ($available_gross_discount >= $itemprice) {
						$available_gross_discount -= $itemprice;
						$discount_grossamount += $itemprice;
						$discount_netamount += $itemprice * 100 / (100 + $i->tax);
					} else {
						$discount_grossamount += $available_gross_discount;
						$discount_netamount += $available_gross_discount * 100 / (100 + $i->tax);
						$available_gross_discount = 0;
					}
				}
				if ($available_gross_discount == 0) {
					break;
				}
			}
		}

		$newinvoice->net_amount = number_format($invoice->net_subamount - $discount_netamount,2,'.','');
		$newinvoice->gross_amount = number_format($invoice->gross_subamount - $discount_grossamount,2,'.','');
		if ($newinvoice->gross_amount == -0) {
			$newinvoice->gross_amount = 0;
		}
		$newinvoice->tax_amount = number_format($newinvoice->gross_amount - $newinvoice->net_amount,2,'.','');
		
		$newinvoice->net_discount_amount = number_format($discount_netamount,2,'.','');
		$newinvoice->gross_discount_amount = number_format($discount_grossamount,2,'.','');
		$newinvoice->tax_discount_amount = number_format($discount_taxamount,2,'.','');
		
		$newinvoice->coupon_id = $coupon->invoicing_coupon_id;
		$newinvoice->discount_type = $coupon->valuetype;
		$newinvoice->discount_value = $coupon->value;
		
		$db =\JFactory::getDBO();
		$db->updateObject("#__invoicing_invoices",$newinvoice,'invoicing_invoice_id');
		
		return $result;
	}
	
	function removeCoupon($invoice) 
	{
		$newinvoice = new \stdClass();
		$newinvoice->invoicing_invoice_id = $invoice->invoicing_invoice_id;
		
		$newinvoice->net_amount = $invoice->net_subamount;
		$newinvoice->gross_amount = $invoice->gross_subamount;
		$newinvoice->tax_amount = $invoice->tax_subamount;
		
		$newinvoice->net_discount_amount = 0;
		$newinvoice->gross_discount_amount = 0;
		$newinvoice->tax_discount_amount = 0;
		
		$newinvoice->coupon_id = 0;
		$newinvoice->discount_type = "percent";
		$newinvoice->discount_value = 0;
	
		$db =\JFactory::getDBO();
		$db->updateObject("#__invoicing_invoices",$newinvoice,'invoicing_invoice_id');
	}
	
	 function isCouponUserLimitExceeded($coupon,$user_id) {
		if ($coupon->userhitslimit == 0)
			return false;
		else {
			//If User is not login and userhits is set, cannot use the coupon
			if(\JFactory::getUser()->guest) {
				return true;
			}
			$db = \JFactory::getDBO();
			$query = " SELECT count(*) FROM #__invoicing_invoices "
				   . " WHERE coupon_id='".$coupon->invoicing_coupon_id
				   . "' AND user_id='".$user_id."'";
			
			$this->_db->setQuery($query);
			$nb = $this->_db->loadResult();

			if ($nb >= $coupon->userhitslimit) {
				return true;
			} else {
				return false;	
			}
		}
	}
	
	public function onBeforeSave(&$data) {
		// If invoice is going to be Paid (previous != PAID, new == PAID)
		// Update coupon hits
		if($data->invoicing_invoice_id != '') {
			$db = \JFactory::getDbo();
			$query = $db->getQuery(true)
				->select('*')
				->from("#__invoicing_invoices as i")
				->where("i.invoicing_invoice_id = ".(int)$data->invoicing_invoice_id);
			$db->setQuery($query);
			$invoice = $db->loadObject();
			$data->invoice_number = $invoice->invoice_number;
			$data->order_number = $invoice->order_number;
		} else {
			$invoice = false;
		}
		if ($invoice !== false && $invoice->status != "PAID" && $data->status == "PAID") {
			$coupon_id = null;
			if (@$data->coupon_id != null) {
				$coupon_id = $data->coupon_id;
			} else if ($invoice->coupon_id) {
				$coupon_id = $invoice->coupon_id;
			}
			
			if ($coupon_id == null) {
				return true;
			}

			//TODO import coupon model
			
			$couponModel = InvoicingModelCoupons::getInstance('Coupons', 'InvoicingModel');
			
			$coupon = $couponModel->getItem($coupon_id);
			
			if($coupon->invoicing_coupon_id == '') {
				return true;
			}
			
			$newcoupon = new \stdClass();
			$newcoupon->invoicing_coupon_id = $coupon->invoicing_coupon_id;
			$newcoupon->hits = $coupon->hits + 1;
			$db =\JFactory::getDBO();
			$db->updateObject("#__invoicing_coupons",$newcoupon,'invoicing_coupon_id');
			
		}
		
		if(!isset($data->order_number)) {
			$order_number_counter = InvoicingHelperCParams::getParam('order_number_counter',5);

			$data->order_number = $order_number_counter;
			$order_number_counter++;
			InvoicingHelperCparams::setParam($order_number_counter,'order_number_counter');
		}

		return true;
	} 
	
	
	function getNumberOfPendingInvoices()  {
		$db = \JFactory::getDBO();
		$status = "PENDING";

		$query = "SELECT COUNT(invoicing_invoice_id) FROM #__invoicing_invoices WHERE status LIKE ".$db->quote($status);

		$db->setQuery($query);
		$count = $db->loadResult();
		
		return $count;
	}

    function getNumberAndSumBySpecifiedTime($period) {
		switch($period) {
			case "lastyear" : 
				$condition = "`created_on` > '".date("Y-12-31",strtotime('today - 2 years'))."' AND `created_on` < '".date("Y-01-01")."'";
				break;
			case "thisyear" : 
				$condition = "`created_on` >= '".date("Y-01-01")."' AND `created_on` <= '".date("Y-m-d")."'";
				break;
			case "lastmonth" : 
				$condition = "`created_on` > '".date("Y-m-01",strtotime('today - 2 months'))."' AND `created_on` < '".date("Y-m-01")."'";
				break;
			case "thismonth" : 
				$condition = "`created_on` >= '".date("Y-m-01")."' AND `created_on` <= '".date("Y-m-d")."'";
				break;
			case "lastsevendays" : 
				$condition = "`created_on` >= '".date("Y-m-d",strtotime('today - 7 days'))."' AND `created_on` < '".date("Y-m-d")."'";
				break;
			case "yesterday" : 
				$condition = "`created_on` >= '".date("Y-m-d",strtotime('today - 1 days'))."' AND `created_on` < '".date("Y-m-d")."'";
				break;
			case "today" : 
				$condition = "`created_on` >= '".date("Y-m-d 00:00:00")."' AND `created_on` <= '".date("Y-m-d 23:59:59")."'";
				break;
			default : 
				$condition = "1";
		}
		
		$query = "SELECT COUNT(*) as number, SUM( `net_amount` ) as sum FROM #__invoicing_invoices WHERE status = 'PAID' AND $condition";
		
		
		$db = \JFactory::getDBO();
		$db->setQuery($query);
		$list = $db->loadObjectList();
		$result = array ($list[0]->number,$list[0]->sum);
		
		return $result;
	}

	function getCAHTbetweenDaysFilters($dateFilterFrom,$dateFilterTo) {
		$db = \JFactory::getDBO();
		if ($dateFilterFrom != '' &&  $dateFilterTo != '') {
			
			$query="SELECT DATE( `created_on` ) as date, SUM( `net_amount` ) as sum FROM #__invoicing_invoices WHERE status = 'PAID' AND `created_on` >= '$dateFilterFrom' AND `created_on` <= '$dateFilterTo' GROUP BY DAY( `created_on` ) , MONTH( `created_on` ) , YEAR( `created_on` )";
		}
		else {
			$query="SELECT DATE( `created_on` ) as date, SUM( `net_amount` ) as sum FROM #__invoicing_invoices WHERE status = 'PAID' AND MONTH(`created_on`) = MONTH(CURDATE()) AND YEAR(`created_on`) = YEAR(CURDATE()) GROUP BY DAY( `created_on` ) , MONTH( `created_on` ) , YEAR( `created_on` )";
			$db->setQuery($query);
		}
		$db->setQuery($query);
		$result = $db->loadObjectList();
		return $result;
	}
	
	function getCAHTbetweenMonthsFilters($monthto,$yearto,$monthfrom,$yearfrom) {
		$db = \JFactory::getDBO();
		$datebegin = $yearfrom.'-'.$monthfrom.'-01';
		if ($monthto == 12)
			$dateend = ($yearto+1).'-01-01';
		else
			$dateend = $yearto.'-'.($monthto+1).'-01';
		$query="SELECT DATE( `created_on` ) as date, SUM( `net_amount` ) as sum
		FROM #__invoicing_invoices WHERE vendor_id = 1 AND status = 'PAID' AND created_on >= '$datebegin'  AND `created_on` <'$dateend' GROUP BY MONTH( `created_on` ) , YEAR( `created_on` )";
		$db->setQuery($query);
		//echo $query;
		$result = $db->loadObjectList();
	
		return $result;
	}
	
}
