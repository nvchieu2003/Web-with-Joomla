<?php
/**
 * @package		Invoicing
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license GNU General Public License version 3, or later
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/controllers/default.php');

/**
 * Invoicing Component Controller
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class InvoicingControllerUsers extends InvoicingControllerDefault {
	protected $accessLabel = "user";
	protected $controllerLabel = "users";

	function __construct($config= array()) {
		parent::__construct($config);

		$this->_model = $this->getModel("users");
	}
	
	function init() {
		// Set the default view name from the Request
		$this->_view = $this->getView("users",'html');

		// Push a model into the view
		$this->_model = $this->getModel("users");
		try {
			$this->_view->setModel( $this->_model, true );
		} catch(Exception $e) {

		}
	}

	public function isNew($post) {
		if($post['invoicing_user_id'] == '') {
			return true;
		}

		return false;
	}

	function onAfterSave() {
		$input = \JFactory::getApplication()->input;
		if ($input->getInt("ajaxcall",0)) {
			$model = $this->getModel();
			$id = $model->getId();

			if ($id == 0) {
				$result = new \stdClass();
				$result->error = 1;
			} else {
				$model->savestate(0);
				$item = $model->getItem($id);
				$result = new \stdClass();
				$result->value = $item->invoicing_user_id;
				$result->label = $item->businessname." / ".$item->username;
			}
			do
			{
			} while(@ob_end_clean());
			echo json_encode($result);
			exit();
		}
		return true;
	}
}
