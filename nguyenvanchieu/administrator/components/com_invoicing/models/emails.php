<?php
/**
 * @package Invoicing
 * @copyright Copyright (c)203 Juloa.com
 * @license GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/models/default.php');

class InvoicingModelEmails extends InvoicingModelDefault {
	protected $_tableName = "emails";
	protected $_fieldId = "invoicing_email_id";
	protected $_publishField = "published";

	protected function initEmptyEntry() {
		$item = new stdClass;

		$item->invoicing_email_id = null;
		$item->subject = '';
		$item->body = '';
		$item->description = '';
		$item->published = 1;
		$item->pdf = 1;
		$item->key = '';
		$item->language = '*';

		return $item;
    }
}
