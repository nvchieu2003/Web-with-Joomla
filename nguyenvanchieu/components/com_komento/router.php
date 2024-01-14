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

require_once(JPATH_ROOT . '/components/com_komento/bootstrap.php');


/**
 * Proxy layer to support Joomla 4.0 and Joomla 3.0
 *
 * @since  4.0.6
 */
class KomentoRouterBase
{
	public static function buildRoute(&$query)
	{
		// Declare static variables.
		static $items;
		static $default;
		static $dashboard;
		static $subscriptions;

		// Initialise variables.
		$segments = array();
		$config	= KT::config();

		// Get the relevant menu items if not loaded.
		if (empty($items)) {

			// Get all relevant menu items.
			$app = JFactory::getApplication();
			$menu = $app->getMenu('site');
			$items = $menu->getItems('component', 'com_komento');

			// Build an array of serialized query strings to menu item id mappings.
			for ($i = 0, $n = count($items); $i < $n; $i++) {

				// Check to see if we have found the dashboard menu item.
				if (empty($dashboard) && !empty($items[$i]->query['view']) && ($items[$i]->query['view'] == 'dashboard')) {
					$dashboard = $items[$i]->id;
				}

				// Check to see if we have found the subscriptions menu item.
				if (empty($subscriptions) && !empty($items[$i]->query['view']) && ($items[$i]->query['view'] == 'subscriptions')) {
					$subscriptions = $items[$i]->id;
				}
			}
		}

		if (!empty($query['view'])) {

			if (!isset($query['Itemid'])) {
				// Set menu item directly with the view as the variable string
				// Profile link should be generated with $profile item id
				// If the view is 'profile', then itemid shouhld be set with $profile
				// If the view is 'subscriptions', then the itemid should be set with $subscriptions
				if (isset(${$query['view']})) {
					$query['Itemid'] = ${$query['view']};
				}
			}

			switch ($query['view']) {

				case 'subscriptions':

					if (is_null($subscriptions)) {
						$segments[] = 'subscriptions';
						unset($query['view']);
					}

					if (isset($query['Itemid']) && $query['Itemid'] == $subscriptions) {
						unset ($query['view']);
					}
					break;
				
				default:
				case 'dashboard':

					if (is_null($dashboard)) {
						$segments[] = 'dashboard';
						unset($query['view']);
					}

					if (isset($query['Itemid']) && $query['Itemid'] == $dashboard) {
						unset($query['view']);
					}

					// Translate filter urls
					// $filter = isset($query['filter']) ? $query['filter'] : null;
					// $addFilter = !is_null($filter) ? true : false;

					// if ($addFilter) {
					// 	$filterType = FCJString::strtoupper($query['filter']);
					// 	$segments[] = JText::_('COM_KT_DASHBOARD_FILTER_' . $filterType);
					// }

					// unset($query['filter']);

					// Layout download
					$layout = isset($query['layout']) ? $query['layout'] : null;

					if ($layout) {
						$segments[] = $layout;
					}

					unset($query['layout']);

					break;
			}
		}

		return $segments;
	}

	public static function parseRoute(&$segments)
	{
		// Initialise variables.
		$vars = array();
		$app = JFactory::getApplication();
		$menu = $app->getMenu('site');
		$item = $menu->getActive();
		$total = count($segments);

		// Only run routine if there are segments to parse.
		if ($total < 1) {
			return;
		}

		if (!isset($item)) {
			$vars['view'] = $segments[0];
		} else {
			$vars['view'] = $item->query['view'];
		}

		if ($total == 1) {
			if ($segments[0] == 'dashboard') {
				$vars['view'] = 'dashboard';
			}

			if ($segments[0] == 'subscriptions') {
				$vars['view'] = 'subscriptions';
			}

			if ($segments[0] == 'download' || $segments[0] == 'downloaddata') {
				$vars['view'] = 'dashboard';
				$vars['layout'] = $segments[0];
			}

			// Determine if the filter translated name match with the original filter name
			$filterName = KT::router()->getOriginalFilterName($segments[0]);
			if ($filterName) {
				$vars['view'] = 'dashboard';
				$vars['filter'] = $filterName;
			}

		}

		if ($total > 1) {

			$vars['view'] = $segments[0];

			if ($segments[1] == 'download' || $segments[1] == 'downloaddata') {
				$vars['view'] = 'dashboard';
				$vars['layout'] = $segments[1];
			} else {

				// Determine if the filter translated name match with the original filter name
				$filterName = KT::router()->getOriginalFilterName($segments[1]);
				$vars['view'] = 'dashboard';
				$vars['filter'] = $filterName;
			}
		}

		// if ($total == 1 && isset($vars['view']) && $vars['view'] == 'dashboard') {
		// 	if ($segments[0] == 'download' || $segments[0] == 'downloaddata') {
		// 		$vars['view'] = 'dashboard';
		// 		$vars['layout'] = $segments[0];
		// 	} else {

		// 		// Determine if the filter translated name match with the original filter name
		// 		$filterName = KT::router()->getOriginalFilterName($segments[0]);
		// 		$vars['filter'] = $filterName;
		// 	}
		// }

		// var_dump($vars);exit;

		return $vars;
	}

}


if (FH::isJoomla4()) {

	class KomentoRouter extends Joomla\CMS\Component\Router\RouterBase
	{
		public function build(&$query)
		{
			$segments = KomentoRouterBase::buildRoute($query);
			return $segments;
		}

		public function parse(&$segments)
		{
			$vars = KomentoRouterBase::parseRoute($segments);

			// look like we have to manually reset the segments so that we will not hit this error:
			// Uncaught Joomla\CMS\Router\Exception\RouteNotFoundException: URL invalid in /libraries/src/Router/Router.php on line 152
			$segments = array();

			return $vars;
		}
	}
}



// Routing methods to support J3
function KomentoBuildRoute(&$query)
{
	$segments = KomentoRouterBase::buildRoute($query);
	return $segments;
}

function KomentoParseRoute(&$segments)
{
	$vars = KomentoRouterBase::parseRoute($segments);
	return $vars;
}

