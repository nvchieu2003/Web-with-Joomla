<?php
/**
* @package      Komento
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

KT::import('admin:/includes/maintenance/dependencies');

class KomentoMaintenanceScriptUpdateTableEngine extends KomentoMaintenanceScript
{
	public static $title = "Update database tables engine type";
	public static $description = "This script will attempt to update the existing Komento table engine type to follow the default engine type used on the server.";

	public function main()
	{
		$db = KT::db();

		$defaultEngine = $this->getDefaultEngineType();
		$requireConvert = $this->isRequireConvertion();

		if ($defaultEngine != 'myisam' && $requireConvert) {
			$tables = $this->getKTTables();

			if ($tables) {
				try {

					foreach ($tables as $table) {
						$query = "alter table " . $db->nameQuote($table) . " engine=InnoDB";
						$db->setQuery($query);
						$db->query();
					}
					
				} catch (Exception $err) {
					// do nothing.
				}
			}
		}

		return true;
	}

	/**
	 * Get default database table engine from mysql server
	 *
	 * @since	5.0
	 * @access	public
	 */
	private function getDefaultEngineType()
	{
		$default = 'myisam';
		$db = KT::db();

		try {

			$query = "SHOW ENGINES";
			$db->setQuery($query);

			$results = $db->loadObjectList();

			if ($results) {
				foreach ($results as $item) {
					if ($item->Support == 'DEFAULT') {
						$default = strtolower($item->Engine);
						break;
					}
				}

				if ($default != 'myisam' && $default != 'innodb') {
					$default = 'myisam';
				}
			}

		} catch (Exception $err) {
			$default = 'myisam';
		}

		return $default;
	}

	/**
	 * Determine if we need to convert myisam engine to innodb
	 *
	 * @since	5.0
	 * @access	public
	 */
	private function isRequireConvertion()
	{
		$require = false;
		$db = KT::db();

		try {
			$query = "SHOW TABLE STATUS WHERE `name` LIKE " . $db->Quote('%_komento_download');
			$db->setQuery($query);
			$result = $db->loadObject();

			if ($result) {
				$currentEngine = strtolower($result->Engine);
				if ($currentEngine == 'myisam') {
					$require = true; 
				}
			}

		} catch (Exception $err) {
			// do nothing.
			$require = false;
		}

		return $require;
	}

	/**
	 * Get Komento tables names
	 *
	 * @since	5.4
	 * @access	public
	 */
	private function getKTTables()
	{
		$tables = [];

		try {

			// for now we do the manual work.
			$tables = [
				'#__komento_acl',
				'#__komento_actions',
				'#__komento_activities',
				'#__komento_captcha',
				'#__komento_comments',
				'#__komento_configs',
				'#__komento_download',
				'#__komento_hashkeys',
				'#__komento_languages',
				'#__komento_mailq',
				'#__komento_migrators',
				'#__komento_pushq',
				'#__komento_subscription',
				'#__komento_uploads'
			];

		} catch (Exception $err) {
			// do nothing.
		}

		return $tables;
	}
}