<?php
/**
* @package		Komento
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.filter.filteroutput');
jimport('joomla.application.router');

class KTRouter
{

	/**
	 * Method to route komento links with correct Itemid
	 *
	 * @since   3.0
	 * @access  public
	 */
	public static function _($url, $xhtml = true, $ssl = null, $search = false)
	{

		// Parse the url
		parse_str($url, $query);

		$view = isset($query['view']) ? $query['view'] : 'dashboard';
		$layout = isset($query['layout']) ? $query['layout'] : null;
		$itemId = isset($query['Itemid']) ? $query['Itemid'] : '';

		if (!$itemId) {
			$menu = self::getMenus($view, $layout);

			if ($menu) {
				$url .= stristr($url, '?') ? '&Itemid=' . $menu->id : '?Itemid=' . $menu->id;
			}
		}

		return JRoute::_($url, $xhtml, $ssl);
	}


	/**
	 * Method to retrieve all the available menu items created for Komento
	 *
	 * @since   3.0
	 * @access  public
	 */
	public static function getMenus($view, $layout = null, $lang = null)
	{
		static $menus = null;
		static $selection = array();

		// Always ensure that layout is lowercased
		if (!is_null($layout)) {
			$layout = strtolower($layout);
		}

		// We want to cache the selection user made.
		// $key = $view . $layout . $id;
		$language = false;
		$languageTag = JFactory::getLanguage()->getTag();

		// If language filter is enabled, we need to get the language tag
		if (!FH::isFromAdmin()) {
			$language = JFactory::getApplication()->getLanguageFilter();
			$languageTag = JFactory::getLanguage()->getTag();
		}

		// var_dump($lang);
		if ($lang) {
			$languageTag = $lang;
		}

		$key = $view . $layout . $languageTag;

		// Preload the list of menus first.
		if (is_null($menus)) {

			// Get all relevant menu items.
			$app = JFactory::getApplication();
			$menu = $app->getMenu('site');
			$result = $menu->getItems('component', 'com_komento');

			if (!$result) {
				return $result;
			}

			$menus = array();

			$counter = 0;

			foreach ($result as $row) {

				// Remove the index.php?option=com_easyblog from the link
				$tmp = str_ireplace('index.php?option=com_komento', '', $row->link);

				// Parse the URL
				parse_str($tmp, $segments);

				// Convert the segments to std class
				$segments = (object) $segments;

				// if there is no view, most likely this menu item is a external link type. lets skip this item.
				if(!isset($segments->view)) {
					continue;
				}

				$menu = new stdClass();
				$menu->segments = $segments;

				$menu->link = $row->link;
				$menu->view = $segments->view;
				$menu->layout = isset($segments->layout) ? $segments->layout : 0;
				$menu->id = $row->id;

				// this is the safe step to ensure later we will have atlest one menu item to retrive.
				$menus[$menu->view][$menu->layout]['*'][] = $menu;
				if ($row->language != '*') {
					$menus[$menu->view][$menu->layout][$row->language][] = $menu;
				}

				// for default menu used at the bottom
				if ($counter == 0 && !isset($menus[$menu->view][0]['*'])) {
					$menus[$menu->view][0]['*'][] = $menu;
				}

				$counter++;
			}

		}


		// Get the current selection of menus from the cache
		if (!isset($selection[$key])) {

			// Search for $view only. Does not care about layout
			if (isset($menus[$view]) && $menus[$view] && (is_null($layout) || !$layout)) {
				if (isset($menus[$view][0][$languageTag])) {
					$selection[$key] = $menus[$view][0][$languageTag];
				} else if (isset($menus[$view][0]['*'])) {
					$selection[$key] = $menus[$view][0]['*'];

				} else {
					$selection[$key] = false;
				}
			}

			// Search for $view and $layout.
			if (isset($menus[$view]) && $menus[$view] && !is_null($layout) && $layout) {

				$layoutMenu = null;

				if (isset($menus[$view][$layout])) {
					$layoutMenu = $menus[$view][$layout];
				} else if (isset($menus[$view])) {
					$layoutMenu = $menus[$view][0];
				}

				if ($layoutMenu && isset($layoutMenu[$languageTag])) {
					$selection[$key] = $layoutMenu[$languageTag];
				} else if ($layoutMenu && isset($layoutMenu['*'])) {
					$selection[$key] = $layoutMenu['*'];
				} else {
					$selection[$key] = false;
				}
			}

			// If there is no menu item for such view, lets take any menu item belong to komento.
			if (!isset($selection[$key])) {
				$arrKeys = array_keys($menus);
				$first = $menus[$arrKeys[0]][0]['*'];
				if ($first) {
					$selection[$key] = $first;
				}
			}

			// If we still can't find any menu, skip this altogether.
			if (!isset($selection[$key])) {
				$selection[$key] = false;
			}

			// Flatten the array so that it would be easier for the caller.
			if (is_array($selection[$key])) {
				$selection[$key] = $selection[$key][0];
			}
		}

		return $selection[$key];
	}




	public function getFeedUrl($component = 'all', $cid = 'all', $userid = '')
	{
		$link = 'index.php?option=com_komento&view=rss';

		if ($component != 'all') {
			$link .= '&component=' . $component;
		}

		if ($cid != 'all') {
			$link .= '&cid=' . $cid;
		}

		if ($userid != '') {
			$link .= '&userid=' . $userid;
		}

		return self::_($link) . '&format=feed';
	}

	/**
	 * Determine if the filter translated name match with the original filter name
	 *
	 * @since   3.1.3
	 * @access  public
	 */
	public static function getOriginalFilterName($str, $type = 'filter')
	{
		// For the user dashboard page only has 4 filters
		$filters = [
			'all' => JText::_('COM_KT_DASHBOARD_FILTER_ALL'),
			'pending' => JText::_('COM_KT_DASHBOARD_FILTER_PENDING'),
			'spam' => JText::_('COM_KT_DASHBOARD_FILTER_SPAM'),
			'reports' => JText::_('COM_KT_DASHBOARD_FILTER_REPORTS')
		];

		foreach ($filters as $key => $translatedValue) {
			
			if ($translatedValue == $str) {
				return $key;
			}
		}

		return $str;
	}	
}
