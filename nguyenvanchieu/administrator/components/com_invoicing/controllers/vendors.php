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
class InvoicingControllerVendors extends InvoicingControllerDefault {
	protected $accessLabel = "vendor";
	protected $controllerLabel = "vendors";

	function __construct($config= array()) {
		parent::__construct($config);

		$this->_model = $this->getModel( "vendors");
	}

	public function isNew($post) {
		if($post['invoicing_vendor_id'] == '') {
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
				$result->vendor_id = $item->invoicing_vendor_id;
				$result->vendor_label = $item->company_name." (".$item->contact_name.")";
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
