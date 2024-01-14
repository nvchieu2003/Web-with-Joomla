<?php
/**
 * @package Invoicing
 * @copyright Copyright (c)203 Juloa.com
 * @license GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/models/default.php');

class InvoicingModelTaxes extends InvoicingModelDefault {
	protected $_tableName = "taxes";
	protected $_fieldId = "invoicing_tax_id";
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

		$item->invoicing_tax_id = null;
		$item->taxrate = 20;
		$item->enabled = 1;

		return $item;
    }

	public function onBeforeSave(&$data) {
		$data->ordering = 99;
		return true;
	}
}
