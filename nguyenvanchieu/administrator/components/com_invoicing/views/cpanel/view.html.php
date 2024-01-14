<?php
/**
 * @package		Invoicing
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

use FOF30\Container\Container;

include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/cparams.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/format.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/dates.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/views/default/view.html.php');

class InvoicingViewCpanel extends InvoicingViewDefault
{
	
	protected function display($tpl=null)
	{
		$input = \JFactory::getApplication()->input;
		$currency_id = $input->getInt('currency_id', 0);
		if ($currency_id == "") {
			$currency = Container::getInstance('com_invoicing')->factory
			->model('Currencies')->tmpInstance()
			->savestate(0)
			->filter_order('ordering')
			->filter_order_Dir('ASC')
			->limit(0)
			->offset(0)
			->getFirstItem();
			if ($currency !== false) {
				$currency_id = $currency->invoicing_currency_id;
			}
		}
		$this->currency_id = $currency_id;
		
		$invoiceModel = Container::getInstance('com_invoicing')->factory
		->model('Invoices')->tmpInstance();
		$nb_pending_orders = $invoiceModel->getNumberOfPendingInvoices();
		$this->nb_pending_orders = $nb_pending_orders;
		
		$stats = new \stdClass();
		$filters = new \stdClass();
		
		$dateFilterFrom = $input->getCmd('dateFilterFrom', '');
		$dateFilterTo = $input->getCmd('dateFilterTo', '');
		if ($dateFilterFrom == '' ||  $dateFilterTo == ''){
			//$today = date('Y-m-d');
			$last30days = date('Y-m-d', strtotime('today - 30 days'));
			$dateFilterFrom = date('Y-m-d', strtotime($last30days));
			$dateFilterTo = date('Y-m-d');
		}
		
		$filters->dateFilterTo = $dateFilterTo;
		$filters->dateFilterFrom = $dateFilterFrom;
		
		
		
		$filters->monthfrom = $input->getCmd('monthfrom', '');
		$filters->yearfrom = $input->getCmd('yearfrom', '');
		
		$filters->monthto = $input->getCmd('monthto', '');
		$filters->yearto = $input->getCmd('yearto', '');
		
		if ($filters->monthfrom == 0 || $filters->monthto == 0) {
			$filters->monthfrom =  (int) date("m");
			$filters->monthto = $filters->monthfrom;
		}
		
		else if ($filters->monthfrom == $filters->monthto) {
				$filters->monthto = (int)$filters->monthfrom + 1;
		}
		
		if ($filters->yearfrom == 0 || $filters->yearto == 0) {
			$filters->yearto = date("Y");
			$filters->yearfrom = date("Y",strtotime("-1 year"));
		}

		$lastyear = $invoiceModel->getNumberAndSumBySpecifiedTime("lastyear");
		$stats->lastyear_number = ($lastyear[0]);
		$stats->lastyear_sum = ($lastyear[1]);
		
		$thisyear = $invoiceModel->getNumberAndSumBySpecifiedTime("thisyear");
		$stats->thisyear_number = ($thisyear[0]);
		$stats->thisyear_sum = ($thisyear[1]);
		
		$lastmonth = $invoiceModel->getNumberAndSumBySpecifiedTime("lastmonth");
		$stats->lastmonth_number = ($lastmonth[0]);
		$stats->lastmonth_sum = ($lastmonth[1]);
		
		$thismonth = $invoiceModel->getNumberAndSumBySpecifiedTime("thismonth");
		$stats->thismonth_number = ($thismonth[0]);
		$stats->thismonth_sum = ($thismonth[1]);
		
		$lastsevendays = $invoiceModel->getNumberAndSumBySpecifiedTime("lastsevendays");
		$stats->lastsevendays_number = ($lastsevendays[0]);
		$stats->lastsevendays_sum = ($lastsevendays[1]);
		
		$yesterday = $invoiceModel->getNumberAndSumBySpecifiedTime("yesterday");
		$stats->yesterday_number = ($yesterday[0]);
		$stats->yesterday_sum = ($yesterday[1]);
		
		$today = $invoiceModel->getNumberAndSumBySpecifiedTime("today");
		$stats->today_number = ($today[0]);
		$stats->today_sum = ($today[1]);
		
		$caperdaylast31days = array();
		$caperdaylast31days = $invoiceModel->getCAHTbetweenDaysFilters($filters->dateFilterFrom,$filters->dateFilterTo);
		$dates = InvoicingHelperDates::getDatesBetween($filters->dateFilterFrom, $filters->dateFilterTo); 
		$dailypoints = InvoicingHelperDates::fillDatesWithZero($dates,$caperdaylast31days);
		$this->dailypoints = $dailypoints;
		
		$capermonth = array(); 
		$capermonth = $invoiceModel->getCAHTbetweenMonthsFilters((int)$filters->monthto,(int)$filters->yearto,(int)$filters->monthfrom,(int)$filters->yearfrom);
		$months = InvoicingHelperDates::getMonthsBetween((int)$filters->monthfrom, (int)$filters->monthto,(int)$filters->yearfrom,(int)$filters->yearto);   
		$monthlypoints = InvoicingHelperDates::fillMonthsWithZero($months,$capermonth);
		$this->monthlypoints = $monthlypoints;
		
		//$stats->average_day_sum = 0;
		//$stats->average_day_number= 0;
		
		$this->filters = $filters;
		$this->stats = $stats;
	}
}
