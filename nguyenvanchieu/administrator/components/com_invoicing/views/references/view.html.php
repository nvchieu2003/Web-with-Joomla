<?php
/**
 * @package		Invoicing
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/cparams.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/format.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/dates.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/views/default/view.html.php');

class InvoicingViewReferences extends InvoicingViewDefault
{
	public $title = "INVOICING_REFERENCES";

	protected function setFilters() {
		$app = \JFactory::getApplication();

		$filters = array();
		$filters['source_key'] = $app->getUserStateFromRequest( 'com_invoicing.reference.source_key','source_key', '','string' );
		$filters['name'] = $app->getUserStateFromRequest( 'com_invoicing.reference.name','name', '','string' );
		$filters['description'] = $app->getUserStateFromRequest( 'com_invoicing.reference.description','description', '','string' );

		return $filters;
	}
}
