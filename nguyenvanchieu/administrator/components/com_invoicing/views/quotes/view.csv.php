<?php
/**
 * @package		Invoicing
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/cparams.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/format.php');

use \JFactory;
use JText;

class InvoicingViewQuotes extends \JViewLegacy
{
	function  __construct($config) {
		if (!is_array($config)) {
			$config = array();
		}	
		$config['csv_filename'] = "invoices.csv";
		parent::__construct($config);
	}
	
	protected function onBeforeBrowse($tpl=null)
	{
		// Load the model
		$model = InvoicingModelInvoices::getInstance('Quotes', 'InvoicingModel');
		$items = $model->savestate(0)
						->limit(0)
						->offset(0)->getItemList
						();
		
		$this->items = $items;
	
		$document = \JFactory::getDocument();
		$document->setMimeEncoding('text/csv');
		JResponse::setHeader('Pragma','public');
		JResponse::setHeader('Expires','0');
		JResponse::setHeader('Cache-Control','must-revalidate, post-check=0, pre-check=0');
		JResponse::setHeader('Cache-Control','public', false);
		JResponse::setHeader('Content-Description','File Transfer');
		JResponse::setHeader('Content-Disposition','attachment; filename="'.$this->csvFilename.'"');
	
		// Default CSV behaviour
		//if(empty($items)) return;
		
		$csvformat = InvoicingHelperCparams::getParam('csvformat','');
		if ($csvformat != "") {
			$fields = explode(',',$csvformat);
			
		} else {
			$fields = null;
		}
		
		if($this->csvHeader) {
			$item = array_pop($items);
			
			if ($fields == null) {
				$keys = get_object_vars($item);
			}
			$items[] = $item;
			reset($items);

			$csv = array();
			if ($fields != null) {
				foreach($fields as $f) {
					$csv[] = '"' . str_replace('"', '""', str_replace(array("{","}"),"",$f)) . '"';
				}
			} else {
				foreach($keys as $k => $v) {
					$csv[] = '"' . str_replace('"', '""', $k) . '"';
				}
			}
			echo implode(",", $csv) . "\r\n";
		}
				
		foreach($items as $item) {

			$item = $model->savestate(0)->getItem($item->invoicing_quote_id);
			if ($item->vendor == null)
				continue;
			if ($item->buyer == null)
				continue;

			$csv = array();
			if ($fields == null) {
				$keys = get_object_vars($item);
			}
			if ($fields != null) {
				foreach($fields as $f) {
					$value = InvoicingHelperFormat::replaceTags($f, $item);
					$value = str_replace("&nbsp;"," ",$value);
					$csv[] = '"' . str_replace('"', '""',$value)  . '"';
				}
			} else {
				foreach($item as $k => $v) {
					$csv[] = '"' . str_replace('"', '""', $v) . '"';
				}
			}
			echo implode(",", $csv) . "\r\n";
		}
		return false;
	}
}
