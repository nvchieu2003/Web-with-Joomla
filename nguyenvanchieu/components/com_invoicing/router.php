<?php
/**
 * @package		Invoicing
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license GNU General Public License version 3, or later
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$lang = \JFactory::getLanguage();
$lang->load("com_invoicing",JPATH_ROOT);

function InvoicingBuildRoute(&$query)
{
	$segments = array();

	// Default view
	$default = 'invoices';

	// We need to find out if the menu item link has a view param
	/*if(array_key_exists('Itemid', $query)) {
		$menu = \JFactory::getApplication()->getMenu()->getItem($query['Itemid']);
		if(!is_object($menu)) {
			$menuquery = array();
		} else {
			parse_str(str_replace('index.php?',  '',$menu->link), $menuquery); // remove "index.php?" and parse
		}
	} else {
		$menuquery = array();
	}

	// Add the view
	$newView = array_key_exists('view', $query) ? $query['view'] :
	(array_key_exists('view', $menuquery) ? $menuquery['view'] : $default);*/
	
	if (isset($query['view']) && $query['view'] != "") {
		$newView = $query['view'];
		$change = true;
		switch($newView) {
			case "message":
				if(!array_key_exists('layout', $query)) $query['layout'] = 'complete';
				if($query['layout'] == 'complete') {
					$segments[0] = \JText::_('INVOICING_SEF_THANKYOU');
				} else {
					$segments[0] = \JText::_('INVOICING_SEF_CANCELLED');
				}
				unset($query['layout']);
				break;
				
			case 'invoice':
				if(!array_key_exists('layout', $query)) $query['layout'] = 'item';
				if($query['layout'] == 'item') {
					$segments[0] = \JText::_('INVOICING_SEF_INVOICE');
				} else {
					$segments[0] = \JText::_('INVOICING_SEF_CHECKOUT');
				}
				unset($query['layout']);
				
				$segments[1] = $query['id'];
				unset($query['id']);
				break;
				
			case "payment":
				$segments[0] = \JText::_('INVOICING_SEF_PAYMENT');
	            if(isset($query['id'])) {
	                $segments[1] = $query['id'];
	                unset($query['id']);
	            }
				break;
			case 'invoices':
				$segments[0] = \JText::_('INVOICING_SEF_INVOICES');
				break;
			default:
				$change = false;
				break;
				
		}
		if ($change == true) {
			unset($query['view']);
		}
	}

	return $segments;
}

function InvoicingParseRoute(&$segments)
{
	$lang = \JFactory::getLanguage();
	$lang->load("com_invoicing");
	
    if(count($segments))
	{
        $mObject = \JFactory::getApplication()->getMenu()->getActive();
        $menu = is_object($mObject) ? $mObject->query : array();
        
        if ($segments[0] == \JText::_('INVOICING_SEF_THANKYOU')) {
            $vars['view'] = 'message';
			$vars['layout'] = 'complete'; 
        } else if ($segments[0] == \JText::_('INVOICING_SEF_CANCELLED')) {
            $vars['view'] = 'message';
			$vars['layout'] = 'cancel'; 
        }  else if ($segments[0] == \JText::_('INVOICING_SEF_INVOICE')) {
            $vars['view'] = 'invoice';
        }  else if ($segments[0] == \JText::_('INVOICING_SEF_CHECKOUT')) {
            $vars['view'] = 'invoice';
			$vars['layout'] = 'payment'; 
        }  else if ($segments[0] == \JText::_('INVOICING_SEF_PAYMENT')) {
            $vars['view'] = 'payment';
        }  else if ($segments[0] == \JText::_('INVOICING_SEF_INVOICES')) {
            $vars['view'] = 'invoices'; 
        } else {
             //defaultviews
            $default = 'invoices';
            $vars['view'] = array_key_exists('view', $menu) ? $menu['view'] : $default;
        }
        
		if (($vars['view'] == 'invoice')||($vars['view'] == 'checkout')||($vars['view'] == 'payment')) {
            if (isset($segments[1])) {
                $id = explode( ':', $segments[1] );
                $vars['id'] = (int) $id[0];
            }
		}
	}

	$segments = array();

	return $vars;
}