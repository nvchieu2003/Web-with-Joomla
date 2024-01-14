<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');

include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/models/payment.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/models/coupons.php');

/**
 * Content Component Controller
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class InvoicingController extends JControllerLegacy
{
    public function process($cachable = false) {		
		$input = \JFactory::getApplication()->input;
		$paymentmethod = $input->getCmd('method','none');
        $model = InvoicingModelPayment::getInstance('Payment', 'InvoicingModel');
		$result = $model->runCallback($paymentmethod);
		echo $result ? 'OK' : 'FAILED';
		\JFactory::getApplication()->close();
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
            $model = InvoicingModelCoupons::getInstance('Coupons', 'InvoicingModel');
            $coupon = $model->getCoupon($couponcode);

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

		$invoice = $imodel->getItem($id);
		if(!\JFactory::getUser()->guest) {
			$invoicing_user_id = InvoicingModelUsers::getInstance('Users', 'InvoicingModel')->getInvoicingUser($user->id);
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
				$app->redirect( \JRoute::_('index.php?option=com_invoicing&view=invoice'),200 );
			}
		}

		if ($invoice->net_amount != 0)
			exit();

		$invoice->processor = '';
		$invoice->status = 'PAID';

		$imodel->update($invoice);

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
			$app->redirect( \JRoute::_('index.php?option=com_invoicing&view=invoice'),200);
		}
	}

	public function registerUser() {
		// Check for request forgeries.
		\JSession::checkToken() or jexit(\JText::_('JINVALID_TOKEN'));

		$app	= \JFactory::getApplication();
		$input = \JFactory::getApplication()->input;

		$return = $input->get('return','', 'String');

		$user = \JFactory::getUser();

		if ($user->id != 0) {
			// Redirect back to the user profile screen.
			$this->setRedirect(\JRoute::_('index.php?option=com_invoicing&view=user&task=edit&return='.$return, false));
			return false;
		}

		// If user registration is not allowed, show 403 not authorized.
		$params = JComponentHelper::getParams('com_users');
		if ($params->get('allowUserRegistration') == '0') {
			throw new Exception(\JText::_('Access Forbidden'));
			return;
		}

		// Get the user data.
		$requestData = $this->input->post->get('jform', array(), 'array');

		// Save the data in the session.
		$app->setUserState('com_users.registration.data', $requestData);

		$errors = array();
		$mandatoryFields = array('username','name','password1','password2','email1','email2','zip','city','country','address1');
		foreach($mandatoryFields as $f) {
			if ($requestData[$f] == "") {
				$errors[] = \JText::_('INVOICING_FORM_ERROR_INCOMPLETE');
			}
		}
		if ($requestData['password1'] != $requestData['password2']) {
			$errors[] = \JText::_('INVOICING_FORM_ERROR_PASSWORD_DONT_MATCH');
		}
		if ($requestData['email1'] != $requestData['email2']) {
			$errors[] = \JText::_('INVOICING_FORM_ERROR_EMAIL_DONT_MATCH');
		}

		if (count($errors) > 0) {
			foreach($errors as $error) {
				if ($error instanceof Exception)
				{
					$app->enqueueMessage($error->getMessage(), 'warning');
				} else {
					$app->enqueueMessage($error, 'warning');
				}
			}
			// Redirect back to the registration screen.
			$this->setRedirect(\JRoute::_('index.php?option=com_invoicing&view=user&task=add&return='.$return, false));
			return false;
		}

		// Initialise the table with JUser.
		$user = new JUser;
		$data = array();
		// Prepare the data for the user object.
		$data['username'] = $requestData['username'];
		$data['name'] = $requestData['name'];
		$data['email'] = JStringPunycode::emailToPunycode($requestData['email1']);
		$data['password'] = $requestData['password1'];

		// Prepare the data for the user object.
		$useractivation = $params->get('useractivation');

		// Check if the user needs to activate their account.
		if (($useractivation == 1) || ($useractivation == 2)) {
			jimport('joomla.user.helper');
			if (version_compare(JVERSION,'3.0.3','>=')) {
				$data['activation'] = JApplication::getHash(JUserHelper::genRandomPassword());
			} else {
				$data['activation'] = JUtility::getHash(JUserHelper::genRandomPassword());
			}
			//$data['block'] = 1;
		}

		// Get the groups the user should be added to after registration.
		$data['groups'] = array();
		// Get the default new user group, Registered if not specified.
		$system	= $params->get('new_usertype', 2);
		$data['groups'][] = $system;

		// Bind the data.
		if (!$user->bind($data)) {
			$error = $user->getError();
			if ($error instanceof Exception)
			{
				$app->enqueueMessage($error->getMessage(), 'warning');
			} else {
				$app->enqueueMessage($error, 'warning');
			}
			$this->setRedirect(\JRoute::_('index.php?option=com_invoicing&view=user&task=add&return='.$return, false));
			return false;
		}

		// Load the users plugin group.
		JPluginHelper::importPlugin('user');

		// Store the data.
		if (!$user->save()) {
			$error = $user->getError();
			if ($error instanceof Exception)
			{
				$app->enqueueMessage($error->getMessage(), 'warning');
			} else {
				$app->enqueueMessage($error, 'warning');
			}
			$this->setRedirect(\JRoute::_('index.php?option=com_invoicing&view=user&task=add&return='.$return, false));
			return false;
		}

		$app->login( array( 'username' => $requestData['username'], 'password' => $requestData['password1'] ), array() );

		$user = \JFactory::getUser();

		$data = new \stdClass();
		$data->user_id = $user->id;
		$data->businessname = $requestData['name'];
		$data->lastname = $requestData['name'];
		$data->address1 = $requestData['address1'];
		$data->address2 = $requestData['address2'];
		$data->city = $requestData['city'];
		$data->zip = $requestData['zip'];
		$data->country = $requestData['country'];

		$userModel = InvoicingModelUsers::getInstance('Users', 'InvoicingModel');
		$userModel->saveUser($user->id,$data);

		if ($return != "") {
			$return = base64_decode($return);
		} else {
			$return = \JRoute::_('index.php?option=com_invoicing');
		}
		$this->setRedirect($return);

		return true;
	}

	public function saveUser() {
		// Check for request forgeries.
		\JSession::checkToken() or jexit(\JText::_('JINVALID_TOKEN'));
		$input = \JFactory::getApplication()->input;

		$return = $input->get('return','', 'String');

		$user = \JFactory::getUser();

		if ($user->id == 0) {
			// Redirect back to the registration screen.
			$this->setRedirect(\JRoute::_('index.php?option=com_invoicing&view=user&task=add&return='.$return, false));
			return false;
		}

		$app	= \JFactory::getApplication();

		// Get the user data.
		$requestData = $this->input->post->get('jform', array(), 'array');

		// Save the data in the session.
		$app->setUserState('com_users.registration.data', $requestData);

		$errors = array();
		$mandatoryFields = array('name','zip','city','country','address1');
		foreach($mandatoryFields as $f) {
			if ($requestData[$f] == "") {
				$errors[] = \JText::_('INVOICING_FORM_ERROR_INCOMPLETE');
			}
		}

		if (count($errors) > 0) {
			foreach($errors as $error) {
				if ($error instanceof Exception)
				{
					$app->enqueueMessage($error->getMessage(), 'warning');
				} else {
					$app->enqueueMessage($error, 'warning');
				}
			}
			// Redirect back to the registration screen.
			$this->setRedirect(\JRoute::_('index.php?option=com_invoicing&view=user&task=edit&return='.$return, false));
			return false;
		}

		$data = new \stdClass();
		$data->user_id = $user->id;
		$data->businessname = $requestData['name'];
		$data->lastname = $requestData['name'];
		$data->address1 = $requestData['address1'];
		$data->address2 = $requestData['address2'];
		$data->city = $requestData['city'];
		$data->zip = $requestData['zip'];
		$data->country = $requestData['country'];

		$userModel = InvoicingModelUsers::getInstance('Users', 'InvoicingModel');
		$userModel->saveUser($user->id,$data);

		if ($return != "") {
			$return = base64_decode($return);
		} else {
			$return = \JRoute::_('index.php?option=com_invoicing');
		}
		$this->setRedirect($return);

		return true;
	}
}