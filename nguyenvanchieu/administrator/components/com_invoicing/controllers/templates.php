<?php
/**
 *  @package Invoicing
 *  @copyright Copyright (c)203 Juloa.com
 *  @license GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/controllers/default.php');

class InvoicingControllerTemplates extends InvoicingControllerDefault {
	protected $accessLabel = "template";
	protected $controllerLabel = "templates";

	function __construct($config= array()) {
		parent::__construct($config);

		$this->_model = $this->getModel( "templates");
	}
	
	function init() {
		// Set the default view name from the Request
		$this->_view = $this->getView("templates",'html');

		// Push a model into the view
		$this->_model = $this->getModel( "templates");
		try {
			$this->_view->setModel( $this->_model, true );
		} catch(Exception $e) {

		}
	}

	public function isNew($post) {
		if($post['invoicing_template_id'] == '') {
			return true;
		}

		return false;
	}
}
