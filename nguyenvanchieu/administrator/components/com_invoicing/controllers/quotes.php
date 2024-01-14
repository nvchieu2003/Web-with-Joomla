<?php
/**
 *  @package Invoicing
 *  @copyright Copyright (c)203 Juloa.com
 *  @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/controllers/default.php');

class InvoicingControllerQuotes extends InvoicingControllerDefault {
	protected $accessLabel = "quote";
	protected $controllerLabel = "quotes";

	function __construct($config= array()) {
		parent::__construct($config);

		$this->_model = $this->getModel( "quotes" );
	}

	public function toInvoice() {
				$app = \JFactory::getApplication();
				$input = $app->input;

        if($input->getInt('boxchecked', 0) < 1) {
					$app->enqueueMessage(\JText::_('COM_INVOICING_SELECTION_NEEDED'), 'message');
					$app->redirect( \JRoute::_('index.php?option=com_invoicing&view=quotes&status=error'), 200 );
				} elseif($input->getInt('boxchecked', 0) > 1) {
					$app->enqueueMessage(\JText::_('COM_INVOICING_MULTI_SELECT_FORBIDDEN'), 'message');
					$app->redirect( \JRoute::_('index.php?option=com_invoicing&view=quotes&status=error'), 200 );
				}

        $quoteId = $input->get('cid', array(), "Array");
        $quoteId = $quoteId[0];

		$url = 'index.php?option=com_invoicing&view=invoice&quote='.  intval($quoteId);

		$app->redirect($url, 200);
	}

	public function isNew($post) {
		if($post['invoicing_quote_id'] == '') {
			return true;
		}

		return false;
	}

    public function mail()
	{
		// Load the model
		$model = $this->getModel();
		if(!$model->getId()) $model->setIDsFromRequest();

		$item = $model->getItem();

		$email = InvoicingHelperMail::sendQuoteMail($item);

		$app = \JFactory::getApplication();
		$url = 'index.php?option=com_invoicing&view=quotes';

		if(is_bool($email))
			$msg = \JText::_('INVOICING_COMMON_EMAIL_SENT');

		$app->enqueueMessage($msg, 'message');
		$app->redirect($url, 200);
	}
}
