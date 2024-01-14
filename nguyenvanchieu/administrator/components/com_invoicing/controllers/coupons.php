<?php
/**
 *  @package Invoicing
 *  @copyright Copyright (c)203 Juloa.com
 *  @license GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/controllers/default.php');

class InvoicingControllerCoupons extends InvoicingControllerDefault {
	protected $accessLabel = "coupon";
	protected $controllerLabel = "coupons";

	function __construct($config= array()) {
		parent::__construct($config);

		$this->_model = $this->getModel( "coupons");
	}
	
	function init() {
		// Set the default view name from the Request
		$this->_view = $this->getView("coupons",'html');

		// Push a model into the view
		$this->_model = $this->getModel( "coupons");
		try {
			$this->_view->setModel( $this->_model, true );
		} catch(Exception $e) {

		}
	}

	public function isNew($post) {
		if($post['invoicing_coupon_id'] == '') {
			return true;
		}

		return false;
	}

	public function processData($post, $isNew) {
		$post = (object)$post;
		$post->ordering = 99;
		$post->hitslimit = (int)$post->hitslimit;
		$post->userhitslimit = (int)$post->userhitslimit;
		$post->hits = (int)$post->hits;

		if($isNew) {
            $post->created_on = date('Y-m-d H:i:s');
            $post->created_by = JFactory::getUser()->id;
        } else {
            $post->modified_on = date('Y-m-d H:i:s');
            $post->modified_by = JFactory::getUser()->id;
        }

		return $post;
	}
}
