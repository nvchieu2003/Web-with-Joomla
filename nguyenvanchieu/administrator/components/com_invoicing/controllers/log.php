<?php
/**
 *  @package Invoicing
 *  @copyright Copyright (c)203 Juloa.com
 *  @license GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/controllers/default.php');

class InvoicingControllerLog extends InvoicingControllerDefault {
	protected $accessLabel = "log";
	protected $controllerLabel = "logs";

	function __construct($config= array()) {
		parent::__construct($config);

		$this->_model = $this->getModel( "log");
	}

	public function execute($task) {
   		parent::execute('add');
	}
}
