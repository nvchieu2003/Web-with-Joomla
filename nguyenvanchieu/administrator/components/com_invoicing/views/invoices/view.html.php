<?php
/**
 * @package   Juloa
 * @copyright Copyright (c)2020 Juloa
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/cparams.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/format.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/dates.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/views/default/view.html.php');

class InvoicingViewInvoices extends InvoicingViewDefault
{
    public $title = "INVOICING_INVOICES";

    protected function setFilters() {
		$app = \JFactory::getApplication();

		$filters = array();
		$filters['search'] = $app->getUserStateFromRequest( 'com_invoicing.invoice.search','search', '','string' );
		$filters['dateFilterFrom'] = $app->getUserStateFromRequest( 'com_invoicing.invoice.dateFilterFrom','dateFilterFrom', '','string' );
		$filters['dateFilterTo'] = $app->getUserStateFromRequest( 'com_invoicing.invoice.dateFilterTo','dateFilterTo', '','string' );
		$filters['coupon_id'] = $app->getUserStateFromRequest( 'com_invoicing.invoice.coupon_id','coupon_id', '','string' );
		$filters['processor'] = $app->getUserStateFromRequest( 'com_invoicing.invoice.processor','processor', '','string' );
		$filters['status'] = $app->getUserStateFromRequest( 'com_invoicing.invoice.status','status', '','string' );
		$filters['vendor_id'] = $app->getUserStateFromRequest( 'com_invoicing.invoice.vendor_id','vendor_id', '','string' );
		$filters['user_id'] = $app->getUserStateFromRequest( 'com_invoicing.invoice.user_id','user_id', '','string' );

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
	
	public function onBeforeAdd($tpl = null) {
        parent::onBeforeAdd($tpl);

        $input = \JFactory::getApplication()->input;
        
        $quoteId = $input->getInt('quote', 0);
        
        $returnurl = $input->get('returnurl','', "String");
        $this->returnurl = $returnurl;
        /**
         * If quoteId is set we want to create a invoice from a quote.
         * We need to fill the invoice params with quote
         */
        if($quoteId){
			$quoteModel = InvoicingModelQuotes::getInstance('Quotes', 'InvoicingModel');
            $quote = $quoteModel->getItem($quoteId);
            
            $quote->invoicing_invoice_id = null;
            $quote->order_number = null;
            $quote->invoice_number = null;
            $quote->status = null;
            
            // Need to remove the item id to recreate them in the invoice
            foreach($quote->items as $key => $item) {
            	$quote->items[$key]->invoicing_invoice_item_id = "";
            }
            
            $this->item = $quote;
        }
		
	}
}