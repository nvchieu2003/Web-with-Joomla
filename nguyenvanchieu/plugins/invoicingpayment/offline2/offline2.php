<?php
/**
 * @package		Invoicing
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license GNU General Public License version 3, or later
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

require_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/assets/paymentplugin.php');
require_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/format.php');
require_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/mail.php');


class plgInvoicingpaymentOffline2 extends InvoicingAbstractPaymentPlugin
{
	public function __construct(&$subject, $config = array())
	{
		if(version_compare(JVERSION, '1.6', 'ge')) {
			$defaultimg = JURI::root().'plugins/invoicingpayment/offline/offline/offline.jpg';
		} else {
			$defaultimg = JURI::root().'plugins/invoicingpayment/offline/offline.jpg';
		}		
		
		$config = array_merge($config, array(
			'ppName'		=> 'offline2',
			'ppKey'			=> 'PLG_INVOICINGPAYMENT_OFFLINE2_TITLE',
			'ppImage'		=> $defaultimg)
				);
		
		parent::__construct($subject, $config);
	}
	
	function onInvoicingPaymentDisplay($paymentmethod,$data)
	{	
		if($paymentmethod != $this->ppName) return false;
		
		$this->config = new \stdClass();
		$this->config->text = \JText::_($this->params->get('text',""));
		$this->config->text = InvoicingHelperFormat::replaceTags($this->config->text,$data);
		
		ob_start();
		include(dirname(__FILE__)."/offline2/details.php");
		$result = ob_get_clean ();
		
		$imodel = InvoicingModelInvoices::getInstance('Invoices', 'InvoicingModel');
		$item = $imodel->getItem($data->invoicing_invoice_id);
		$item->processor = 'offline2';
		$imodel->update($item);
		
        InvoicingHelperMail::sendMailByStatus($item);
        
		return $result;
	}

	function onInvoicingPaymentNotification($paymentmethod,$data) {	
	}
}
