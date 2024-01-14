<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

if (version_compare(JVERSION,'1.6','>=')) {
    //ACL
    if (!JFactory::getUser()->authorise('core.manage', 'com_adsmanager')) {
        throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
    }
}

// Make sure the user is authorised to view this page
$user = JFactory::getUser();

// Component Helper
jimport('joomla.application.component.helper');

require_once(JPATH_ROOT."/components/com_invoicing/lib/core.php");

$input = JFactory::getApplication()->input;
if($input->getCmd( 'c' ) === null && $input->getCmd('view') !== null) {
	$input->set('c', $input->getCmd('view'));
}
$controllerName = $input->getCmd( 'c', 'cpanels' );

require_once( JPATH_COMPONENT."/controllers/$controllerName.php" );
$controllerName = 'InvoicingController'.ucfirst($controllerName);

$lang = JFactory::getLanguage();
$lang->load("com_invoicing",JPATH_ROOT);

// Create the controller
$controller = new $controllerName();

/*if(version_compare(JVERSION,'1.6.0','>=')){
	JHtml::_('jquery.framework');
	if($user->authorise('adsmanager.accessconfiguration','com_adsmanager')) {
		JHtmlSidebar::addEntry(JText::_('COM_ADSMANAGER_CONFIGURATION'), 'index.php?option=com_adsmanager&amp;c=configuration');
	}
	if($user->authorise('adsmanager.accessfield','com_adsmanager')) {
		JHtmlSidebar::addEntry(JText::_('COM_ADSMANAGER_FIELDS'), 'index.php?option=com_adsmanager&amp;c=fields');
	}
	if($user->authorise('adsmanager.accesslayoutcontentform','com_adsmanager')) {
		JHtmlSidebar::addEntry(JText::_('COM_ADSMANAGER_CONTENT_FORM'), 'index.php?option=com_adsmanager&amp;c=contentform');
	}
	if($user->authorise('adsmanager.accesslayoutlist','com_adsmanager')) {
		JHtmlSidebar::addEntry(JText::_('COM_ADSMANAGER_COLUMNS'), 'index.php?option=com_adsmanager&amp;c=columns');
	}
	if($user->authorise('adsmanager.accesspositiondetails','com_adsmanager')) {
		JHtmlSidebar::addEntry(JText::_('COM_ADSMANAGER_AD_DISPLAY'), 'index.php?option=com_adsmanager&amp;c=positions');
	}
	if($user->authorise('adsmanager.accesscategory','com_adsmanager')) {
		JHtmlSidebar::addEntry(JText::_('COM_ADSMANAGER_CATEGORIES'), 'index.php?option=com_adsmanager&amp;c=categories');
	}
	if($user->authorise('adsmanager.accesscontent','com_adsmanager')) {
		JHtmlSidebar::addEntry(JText::_('COM_ADSMANAGER_CONTENTS'), 'index.php?option=com_adsmanager&amp;c=contents');
	}
	if($user->authorise('adsmanager.accessplugin','com_adsmanager')) {
		JHtmlSidebar::addEntry(JText::_('COM_ADSMANAGER_PLUGINS'), 'index.php?option=com_adsmanager&amp;c=plugins');
	}
	if($user->authorise('adsmanager.accessfieldimage','com_adsmanager')) {
		JHtmlSidebar::addEntry(JText::_('COM_ADSMANAGER_FIELD_IMAGES'), 'index.php?option=com_adsmanager&amp;c=fieldimages');
	}
	if($user->authorise('adsmanager.accesssearchmodule','com_adsmanager')) {
		JHtmlSidebar::addEntry(JText::_('COM_ADSMANAGER_SEARCH_MODULE'), 'index.php?option=com_adsmanager&amp;c=searchmodule');
	}
	if($user->authorise('adsmanager.accesssearchpage','com_adsmanager')) {
		JHtmlSidebar::addEntry(JText::_('COM_ADSMANAGER_SEARCH_PAGE'), 'index.php?option=com_adsmanager&amp;c=searchpage');
	}
	if($user->authorise('adsmanager.accessmail','com_adsmanager')) {
		JHtmlSidebar::addEntry(JText::_('COM_ADSMANAGER_MAILS'), 'index.php?option=com_adsmanager&amp;c=mails');
	}
	if($user->authorise('adsmanager.accessexport','com_adsmanager')) {
		JHtmlSidebar::addEntry(JText::_('COM_ADSMANAGER_EXPORT'), 'index.php?option=com_adsmanager&amp;c=export');
	}
}	*/
// Admin panel style
$document = JFactory::getApplication()->getDocument();
$document->addStyleSheet(JUri::root().'media/com_invoicing/css/backend.css');
$document->addStyleSheet(JUri::base().'components/com_invoicing/css/admin_invoicing_panel.css');

/*Jquery non conflict mode*/
require_once JPATH_ROOT.'/libraries/juloalib/Lib.php';
$document = \JFactory::getDocument();
			
\JuloaLib::loadCSS('bootstrap2');
\JuloaLib::loadJquery();
\JuloaLib::loadJqueryUI();
//\FOFTemplateUtils::addCSS('media://com_invoicing/css/frontend.css');
//\FOFTemplateUtils::addCSS('media://com_invoicing/css/backend.css');

// Perform the Request task
$controller->execute($input->getCmd('task', null));
$controller->redirect();


echo "<br/><div align='center'><i>Invoicing 3.2</i></div>";