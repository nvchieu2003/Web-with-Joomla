<?php
/**
 * @package		Invoicing
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/cparams.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/format.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/dates.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/views/default/view.html.php');

class InvoicingViewTemplates extends InvoicingViewDefault
{
	public $title = "INVOICING_TEMPLATES";

	function setContentsToolbar($title) {
		//Access Right
		$user = JFactory::getUser();

		//To remove when the ACL will be written in details
		$canCreate = false;
		$canDelete = false;
		$canEdit = true;
		$canPublish = false;
		$canUnpublish = false;
		$canDuplicate = false;

		JToolBarHelper::title( $title, 'invoicing' );
		if($canCreate) {
			JToolBarHelper::addNew();
		}
		if($canEdit) {
			JToolBarHelper::editList();
		}
		if($canPublish) {
			JToolBarHelper::publishList();
		}
		if($canUnpublish) {
			JToolBarHelper::unpublishList();
		}
		if($canDuplicate) {
			JToolbarHelper::custom('duplicate', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
		}
		if($canDelete) {
			JToolBarHelper::deleteList();
		}
		$bar = JToolBar::getInstance('toolbar');
		$label = 'JTOOLBAR_HELP';
		$bar->appendButton( 'Link', 'help', $label, JRoute::_('index.php?option=com_invoicing&c=doc') );
        
		if (JFactory::getUser()->authorise('core.admin', 'com_invoicing')) {
			JToolBarHelper::preferences('com_invoicing');
		}
	}
}
