<?php
/**
 * @package Invoicing
 * @copyright Copyright (c)203 Juloa.com
 * @license GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/models/default.php');

class InvoicingModelReferences extends InvoicingModelDefault {
	protected $_tableName = "references";
	protected $_fieldId = "invoicing_reference_id";
	protected $_publishField = '';
	protected $_filters = array('source_key', 'name', 'description');

	protected function getSQLFilters($filters = array()) {
		$isFiltered = parent::getSQLFilters($filters);

		if($isFiltered == '') return '';

		$where = ' WHERE ';
		$conditions = array();

		foreach($this->_filters as $filter) {
			if(!isset($filters[$filter]) || $filters[$filter] == '') {
				continue;
			}
			$conditions[] = $filter.' LIKE '.$this->_db->q('%'.$filters[$filter].'%');
		}
		
		if(empty($conditions)) return '';

		return $where.implode(' AND ', $conditions);
	}

	public function onAfterGetItem(&$record) {
		$record->params = json_decode($record->params);
	}
	
	public function onBeforeSave(&$data) {
		$data->ordering = 99;
		if(isset($data->params)) {
			$data->params = json_encode($data->params);
		} else {
			$data->params = '';
		}
		return true;
	}
	
	protected function initEmptyEntry() {
		$item = new stdClass;

		$item->invoicing_reference_id = null;
		$item->name = '';
		$item->description = '';
		$item->quantity = 0;
		$item->gross_unit_price = 0;
		$item->tax = 0;
		$item->net_unit_price = 0;
		$item->net_amount = 0;
		$item->gross_amount = 0;
		$item->source = '';
		$item->source_key = '';
		$item->params = '';

		return $item;
    }

	protected function getId($post) {
		$sql = "SELECT MAX(invoicing_reference_id) FROM #__invoicing_references";

		$this->_db->setQuery($sql);

		return $this->_db->loadResult();
	}
}
