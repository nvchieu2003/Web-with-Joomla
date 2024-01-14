<?php
/**
 * @package   Juloa
 * @copyright Copyright (c)2020 Juloa
 * @license   GNU General Public License version 3, or later
 */

include_once(JPATH_ROOT.'/administrator/components/com_invoicing/assets/view.pdf.php');
include_once(JPATH_ROOT.'/administrator/components/com_invoicing/helpers/format.php');

defined('_JEXEC') or die;

class InvoicingViewInvoices extends TViewPdf
{
	public function onBeforeRead($tpl = NULL) {
		
		$input = \JFactory::getApplication()->input;
		$cid = $input->get('cid', 0, 'integer');

		$imodel = InvoicingModelInvoices::getInstance('Invoices', 'InvoicingModel');
		$invoice  = $imodel->getItem($cid);
		
		if ($invoice->invoice_number != null) {
			$content = InvoicingHelperFormat::formatInvoicePDF($invoice);
            $invoiceNumber = InvoicingHelperFormat::formatInvoiceNumber($invoice);
            $filenameText = \JText::_('INVOICING_INVOICE_FILENAME');
        } else { 
			$content = InvoicingHelperFormat::formatOrderPDF($invoice);
            $invoiceNumber = InvoicingHelperFormat::formatOrderNumber($invoice);
            $filenameText = \JText::_('INVOICING_ORDER_FILENAME');
        }
        
		$this->content = $content;

		$filename = sprintf($filenameText,$invoiceNumber);
		$filename = str_replace(array("/","."," "),"_",$filename);
		$this->setName($filename);
	}
}