<?php
/**
 * @package		Invoicing
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/cparams.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/format.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/dates.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/views/default/view.html.php');

/**
 * 
 * @package		Joomla
 * @subpackage	Contacts
 */
class InvoicingViewLogs extends InvoicingViewDefault
{
	public $title = "INVOICING_LOG";

	public function add($tpl = null) {
		$this->setEditToolbar(JText::_("COM_INVOICING")." - ".JText::_($this->title), '');

		$logModel = InvoicingModelLogs::getInstance('Logs', 'InvoicingModel');
		$log = $logModel->getLog();
		$log = str_replace("\n",'<br/>',$log);
		$this->log = $log;

		//this is ugly
		echo $this->log;
		//parent::display('log');
	}
}