<?php
/**
 * @package		Invoicing
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

include_once(JPATH_ROOT.'/administrator/components/com_invoicing/helpers/mail.php');

class InvoicingControllerInvoice extends Controller
{
	public function __construct($config = array()) {
		parent::__construct($config);

		$this->cacheableTasks = array();
	}

	public function execute($task) {

		//var_dump($this->input);

		$allowedTasks = array('browse','read','mail');
		if(in_array($task,array('edit','add'))) $task = 'read';
		if(!in_array($task,$allowedTasks)) $task = 'browse';

		//FOFInput::setVar('task',$task,$this->input);
		parent::execute($task);
	}

	public function onBeforeBrowse()
	{
		// If we have a guest user, show the login page
		$user = \JFactory::getUser();
		if($user->guest) {
			// Show login page
			$juri = \JURI::getInstance();
			$myURI = base64_encode($juri->toString());
			$com = version_compare(JVERSION, '1.6.0', 'ge') ? 'users' : 'user';
			\JFactory::getApplication()->redirect(\JURI::base().'index.php?option=com_'.$com.'&view=login&return='.$myURI, 200);
			return false;
		}
		$userModel = InvoicingModelUsers::getInstance('Users', 'InvoicingModel');
		$client_id = $userModel->getInvoicingUser(\JFactory::getUser()->id);
		if ($client_id == null) {
			//Change client_id to -2 (to return no invoice, client_id =null returns all
			//invoices not attached to an user.
			$client_id = -2;
			$this->getModel()->user_id($client_id);
		} else {
			$this->getModel()->user_id($client_id);
		}

		//$this->getModel()->paystate('C,P');
		
		$input = \JFactory::getApplication()->input;

		// Let me cheat. If the request doesn't specify how many records to show, show them all!
		if($input->getCmd('format','html') != 'html') {
			if(!$input->getInt('limit',0) && !$input->getInt('limitstart',0)) {
				$this->getModel()->limit(0);
				$this->getModel()->limitstart(0);
			}
		}

		return true;
	}

	public function onBeforeRead()
	{		
		$input = \JFactory::getApplication()->input;
		// Force the item layout
		$layout = $input->get('layout', 'default', "String");
		if (!in_array($layout,array("item","payment"))) {
			$layout = 'item';
		}
		$this->layout = $layout;
		$this->getThisView()->setLayout($layout);



		// Make sure it's the current user's subscription
		$this->getModel()->setIDsFromRequest();

		$invoice = $this->getModel()->getItem();

		if(\JFactory::getUser()->guest) {
			$auth = false;
			// Security:  If the buyer is not linked to a joomla account, the URL should contains a key to identified the buyer
			if ((int) $invoice->buyer->user_id == 0) {
				$app = \JFactory::getApplication();
				$key = $app->getUserStateFromRequest("com_invoicing.invoice.key",			'key',		0,			'string');
				if ($key == InvoicingHelperFormat::key($invoice)) {
					$auth = true;
				}
			}

			if ($auth == false) {
					$juri = JURI::getInstance();
					$myURI = base64_encode($juri->toString());
					$com = version_compare(JVERSION, '1.6.0', 'ge') ? 'users' : 'user';
					\JFactory::getApplication()->redirect(JURI::base().'index.php?option=com_'.$com.'&view=login&return='.$myURI, 200);
					return false;
			}

		} else {
			$userModel = InvoicingModelUsers::getInstance('Users', 'InvoicingModel');
			$invoicing_user_id = $userModel->getInvoicingUser(\JFactory::getUser()->id);
			if ($invoicing_user_id != $invoice->user_id) {
				throw new Exception(\JText::_('ACCESS DENIED'));
				return false;
			}
		}

		if (($layout == "payment") &&
			($invoice->status == 'PAID')||($invoice->status=='CANCELLED')) {
			throw new Exception(\JText::_('ACCESS DENIED'));
			return false;
		}

		return true;
	}
}
