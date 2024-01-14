<?php
/**
 * @package		Invoicing
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license GNU General Public License version 3, or later
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

include_once(JPATH_ROOT.'/components/com_invoicing/views/default/view.html.php');

class InvoicingViewUser extends InvoicingViewDefault
{
	protected function onBeforeAdd($tpl = null)
	{
		$user = \JFactory::getUser();
		
		$input = \JFactory::getApplication()->input;
		$itemid = $input->getInt('Itemid','');
		$defaulturl = base64_encode(\JRoute::_('index.php?option=com_invoicing&Itemid='.$itemid));
		$return_url = $input->get('return',$defaulturl,'String');
		$this->return_url = $return_url;
		$this->itemid = $itemid;
		
		$app = \JFactory::getApplication();
		
		if ($user->id == 0) {
			$data = (array) $app->getUserState('com_users.registration.data', array());
			$this->data = $data;
		
			$this->setLayout("register");
		} else {
			$model = InvoicingModelUsers::getInstance('Users', 'InvoicingModel');
			$data = (array) $model->getItem($model->getInvoicingUser($user->id));
			$this->data = $data;
			
			$this->setLayout("form");
		}
	}
}
