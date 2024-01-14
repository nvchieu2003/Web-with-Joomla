<?php
/**
 * @package		Invoicing
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license GNU General Public License version 3, or later
 */

use \JFactory;
use JText;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

include_once(JPATH_ROOT.'/administrator/components/com_invoicing/assets/view.pdf.php');
include_once(JPATH_ROOT.'/administrator/components/com_invoicing/helpers/format.php');

/**
 * @package		Joomla
 * @subpackage	Contacts
 */
class InvoicingViewQuotes extends TViewPdf
{
	public function onBeforeRead($tpl = NULL) {
		
		$input = \JFactory::getApplication()->input;
		$cid = $input->get('cid', 0, 'integer');

		$qmodel = InvoicingModelInvoices::getInstance('Quotes', 'InvoicingModel');
		$quote  = $qmodel->getItem($cid);
		
		if ($quote->invoice_number != null) {
			$content = InvoicingHelperFormat::formatInvoicePDF($quote);
            $invoiceNumber = InvoicingHelperFormat::formatInvoiceNumber($quote);
            $filenameText = \JText::_('INVOICING_INVOICE_FILENAME');
        } else { 
			$content = InvoicingHelperFormat::formatOrderPDF($quote);
            $invoiceNumber = InvoicingHelperFormat::formatOrderNumber($quote);
            $filenameText = \JText::_('INVOICING_ORDER_FILENAME');
        }
        
		$this->content = $content;

		$filename = sprintf($filenameText,$invoiceNumber);
		$filename = str_replace(array("/","."," "),"_",$filename);
		$this->setName($filename);
	}
	
}