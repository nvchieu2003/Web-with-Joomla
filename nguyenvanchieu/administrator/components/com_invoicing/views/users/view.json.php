<?php
/**
 * @package		Invoicing
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license GNU General Public License version 3, or later
 */
namespace Juloa\Invoicing\Admin\View\Users;

use \JFactory;
use Jtext;
// Check to ensure this file is within the rest of the framework
defined('_JEXEC') or die();
 
class Json extends \FOF30\View\DataView\Json {

	public function onBeforeRead($tpl = NULL) {
		$item  = $this->getModel()->getItem();
		$item->country  = \JText::_($item->country);
		$model = $this->getModel();

		$document = \JFactory::getDocument();
		$document->setMimeEncoding('application/json');
		
		// Default JSON behaviour in case the template isn't there!
		$json = json_encode($item->getData());
		do
		{
		} while(@ob_end_clean());
		echo $json;
		exit() ; // needed if php notice are displayed
		return false;	
	}	
}
