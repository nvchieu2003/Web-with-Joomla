<?php
/**
 *  @package Invoicing
 *  @copyright Copyright (c)2012 JoomPROD
 *  @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

include_once('cparams.php');

class InvoicingHelperLoad
{	
	public static function loadLanguage($language) {
			$paths = array(JPATH_ADMINISTRATOR, JPATH_ROOT);
			$jlang = \JFactory::getLanguage();
			$jlang->load("com_invoicing", $paths[0], $language, true);
			$jlang->load("com_invoicing", $paths[1], $language, true);
	}
}