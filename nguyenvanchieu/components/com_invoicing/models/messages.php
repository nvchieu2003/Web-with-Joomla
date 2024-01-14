<?php
/**
 *  @package Invoicing
 *  @copyright Copyright (c)203 Juloa.com
 *  @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

class InvoicingModelMessages extends \JModelLegacy
{
	public function __construct($config = array()) 
	{
		// This is a dirty trick to avoid getting warning PHP messages by the 
		// JDatabase layer
		$config['table'] = 'shops';
		parent::__construct($config);
	}

	public function &getItem($id = null) {
		return null;
	}
}
