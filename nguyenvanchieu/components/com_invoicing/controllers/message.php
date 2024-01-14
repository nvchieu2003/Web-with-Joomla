<?php
/**
 * @package		Invoicing
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

class InvoicingControllerMessage extends DataController
{
	private static $loggedinUser = false;

	public function __construct($config = array()) {
		parent::__construct($config);
	}

	public function execute($task) {
		parent::execute('read');
	}

	public function read()
	{
		// Load the model
		$model = $this->getModel();

		// Set the layout to item, if it's not set in the URL
		if(is_null($this->layout)) $this->layout = 'item';

		// Display
		$this->display(in_array('read', $this->cacheableTasks));
	}
}
