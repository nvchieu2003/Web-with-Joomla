<?php
/**
 *  @package Invoicing
 *  @copyright Copyright (c)203 Juloa.com
 *  @license GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/controllers/default.php');

class InvoicingControllerCpanels extends InvoicingControllerDefault
{
	protected $accessLabel = "cpanel";
	protected $controllerLabel = "cpanels";

	public function execute($task) {
		if(!in_array($task, array('browse'))) {
			$task = 'browse';
		}
		parent::execute($task);
	}
}
