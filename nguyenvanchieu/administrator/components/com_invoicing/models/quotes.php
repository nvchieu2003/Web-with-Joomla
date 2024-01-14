<?php
/**
 * @package Invoicing
 * @copyright Copyright (c)203 Juloa.com
 * @license GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/models/default.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/models/users.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/mail.php');

class InvoicingModelQuotes extends InvoicingModelDefault {
	protected $_tableName = "quotes";
	protected $_fieldId = "invoicing_quote_id";
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

				$conditions[] = '(quote_number = '.(int)$filters[$filter].' 
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

		$item->invoicing_quote_id = null;
		$item->quote_number = null;
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
	
	public function onAfterSave(&$table) {		
		//From $params
		$quote_number_counter = InvoicingHelperCParams::getParam('quote_number_counter',5);
		
		$tmp_quote_number = null;
		if(isset($table->quote_number)) {
			$tmp_quote_number = $table->quote_number;
		}
		
		if ($table->quote_number == null) {
			$table->quote_number = $quote_number_counter;
			$quote_number_counter++;
			InvoicingHelperCparams::setParam($quote_number_counter,'quote_number_counter');
			$this->_db->updateObject('#__invoicing_quotes', $post,'invoicing_quote_id');
		}

		$input = \JFactory::getApplication()->input;

		$quote_id =$table->invoicing_quote_id;
		$itemids = $input->getCmd('itemid',array());
		if (count($itemids) > 0) {			
			$quantities = $input->get('quantity',array());
			$names = $input->get('name',array());
			$source_keys = $input->get('source_key',array());
			$descriptions = $input->get('description',array());
			$gross_unit_prices = $input->get('gross_unit',array());
			$net_unit_prices = $input->get('net_unit',array());
			$taxes = $input->get('tax',array());
			$db =\JFactory::getDBO();
	
			$keepitems = array();
			foreach($itemids as $key =>$itemid) {
				if ($itemid != "") {
					$keepitems[] = $itemid;
				}
			}
			if (count($keepitems)>0) {
				$db->setQuery("DELETE FROM #__invoicing_quote_items WHERE quote_id = $quote_id AND invoicing_quote_item_id NOT IN(".implode(',',$keepitems).")");
			} else {
				$db->setQuery("DELETE FROM #__invoicing_quote_items WHERE quote_id = $quote_id");
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
				$obj->quote_id = $quote_id;
				if ($itemid != "") {
					$obj->invoicing_quote_item_id = $itemid;
					$db->updateObject('#__invoicing_quote_items', $obj,'invoicing_quote_item_id');
				} else {
					$db->insertObject('#__invoicing_quote_items', $obj);
				}
			}
		}
		
		$quote = $table;
		
		$input = \JFactory::getApplication()->input;
		$sendMailToClient = $input->getInt('sendMailToClient', -1);
        if (($sendMailToClient == 1) || (($sendMailToClient == -1) && ($tmp_quote_number == null))) {
            $email = InvoicingHelperMail::sendMailByStatus($quote);
        }
	}
	
	public function onAfterGetItem(&$record) {
	
		$id = $record->invoicing_quote_id;
		if ($id != null) {
			$db =\JFactory::getDBO();
			$db->setQuery("SELECT * FROM #__invoicing_quote_items WHERE quote_id=".$id." ORDER BY ordering ASC");
			$list = $db->loadObjectList();
			$record->items = $list;
		
			$db->setQuery("SELECT * FROM #__invoicing_vendors WHERE invoicing_vendor_id=".(int)$record->vendor_id);
			$vendor = $db->loadObject();
			$record->vendor = $vendor;

			$userModel = InvoicingModelUsers::getInstance('Users', 'InvoicingModel');
			$record->buyer = $userModel->getItem($record->user_id);
			
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
	public function setCoupon($coupon,$quote) {
		$newquote = new \stdClass();
		$newquote->invoicing_invoice_id = $quote->invoicing_quote_id;
		$coupontypes = explode (",",$coupon->apply_on);
		
		if (in_array("0",$coupontypes)) {
			$alltypes = true;	
		} else {
			$alltypes = false;
		}
		
		$result = false;
		foreach($quote->items as $i) 
		{
				if ($alltypes || (in_array($i->source,$coupontypes))) {
					$result = true;
				} 
		}
		
		if ($coupon->valuetype == "percent") {
			$discount_netamount = 0;
			$discount_grossamount = 0;
			
			foreach($quote->items as $i) 
			{
				if ($alltypes || (in_array($i->source,$coupontypes))) {
					$discount_netamount += ($i->quantity * $i->net_unit_price * $coupon->value / 100);
					$discount_grossamount += ($i->quantity * $i->gross_unit_price * $coupon->value / 100);
				} 
			}
			$discount_taxamount = $discount_netamount-$discount_grossamount;
		} else {
			$discount_netamount = 0;
			$discount_grossamount = 0;
			
			$available_net_discount = $coupon->value;
			foreach($quote->items as $i)
			{
				if ($alltypes || (in_array($i->source,$coupontypes))) {
					$itemprice = $i->quantity * $i->net_unit_price;
					if ($available_net_discount >= $itemprice) {
						$available_net_discount -= $itemprice;
						$discount_netamount += $itemprice;
						$discount_grossamount += $itemprice / ((100 + $i->tax) / 100);
					} else {
						$discount_netamount += $available_net_discount;
						$discount_grossamount += $available_net_discount / ((100 + $i->tax) / 100);
						$available_net_discount = 0;
					}
				}
				if ($available_net_discount == 0) {
					break;
				}
			}
		}

		$newquote->net_amount = number_format($quote->net_subamount - $discount_netamount,2);
		$newquote->gross_amount = number_format($quote->gross_subamount - $discount_grossamount,2);
		if ($newquote->gross_amount == -0) {
			$newquote->gross_amount = 0;
		}
		$newquote->tax_amount = number_format($newquote->net_amount - $newquote->gross_amount,2);
		
		$newquote->net_discount_amount = number_format($discount_netamount,2);
		$newquote->gross_discount_amount = number_format($discount_grossamount,2);
		$newquote->tax_discount_amount = number_format($discount_taxamount,2);
		
		$newquote->coupon_id = $coupon->invoicing_coupon_id;
		$newquote->discount_type = $coupon->valuetype;
		$newquote->discount_value = $coupon->value;
		
		$db =\JFactory::getDBO();
		$db->updateObject("#__invoicing_quotes",$newquote,'invoicing_quote_id');
		
		return $result;
	}
	
	function removeCoupon($quote) 
	{
		$newquote = new \stdClass();
		$newquote->invoicing_invoice_id = $quote->invoicing_invoice_id;
		
		$newquote->net_amount = $quote->net_subamount;
		$newquote->gross_amount = $quote->gross_subamount;
		$newquote->tax_amount = $quote->tax_subamount;
		
		$newquote->net_discount_amount = 0;
		$newquote->gross_discount_amount = 0;
		$newquote->tax_discount_amount = 0;
		
		$newquote->coupon_id = 0;
		$newquote->discount_type = "percent";
		$newquote->discount_value = 0;
	
		$db =\JFactory::getDBO();
		$db->updateObject("#__invoicing_quotes",$newquote,'invoicing_quote_id');
	}
	
}
