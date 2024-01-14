<?php
/**
 * @package		Invoicing
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

class InvoicingViewDefault extends \JViewLegacy {
	function __construct($config = array())
	{
		parent::__construct($config);

		$uri = JUri::getInstance();
		$baseurl = JURI::base();
		$baseurl = str_replace("administrator/","",$baseurl);
		
		$user		= JFactory::getUser();
		
		$this->userid = $user->id;
		$this->baseurl = $baseurl;
		
		if(version_compare(JVERSION, '3.0', 'ge')) {
			$js = "checkAll = Joomla.checkAll;";
			$js .= "isChecked = Joomla.isChecked;";
			$doc = JFactory::getDocument();
			$doc->addScriptDeclaration($js);
		}
	}

	function hasAjaxOrderingSupport() {
		return true;
	}

	function onBeforeAdd($tpl = null) {
		return true;
	}

	protected function setFilters() {
		return array();
	}

	function display($tpl = null) {
		$app = JFactory::getApplication();

		$task = $app->input->getCmd('task');
		
		if($task == 'edit') {
			$task = 'add';
		}

		if(method_exists($this, 'onBefore'.ucfirst($task))) {
			$functionName = 'onBefore'.ucfirst($task);
			$this->$functionName($tpl);
		}

		if($task == 'add') {
			$this->add($tpl);
		} else {
			$this->setContentsToolbar(JText::_("COM_INVOICING")." - ".JText::_($this->title));

			$this->filters = $this->setFilters();

			$limit			  = $app->getUserStateFromRequest('global.list.limit','limit', $app->getCfg('list_limit'),'int');
			$limitstart		  = $app->getUserStateFromRequest( "com_invoicing.field.limitstart",'limitstart',0,'int');
			$filter_order     = $app->getUserStateFromRequest( 'com_invoicing.field.filter_order','filter_order','f.ordering','cmd' );
			$filter_order_Dir = $app->getUserStateFromRequest( 'com_invoicing.field.filter_order_Dir','filter_order_Dir', 'ASC','word' );

			$model = $this->getModel();
			$this->items = $model->getItems($this->filters);
			$this->total = $model->getNbItems($this->filters);
			$this->lists = new \stdClass();
			$this->lists->order = $filter_order;
			$this->lists->order_Dir = $filter_order_Dir;

			$pagination = new JPagination($this->total, $limitstart, $limit);

			$this->pagination = $pagination;

			parent::display($tpl);
		}
	}

	function add($tpl = null) {
		$app = JFactory::getApplication();

		$this->setEditToolbar(JText::_("COM_INVOICING")." - ".JText::_($this->title."_EDIT"), '');

		$cid = $app->input->get('cid', 0);
		if(is_array($cid)) {
			$cid = $cid[0];
		}

		$model = $this->getModel();
		$item = $model->getItem($cid);
		
		$this->item = $item;

		parent::display('form');
	}

	function setContentsToolbar($title) {
		//Access Right
		$user = JFactory::getUser();

		//To remove when the ACL will be written in details
		$canCreate = true;
		$canDelete = true;
		$canEdit = true;
		$canPublish = true;
		$canUnpublish = true;
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

	function setEditToolbar($title, $type = '')
	{
		//Access Right
		$user = JFactory::getUser();
		if($type != '') {
			$canEdit = $user->authorise('invoicing.edit'.$type, 'com_invoicing');
		} else {
			$canEdit = true;
		}

		JToolBarHelper::title( $title, 'invoicing' );
		if($canEdit) {
			JToolBarHelper::apply();
			JToolBarHelper::save();
			JToolBarHelper::save2new();
		}
		JToolBarHelper::cancel();
        $bar = JToolBar::getInstance('toolbar');
		$label = 'JTOOLBAR_HELP';
		$bar->appendButton( 'Link', 'help', $label, JRoute::_('index.php?option=com_invoicing&c=doc') );
    }
}