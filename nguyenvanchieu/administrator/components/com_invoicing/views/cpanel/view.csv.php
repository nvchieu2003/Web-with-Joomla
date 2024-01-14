<?php
/**
 * @package		Invoicing
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/cparams.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/format.php');

class InvoicingViewCpanel extends \JViewLegacy
{
	function  __construct($config) {
		if (!is_array($config)) {
			$config = array();
		}	
		$config['csv_filename'] = "data.csv";
		parent::__construct($config);
	}
	
	protected function onDisplay($tpl=null)
	{
		$invoiceModel = InvoicingModelInvoices::getInstance('Invoices', 'InvoicingModel');
		//$this->loadHelper('Dates');

		$today = date('Y-m-d');	

		$input = \JFactory::getApplication()->input;

		$dateFilterFrom = $input->getCmd('dateFilterFrom', '0');
		$dateFilterTo = $input->getCmd('dateFilterTo', '0');

		$monthfrom = $input->getCmd('monthfrom', '0');
		$yearfrom = $input->getCmd('yearfrom', '0');

		$monthto = $input->getCmd('monthto', '0');
		$yearto = $input->getCmd('yearto', '0');

		$caperdaylast31days = array();
		$caperdaylast31days = $invoiceModel->getCAHTbetweenDaysFilters($dateFilterFrom,$dateFilterTo);

		$dates = InvoicingHelperDates::getDatesBetween($dateFilterFrom, $dateFilterTo); 

		$list0 = InvoicingHelperDates::fillDatesWithZero($dates,$caperdaylast31days);

		$capermonth = array(); 
		$capermonth = $invoiceModel->getCAHTbetweenMonthsFilters($monthto,$yearto,$monthfrom,$yearfrom);

		$months = InvoicingHelperDates::getMonthsBetween($filters->monthfrom, $filters->monthto,$filters->yearfrom,$filters->yearto);  
		$list = array();
		$list = InvoicingHelperDates::fillMonthsWithZero($months,$capermonth);
	
		$document = \JFactory::getDocument();
		$document->setMimeEncoding('text/csv');
		JResponse::setHeader('Pragma','public');
		JResponse::setHeader('Expires','0');
		JResponse::setHeader('Cache-Control','must-revalidate, post-check=0, pre-check=0');
		JResponse::setHeader('Cache-Control','public', false);
		JResponse::setHeader('Content-Description','File Transfer');
		JResponse::setHeader('Content-Disposition','attachment; filename="'.$this->csvFilename.'"'); 
		
		echo "\"Jour\",\"CA HT\"\r\n";
				
		foreach($list0 as $item) {
			echo "\"".$item[0]."\",\"".$item[1]."\"\r\n";
		}
		return false;
	}
}