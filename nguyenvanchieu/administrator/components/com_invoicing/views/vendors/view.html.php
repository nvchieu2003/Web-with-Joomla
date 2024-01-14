<?php
/**
 * @package		Invoicing
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/views/default/view.html.php');

class InvoicingViewVendors extends InvoicingViewDefault
{
	public $title = "INVOICING_VENDORS";

	protected function setFilters() {
		$app = \JFactory::getApplication();
    
		$filters = array();
		$filters['search'] = $app->getUserStateFromRequest( 'com_invoicing.vendor.search','search', '','string' );

		return $filters;
	}
}
