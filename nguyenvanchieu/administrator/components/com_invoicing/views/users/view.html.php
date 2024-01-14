<?php
/**
 * @package		Invoicing
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license GNU General Public License version 3, or later
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/views/default/view.html.php');

/**
 * 
 * @package		Joomla
 * @subpackage	Contacts
 */
class InvoicingViewUsers extends InvoicingViewDefault
{
  public $title = "INVOICING_USERS";

  public function onBeforeAdd($tpl = null) {
    parent::onBeforeAdd($tpl);

    $input = \JFactory::getApplication()->input;
    $returnurl = $input->get('returnurl','', "String");
    $this->returnurl = $returnurl; 
  }

  protected function setFilters() {
		$app = \JFactory::getApplication();
    
		$filters = array();
		$filters['search'] = $app->getUserStateFromRequest( 'com_invoicing.user.search','search', '','string' );
		$filters['city'] = $app->getUserStateFromRequest( 'com_invoicing.user.city','city', '','word' );
		$filters['zip'] = $app->getUserStateFromRequest( 'com_invoicing.user.zipo','zip', '','string' );
		$filters['country'] = $app->getUserStateFromRequest( 'com_invoicing.user.country','country', '','word' );

		return $filters;
	}
}