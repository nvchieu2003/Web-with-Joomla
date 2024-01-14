<?php
/**
 * @package		Invoicing
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license GNU General Public License version 3, or later
 */
namespace Juloa\Invoicing\Admin\View\Vendors;

// Check to ensure this file is within the rest of the framework
defined('_JEXEC') or die();

class Json extends \FOF30\View\DataView\Json {

	public function onBeforeRead($tpl = NULL) {
		$user  = $this->getModel()->getItem();
		$user->country  = \JText::_(str_replace('_UE','UE',$user->country));
		
		$document = \JFactory::getDocument();
		$document->setMimeEncoding('application/json');
		
		// Default JSON behaviour in case the template isn't there!
		$json = json_encode($user->getData());
		do
		{
		} while(@ob_end_clean());
		echo $json;
		exit() ; // needed if php notice are displayed
		return false;
	}	
}
