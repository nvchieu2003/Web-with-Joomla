<?php
/**
 * @package		Invoicing
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/cparams.php');

class InvoicingViewDefault extends \JViewLegacy { 
	function __construct($config = array())
	{
		parent::__construct($config);

		$uri = JUri::getInstance();
		$baseurl = JURI::base();
		
		$user		= JFactory::getUser();
		
		$this->userid = $user->id;
		$this->baseurl = $baseurl;
	}

	protected function onBeforeAdd($tpl = null) {
		return true;
	}

	protected function onBeforeRead($tpl = null) {
		return true;
	}

	function display($tpl = null) {
		$app = JFactory::getApplication();

		$task = $app->input->getCmd('task', 'add');
		
		if($task == 'edit') {
			$task = 'add';
		}

		if(method_exists($this, 'onBefore'.ucfirst($task))) {
			$functionName = 'onBefore'.ucfirst($task);
			$this->$functionName($tpl);
		}

		parent::display($tpl);
	}
}