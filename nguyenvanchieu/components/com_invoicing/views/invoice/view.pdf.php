<?php
/**
 * @package		Invoicing
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license GNU General Public License version 3, or later
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

include_once(JPATH_ROOT.'/administrator/components/com_invoicing/assets/view.pdf.php');
include_once(JPATH_ROOT.'/administrator/components/com_invoicing/helpers/format.php');
include_once(JPATH_ROOT.'/administrator/components/com_invoicing/models/invoices.php');
/**
 * @package		Joomla
 * @subpackage	Contacts
 */
class InvoicingViewInvoice extends TViewPdf
{
	public function onBeforeRead($tpl = NULL) {
		
		$input = \JFactory::getApplication()->input;
		$cid = $input->get('id', 0, 'integer');

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