<?php
/**
 * @package		Invoicing
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/views/default/view.html.php');

class InvoicingViewCurrencies extends InvoicingViewDefault
{
	public $title = "INVOICING_CURRENCIES";

	protected function setFilters() {
		$app = \JFactory::getApplication();

		$filters = array();
		$filters['enabled'] = $app->getUserStateFromRequest( 'com_invoicing.currency.publish','enabled', '','cmd' );

		return $filters;
	}
}
