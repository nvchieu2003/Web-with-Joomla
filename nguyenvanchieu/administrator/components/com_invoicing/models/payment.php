<?php
/**
 *  @package Invoicing
 *  @copyright Copyright (c)203 Juloa.com
 *  @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/models/default.php');

class InvoicingModelPayment extends InvoicingModelDefault {
	/**
	 * Gets a list of payment plugins and their titles
	 */
	public function getPaymentPlugins()
	{
		jimport('joomla.plugin.helper');
		\JPluginHelper::importPlugin('invoicingpayment');
		$app = \JFactory::getApplication();
		$jResponse = $app->triggerEvent('onInvoicingPaymentGetIdentity');

		return $jResponse; // name, title
	}
	
	/**
	 * Runs a payment callback
	 */
	public function runCallback($paymentmethod) {	
		$input = \JFactory::getApplication()->input;
		$rawDataPost = $input->get->post->getArray();//JRequest::get('POST', 2);
		$rawDataGet = $input->get->get->getArray();//JRequest::get('GET', 2);
		$data = array_merge($rawDataGet, $rawDataPost);
		
		include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/log.php');
		InvoicingHelperLog::log(print_r($data,true));
		
		jimport('joomla.plugin.helper');
		JPluginHelper::importPlugin('invoicingpayment');
		$app = \JFactory::getApplication();
		$jResponse = $app->triggerEvent('onInvoicingPaymentNotification',array(
			$paymentmethod,
			$data
		));
		if(empty($jResponse)) return false;
		
		$status = false;
		
		foreach($jResponse as $response)
		{
			$status = $status || $response;
		}
		
		return $status;
	}
	
	/**
	 * Get the form set by the active payment plugin
	 */
	public function getPaymentForm($paymentmethod,$invoice) {
		jimport('joomla.plugin.helper');
		JPluginHelper::importPlugin('invoicingpayment');
		$app = \JFactory::getApplication();
		$jResponse = $app->triggerEvent('onInvoicingPaymentDisplay',array($paymentmethod,$invoice));
		if(empty($jResponse)) return false;
		
		foreach($jResponse as $response) {
			if($response === false) continue;
			return $response;
		}
		return "";
	}
}