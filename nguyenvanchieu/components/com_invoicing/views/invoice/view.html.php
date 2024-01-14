<?php
/**
 * @package		Invoicing
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license GNU General Public License version 3, or later
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

include_once(JPATH_ROOT.'/components/com_invoicing/views/default/view.html.php');

/**
 * @package		Joomla
 * @subpackage	Contacts
 */
class InvoicingViewInvoice extends InvoicingViewDefault
{
	public function onBeforeRead($tpl = NULL) {
		parent::onBeforeRead($tpl);
		if ($this->getLayout() == "payment") {
			// Get the list of payment plugins
			$paymentModel = InvoicingModelPayment::getInstance('Payment', 'InvoicingModel');
			$processors = $paymentModel->getPaymentPlugins();
			$this->processors = $processors;
			$app = \JFactory::getApplication();
			$key = $app->getUserStateFromRequest("com_invoicing.invoice.key",'key',	0,'string');
			$this->key = $key;
			
			$input = \JFactory::getApplication()->input;
			$couponstatus = $input->getCmd('couponstatus','');
			$this->couponstatus = $couponstatus;
		}
		$input = \JFactory::getApplication()->input;
		$iModel = InvoicingModelInvoices::getInstance('Invoices', 'InvoicingModel');
		$id = $input->get('id', 0, 'int');
		$invoice  = $iModel->getItem($id);
		//$this->loadHelper('Format');

		
		if ($invoice->invoice_number != null)
			$content = InvoicingHelperFormat::formatInvoiceHTML($invoice);
		else
			$content = InvoicingHelperFormat::formatOrderHTML($invoice);
		
        $enableCoupon = InvoicingHelperCparams::getParam('enable_coupon', 1);
        
        $this->enablecoupon = $enableCoupon;
		$this->content = $content;
		
		$this->invoice = $invoice;
		
		$user = \JFactory::getUser();
		
		$model = InvoicingModelUsers::getInstance('Users', 'InvoicingModel');
		$data = (array) $model->getItem($model->getInvoicingUser($user->id));
		$this->user = $data;
		
		$mandatoryfields = array('city','country','zip','address1','businessname');
		$missing = false;
		foreach($mandatoryfields as $field) {
			if (!isset($data[$field]) || $data[$field] == null) {
				$missing = true;
				break;
			}
		}
		
		// Show login page
    $profileItemid = (int)InvoicingHelperCparams::getParam('itemid_profile',0);
    //check if Itemid for the profile exist, if yes we add it to the url
    if($profileItemid > 0) {
        $itemid = "&Itemid=".$profileItemid;
    } else {
        $itemid = "";
    }
    $juri = JURI::getInstance();
    $myURI = base64_encode($juri->toString());
    $editprofileurl = \JRoute::_('index.php?option=com_invoicing&view=user&payment=1&return='.$myURI.$itemid);

    //If the profile redirect is enabled and if one mandatory field is missing
    $enableRedirection = InvoicingHelperCparams::getParam('enable_mandatory_profile_redirect', 1);

		if ($missing == true && $enableRedirection == 1) {
			\JFactory::getApplication()->redirect($editprofileurl, 200);
		}
		
		$this->editprofileurl = $editprofileurl;
		
	}	
	
}