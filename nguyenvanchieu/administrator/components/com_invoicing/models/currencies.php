<?php
/**
 * @package Invoicing
 * @copyright Copyright (c)203 Juloa.com
 * @license GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/models/default.php');

class InvoicingModelCurrencies extends InvoicingModelDefault {
	protected $_tableName = "currencies";
	protected $_fieldId = "invoicing_currency_id";
	protected $_publishField = "enabled";
	protected $_filters = array('enabled');

	protected function getSQLFilters($filters = array()) {
		$isFiltered = parent::getSQLFilters($filters);

		if($isFiltered == '') return '';

		$where = ' WHERE ';
		$conditions = array();

		foreach($this->_filters as $filter) {
			if(!isset($filters[$filter]) || $filters[$filter] == '') {
				continue;
			}
			if($filter == 'enabled') {
				$conditions[] = 'enabled = '.(int)$filters[$filter];
			}
		}
		
		if(empty($conditions)) return '';

		return $where.implode(' AND ', $conditions);
	}

	protected function initEmptyEntry() {
		$item = new stdClass;

		$item->invoicing_currency_id = null;
		$item->symbol = '€';
		$item->code = 'EUR';
		$item->symbol_position = 'after';
		$item->number_decimals = '2';
		$item->decimal_separator = '.';
		$item->thousand_separator = '';
		$item->enabled = 1;

		return $item;
    }

	public function onBeforeSave(&$data) {
		$data->ordering = 99;
		return true;
	}
}
