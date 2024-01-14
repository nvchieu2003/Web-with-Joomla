<?php
/**
 * @package Invoicing
 * @copyright Copyright (c)203 Juloa.com
 * @license GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/models/default.php');

class InvoicingModelCoupons extends InvoicingModelDefault {
	protected $_tableName = "coupons";
	protected $_fieldId = "invoicing_coupon_id";
	protected $_publishField = "enabled";
	protected $_filters = array('search', 'enabled');

	protected function getSQLFilters($filters = array()) {
		$isFiltered = parent::getSQLFilters($filters);

		if($isFiltered == '') return '';

		$where = ' WHERE ';
		$conditions = array();

		foreach($this->_filters as $filter) {
			if(!isset($filters[$filter]) || $filters[$filter] == '') {
				continue;
			}
			if($filter == 'search') {
				$conditions[] = 'code LIKE '.$this->_db->q('%'.$filters[$filter].'%').' OR title LIKE '.$this->_db->q('%'.$filters[$filter].'%');
			}
			if($filter == 'enabled') {
				$conditions[] = 'enabled = '.(int)$filters[$filter];
			}
		}
		
		if(empty($conditions)) return '';

		return $where.implode(' AND ', $conditions);
	}
	
	public function onAfterGetItem(&$record) {
		if ($record->apply_on != "")
			$record->apply_on = explode(",",$record->apply_on);
		else
			$record->apply_on = array ("");
		if (in_array("0",$record->apply_on)) {
			$record->apply_on = array("0");
		}
	}
	
	public function onBeforeSave(&$data) {
		if(isset($data->apply_on)) {
            $data->apply_on = implode(",",$data->apply_on);
        } else {
            $data->apply_on = '0';
        }
		return true;
	}
	
	public function getCoupon($code) {
			$db = \JFactory::getDbo();
			
			$query = "SELECT * FROM #__invoicing_coupons WHERE code = '".$code."'";
			
			$db->setQuery($query);
			
			$result = $db->loadObject();
			
			return $result;
	}
	
	protected function initEmptyEntry() {
		$item = new stdClass;

		$item->invoicing_coupon_id = null;
		$item->title = '';
		$item->code = '';
		$item->value = '';
		$item->hits = '';
		$item->hitslimit = '';
		$item->userhitslimit = '';
		$item->apply_on = '';
		$item->valuetype = '';
		$item->enabled = 1;

		return $item;
    }

	protected function getId($post) {
		$sql = "SELECT invoicing_coupon_id FROM #__invoicing_coupons
				WHERE code = ".$this->_db->quote($post->code)." 
				AND created_on = ".$this->_db->quote($post->created_on);

		$this->_db->setQuery($sql);

		return $this->_db->loadResult();
	}
}
