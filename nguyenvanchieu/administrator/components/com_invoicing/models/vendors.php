<?php
/**
 * @package Invoicing
 * @copyright Copyright (c)203 Juloa.com
 * @license GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/models/default.php');
jimport( 'joomla.filesystem.file' );

class InvoicingModelVendors extends InvoicingModelDefault {
	protected $_tableName = "vendors";
	protected $_fieldId = "invoicing_vendor_id";
	protected $_filters = array('search');

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
				$conditions[] = '('.$this->_fieldId.' LIKE '.$this->_db->q('%'.$filters[$filter].'%').' 
								OR contact_name LIKE '.$this->_db->q('%'.$filters[$filter].'%').'
								OR company_name LIKE '.$this->_db->q('%'.$filters[$filter].'%').'
								OR company_email LIKE '.$this->_db->q('%'.$filters[$filter].'%').')';
			} else {
				$conditions[] = $filter.' LIKE '.$this->_db->q('%'.$filters[$filter].'%');
			}
		}
		
		if(empty($conditions)) return '';

		return $where.implode(' AND ', $conditions);
	}

	public function onAfterGetItem(&$record) {
		$record->params = json_decode($record->params);
	}
	
	protected function initEmptyEntry() {
		$item = new stdClass;

		$item->invoicing_vendor_id = null;
		$item->contact_name = '';
		$item->company_name = '';
		$item->company_email = '';
		$item->company_phone = '';
		$item->company_url = '';
		$item->logo = '';
		$item->filename = '';
		$item->address1 = '';
		$item->address2 = '';
		$item->city = '';
		$item->state = '';
		$item->zip = '';
		$item->country = '';
		$item->params = '';
		$item->notes = '';

		return $item;
    }

	protected function onBeforeSave(&$data) {
		if(isset($data->params)) {
			$data->params = json_encode($data->params);
		} else {
			$data->params = '';
		}
	
		$formats = array("png","jpg","gif");

		$files = \JFactory::getApplication()->input->files->getArray();

		$data->filename = "";
		$data->logo = "";

		if (isset($files['filename']['name']) && $files['filename']['name'] != '' && !$files['filename']['error']) {
		
			$filename = $files['filename']['name'];
		
			$extension = \JFile::getExt($filename);
			$filename = "logo-".uniqid().".".$extension;
			
			if (file_exists(JPATH_ROOT."/media/com_invoicing/images/vendor/".$filename)) {
				\jFile::delete(JPATH_ROOT."/media/com_invoicing/images/vendor/".$filename);
			}

			if (strpos($extension,"php") !== false) {
				$extension = 'txt';
			} else {
					if ( in_array($extension,$formats)) {
						$extension = strtolower($extension);
						\JFile::upload($files['filename']['tmp_name'],JPATH_ROOT."/media/com_invoicing/images/vendor/".$filename);
						$data->filename = $filename;
						$data->logo = $filename;
					}	
			}
		} else {
			$input = \JFactory::getApplication()->input;
			if ($input->getInt("delete_".$files['filename']['name']) == 1) {
				if (file_exists(JPATH_ROOT."/media/com_invoicing/images/vendor/".$files['filename']['name'])) {
					\JFile::delete(JPATH_ROOT."/media/com_invoicing/images/vendor/".$files['filename']['name']);
				}
				$data->filename = "";
				$data->logo = "";
			}
		}
		
		parent::onBeforeSave($data);

		return true;
	}	
	
}
