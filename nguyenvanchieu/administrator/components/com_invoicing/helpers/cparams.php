<?php
/**
 * @package		Invoicing
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

class InvoicingHelperCparams
{
	public static function getParam($key, $default = null)
	{
		static $params = null;
		
		if(!is_object($params)) {
			jimport('joomla.application.component.helper');
			$params = \JComponentHelper::getParams('com_invoicing');
		}
		
		return $params->get($key, $default);
	}
	// $value to put in $field which is in fields in config.xml
	public static function setParam($value,$field) {
		$db =\JFactory::getDBO();
		
		$db->setQuery("SELECT extension_id,params FROM #__extensions WHERE name='invoicing'");
		$extension = $db->loadObject();
		$params = json_decode($extension->params);
		if ($params == null) {
			$params = new \stdClass();
		}
		
		if ($field) {
			$params->$field = $value;
			$newconfig = new \stdClass();
			$newconfig->extension_id = $extension->extension_id;
			$newconfig->params =  json_encode($params);
			$db->updateObject('#__extensions', $newconfig,'extension_id');
		}
	}
}