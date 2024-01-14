<?php
/**
 *  @package Invoicing
 *  @copyright Copyright (c)2010-2012 JoomPROD
 *  @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

class InvoicingControllerPayment extends Controller
{
	public function __construct($config = array()) {
		parent::__construct($config);

		$this->csrfProtection = false;

		$this->cacheableTasks = array();

		$this->modelName = 'Payment';
	}

	public function execute($task) {
		if (($task != "process")&&($task != 'checkcoupon')&&($task != "validorder")) {
			$task = 'onAdd';
		}

		//FOFInput::setVar('task',$task,$this->input);
		parent::execute($task);
	}

	public function process($cachable = false) {		
		$input = \JFactory::getApplication()->input;
		$paymentmethod = $input->getCmd('method','none');
		$paymentModel = InvoicingModelPayment::getInstance('Payment', 'InvoicingModel');
		$result = $paymentModel->runCallback($paymentmethod);
		echo $result ? 'OK' : 'FAILED';
		\JFactory::getApplication()->close();
	}

	/**
	 * I don't want an ACL check
	 *
	 * @return bool
	 */
	public function onBeforeAdd() {
		return true;
	}

	public function onAdd() {
		$id = $this->getModel()->getState('id',0,'int');
		$paymentmethod = $this->getModel()->getState('method','','cmd');
		
		$invoiceModel = InvoicingModelInvoices::getInstance('Invoices', 'InvoicingModel');
		$invoice = $invoiceModel->getItem($id);

		if (($invoice->status == 'PAID')||($invoice->status=='CANCELLED')) {
			throw new Exception(\JText::_('ACCESS DENIED'));
		} else {
			$form = $this->getModel()->getPaymentForm($paymentmethod,$invoice);
			$this->form = $form;	
			//TODO Better location will be in controller
            //TODO InvoicingHelperMail::sendMailByStatus($invoice);
		}	
		return true;
	}

	public function checkcoupon()
	{
		$app = \JFactory::getApplication();
		$user		= \JFactory::getUser();

		$input = \JFactory::getApplication()->input;
		$couponcode = $input->get('couponcode', '', "String");

        if($couponcode == '') {
            $coupon = null;
        }else{
			$couponModel = InvoicingModelCoupons::getInstance('Coupons', 'InvoicingModel');
            $coupon = $couponModel->getCoupon($couponcode);

            if(!isset($coupon->invoicing_coupon_id))
                $coupon = null;
        }
		$id = $input->getInt('id', '');
		//echo $user->id;
		$imodel = InvoicingModelInvoices::getInstance('Invoices', 'InvoicingModel');
		$umodel = InvoicingModelUsers::getInstance('Users', 'InvoicingModel');

		$invoicing_user_id = $umodel->getInvoicingUser($user->id);
		$invoice = $imodel->getItem($id);

		if(!\JFactory::getUser()->guest) {
			$invoicing_user_id = $umodel->getInvoicingUser($user->id);
			if ($invoicing_user_id != $invoice->user_id) {
				exit();
			}
		}


		if(empty($invoice->invoicing_invoice_id))
			$invoice = null;

		if ($invoice == null)
			exit();

		if(\JFactory::getUser()->guest) {
			// Security:  If the buyer is not linked to a joomla account, the URL should contains a key to identified the buyer
			if ((int) $invoice->buyer->user_id == 0) {
				$key = $app->getUserStateFromRequest("com_invoicing.invoice.key",			'key',		0,			'string');
				if ($key != InvoicingHelperFormat::key($invoice)) {
					exit();
				}
			} else {
				exit();
			}
		}

		if ($coupon != null) {
			if ($imodel->isCouponUserLimitExceeded($coupon,$invoicing_user_id) == true) {
				$app->enqueueMessage(\JText::_('INVOICING_COUPON_USERHITSLIMIT_EXCEEDED'), 'message');
				$app->redirect( \JRoute::_('index.php?option=com_invoicing&view=invoice&layout=payment&id='.$invoice->invoicing_invoice_id.'&couponstatus=error'), 200 );
			}
			else if ($coupon->hitslimit != 0 && $coupon->hitslimit <= $coupon->hits) {
				$app->enqueueMessage(\JText::_('INVOICING_COUPON_LIMIT_EXCEEDED'), 'message');
				$app->redirect( \JRoute::_('index.php?option=com_invoicing&view=invoice&layout=payment&id='.$invoice->invoicing_invoice_id.'&couponstatus=error'), 200 );
			}
			else
			{
					$imodel->onAfterGetItem($invoice);
					$couponapplies = $imodel->setCoupon($coupon,$invoice);
					if($couponapplies) {
					$imodel->setCoupon($coupon,$invoice);
						$app->redirect( \JRoute::_('index.php?option=com_invoicing&view=invoice&layout=payment&id='.$invoice->invoicing_invoice_id.'&couponstatus=valid'), 200 );
					}
					else {
						$imodel->removeCoupon($invoice);
						$app->redirect( \JRoute::_('index.php?option=com_invoicing&view=invoice&layout=payment&id='.$id.'&couponstatus=error'), 200 );
					}
			}
		} else {
			$imodel->removeCoupon($invoice);
			$app->redirect( \JRoute::_('index.php?option=com_invoicing&view=invoice&layout=payment&id='.$id.'&couponstatus=error'), 200 );
		}
	}

	/**
	* Set Order -> Paid, => only if invoice net_amount = 0
	*/
	public function validorder() {
		$app = \JFactory::getApplication();
		$user		= \JFactory::getUser();
		$input = \JFactory::getApplication()->input;

		$id = $input->getInt('id',0);
		$imodel = InvoicingModelInvoices::getInstance('Invoices', 'InvoicingModel');
		$umodel = InvoicingModelUsers::getInstance('Users', 'InvoicingModel');

		$invoice = $imodel->getItem($id);
		if(!\JFactory::getUser()->guest) {
			$invoicing_user_id = $umodel->getInvoicingUser($user->id);
			if ($invoicing_user_id != $invoice->user_id) {
				exit();
			}
		}

		if(empty($invoice->invoicing_invoice_id))
			$invoice = null;
		if ($invoice == null)
			exit();
		if(\JFactory::getUser()->guest) {
			// Security:  If the buyer is not linked to a joomla account, the URL should contains a key to identified the buyer
			if ((int) $invoice->buyer->user_id == null) {

				$key = $app->getUserStateFromRequest("com_invoicing.invoice.key",'key',	0,'string');
				if ($key != InvoicingHelperFormat::key($invoice)) {
					exit();
				}
			} else {
				exit();
			}
		}

		if (($invoice->status == "PAID")||($invoice->status == "CANCELLED")) {

			//If not log in, redirect the the invoice instead of "my invoices page"
			if(\JFactory::getUser()->guest) {
				$app->enqueueMessage(\JText::_('INVOICING_ERROR'), 'message');
				$app->redirect( \JRoute::_('index.php?option=com_invoicing&view=invoice&id='.$invoice->invoicing_invoice_id),200 );
			} else if (file_exists(JPATH_ROOT.'/components/com_comprofiler/')) {
				$app->enqueueMessage(\JText::_('INVOICING_ERROR'), 'message');
				$app->redirect( \JRoute::_('index.php?option=com_comprofiler&tab=InvoicingTab'),200 );
			} else {
				$app->enqueueMessage(\JText::_('INVOICING_ERROR'), 'message');
				$app->redirect( \JRoute::_('index.php?option=com_invoicing&view=invoices'),200 );
			}
		}

		if ($invoice->net_amount != 0)
			exit();

		$imodel->save(array('invoicing_invoice_id'=>$id,
							 'processor' => '',
							 'status'=>'PAID'));

		//If not log in, redirect the the invoice instead of "my invoices page"
		if(\JFactory::getUser()->guest) {
			$app->enqueueMessage(\JText::_('INVOICING_ORDER_CONFIRMED'), 'message');
			$app->redirect( \JRoute::_('index.php?option=com_invoicing&view=invoice&id='.$invoice->invoicing_invoice_id),200 );
		}

		else if (file_exists(JPATH_ROOT.'/components/com_comprofiler/')) {
			$app->enqueueMessage(\JText::_('INVOICING_ORDER_CONFIRMED'), 'message');
			$app->redirect( \JRoute::_('index.php?option=com_comprofiler&tab=InvoicingTab'),200 );
		} else {
			$app->enqueueMessage(\JText::_('INVOICING_ORDER_CONFIRMED'), 'message');
			$app->redirect( \JRoute::_('index.php?option=com_invoicing&view=invoices'),200);
		}
	}
}
