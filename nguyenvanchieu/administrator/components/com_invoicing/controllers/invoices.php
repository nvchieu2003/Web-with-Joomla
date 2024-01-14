<?php
/**
 *  @package Invoicing
 *  @copyright Copyright (c)203 Juloa.com
 *  @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

include_once(JPATH_ROOT.'/administrator/components/com_invoicing/helpers/mail.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/controllers/default.php');

class InvoicingControllerInvoices extends InvoicingControllerDefault
{
	protected $accessLabel = "invoice";
	protected $controllerLabel = "invoices";

	function __construct($config= array()) {
		parent::__construct($config);

		$this->_model = $this->getModel( "invoices");
	}

	public function isNew($post) {
		if($post['invoicing_invoice_id'] == '') {
			return true;
		}

		return false;
	}

	public function mail() {
		$input = \JFactory::getApplication()->input;
		$cid = $input->get('cid', 0, 'integer');

		$imodel = InvoicingModelInvoices::getInstance('Invoices', 'InvoicingModel');
		$item  = $imodel->getItem($cid);

		$email = InvoicingHelperMail::sendMailByStatus($item);

		$app = \JFactory::getApplication();
		$url = 'index.php?option=com_invoicing&view=invoices';

		if(is_bool($email))
			$msg = \JText::_('INVOICING_COMMON_EMAIL_SENT');

		$app->enqueueMessage($msg, 'message');
		$app->redirect($url, 200);
	}
}
