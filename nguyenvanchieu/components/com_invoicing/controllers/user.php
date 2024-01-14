<?php
/**
 *  @package Invoicing
 *  @copyright Copyright (c)2010-2012 JoomPROD
 *  @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

class InvoicingControllerUser extends DataController
{
/**
	 * I don't want an ACL check 
	 *
	 * @return bool
	 */
	public function onBeforeAdd() {
		return true;
	}

	public function onBeforeSave() {
		return true;
	}

	public function register() {
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

	public function save() {
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
