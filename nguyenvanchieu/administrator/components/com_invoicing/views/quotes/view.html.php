<?php
/**
 * @package		Invoicing
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license GNU General Public License version 3, or later
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/cparams.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/format.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/dates.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/views/default/view.html.php');

/**
 * 
 * @package		Joomla
 * @subpackage	Contacts
 */
class InvoicingViewQuotes extends InvoicingViewDefault
{
	public $title = "INVOICING_QUOTES";

	protected function setFilters() {
		$app = \JFactory::getApplication();

		$filters = array();
		$filters['search'] = $app->getUserStateFromRequest( 'com_invoicing.quote.search','search', '','string' );
		$filters['dateFilterFrom'] = $app->getUserStateFromRequest( 'com_invoicing.quote.dateFilterFrom','dateFilterFrom', '','string' );
		$filters['dateFilterTo'] = $app->getUserStateFromRequest( 'com_invoicing.quote.dateFilterTo','dateFilterTo', '','string' );
		$filters['coupon_id'] = $app->getUserStateFromRequest( 'com_invoicing.quote.coupon_id','coupon_id', '','string' );
		$filters['processor'] = $app->getUserStateFromRequest( 'com_invoicing.quote.processor','processor', '','string' );
		$filters['status'] = $app->getUserStateFromRequest( 'com_invoicing.quote.status','status', '','string' );
		$filters['vendor_id'] = $app->getUserStateFromRequest( 'com_invoicing.quote.vendor_id','vendor_id', '','string' );
		$filters['user_id'] = $app->getUserStateFromRequest( 'com_invoicing.quote.user_id','user_id', '','string' );

		return $filters;
	}

	public function onBeforeRead($tpl = NULL) {
        $cid = $app->input->get('cid', 0);
		if(is_array($cid)) {
			$cid = $cid[0];
		}

		$model = $this->getModel();
		$invoice = $model->getItem($cid);
		//$this->loadHelper('Format');

		if ($invoice->invoice_number != null) {
			$content = InvoicingHelperFormat::formatInvoiceHTML($invoice);
        } else {
			$content = InvoicingHelperFormat::formatOrderHTML($invoice);
        }
	
		$this->content = $content;
	}
}