<?php
/**
 * @package		Invoicing
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

include_once(JPATH_ROOT.'/components/com_invoicing/views/default/view.html.php');

class InvoicingViewPayment extends InvoicingViewDefault
{
	protected function onBeforeAdd($tpl = null)
	{
		$input = \JFactory::getApplication()->input;
		$id = $input->get('id',0,'int');
		$paymentmethod = $input->getCmd('method','');
		
		$imodel = InvoicingModelInvoices::getInstance('Invoices', 'InvoicingModel');
		$invoice = $imodel->getItem($id);
		
		if (($invoice->status == 'PAID')||($invoice->status=='CANCELLED')) {
			throw new Exception(\JText::_('ACCESS DENIED'));
		} else {
			$paymentModel = InvoicingModelPayment::getInstance('Payment', 'InvoicingModel');
			$form = $paymentModel->getPaymentForm($paymentmethod,$invoice);
			$this->form = $form;	
			//TODO Better location will be in controller
            //TODO InvoicingHelperMail::sendMailByStatus($invoice);
		}	
	}
}
