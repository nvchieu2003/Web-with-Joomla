<?php
/**
 * @package		Invoicing
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_COMPONENT.'/lib/core.php');

// Require the com_content helper library
require_once(JPATH_COMPONENT.'/controller.php');

// Component Helper
jimport('joomla.application.component.helper');

// Create the controller
$controller = new InvoicingController();

// Perform the Request task
$controller->execute(JFactory::getApplication()->input->getCmd('task'));

include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/cparams.php');

$document = \JFactory::getDocument();
      
$loadBootstrap = (int)InvoicingHelperCparams::getParam('bootstrap_loading', '1');
if($loadBootstrap == 1)
\JuloaLib::loadCSS('bootstrap2');
\JuloaLib::loadJquery();

$document = \JFactory::getDocument();
$document->addStyleSheet(\JURI::root().'media/com_invoicing/css/frontend.css');

// Default to the "levels" view
$view = JFactory::getApplication()->input->get('view','invoices');
if(empty($view) || ($view == 'cpanel') || ($view == 'cpanels')) {
  $view = 'invoices';
}
      
// Set the view, if it's allowed
$url = \JUri::getInstance();
$url->setVar('view', $view);
 
// Redirect if set by the controller
$controller->redirect();