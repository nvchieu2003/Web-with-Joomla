<?php
/**
 *  @package	AdsManager
 *  JFile::copyright	Copyright (c)2010-2014 Thomas Papin / Juloa.com
 *  @license	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 *  @version 	$Id$
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// no direct access
defined('_JEXEC') or die();

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

jimport( 'joomla.error.error' );

class Com_InvoicingInstallerScript
{
	/** @var string The component's name */
	protected $_adsmanager_extension = 'com_invoicing';

	/** @var array The list of extra modules and plugins to install */
	private $installation_queue = array(
		// modules => { (folder) => { (module) => { (position), (published) } }* }*
		'modules' => array(
			'admin' => array(
				
			),
			'site' => array(
				
			)
		),
		'plugins' => array(
			'invoicingpayment' => array(
				'allopass'	=> 0,
				'atos'	=> 0,
				'authorizenet'	=> 0,
				'epaydk'	=> 0,
				'hipay'	=> 0,
				'midtrans' => 0,
				'offline'	=> 1,
				'offline2' => 0,
				'paybox'	=> 0,
				'paypal' => 1,
				'stripe'	=> 0,
				'systempay' => 0
			)
			,'system' => array(
				'invoicing_pluginsmanager' => 0
			)
		)
	);

	private $invoicingRemovePlugins = array(
		'' => array(
		)
	);

	/** @var array Obsolete files and folders to remove */
	private $invoicingRemoveFiles = array(
		'files'	=> array(
			/*'administrator/components/com_invoicing/admin.invoicing.php',
			'administrator/components/com_invoicing/admin.invoicing.html.php'*/
		),
		'folders' => array(
			/*'administrator/components/com_invoicing/commands',*/
		)
	);

	private $invoicingCliScripts = array();

	/**
	 * Joomla! pre-flight event
	 *
	 * @param string $type Installation type (install, update, discover_install)
	 * @param JInstaller $parent Parent object
	 */
	public function preflight($type, $parent)
	{
		
		// Bugfix for "Can not build admin menus"
		if(in_array($type, array('install','discover_install'))) {
			$this->_bugfixDBFunctionReturnedNoError();
		} else {
			$this->_bugfixCantBuildAdminMenus();
		}
		
		// Only allow to install on Joomla! 2.5.1 or later
		if(!version_compare(JVERSION, '2.5.1', 'ge')) {
			echo "<h1>Unsupported Joomla! version</h1>";
			echo "<p>This component can only be installed on Joomla! 2.5.1 or later</p>";
			return false;
		}
		
		return true;
	}

	function install($parent) { 
		$this->_updateDatabase($parent); 
	}
	function update($parent) { 
		$this->_updateDatabase($parent); 
	}



	/**
	 * Runs after install, update or discover_update
	 * @param string $type install, update or discover_update
	 * @param JInstaller $parent
	 */
	function postflight( $type, $parent )
	{
		// Install subextension
		$status = $this->_installSubextensions($parent);

		// Remove obsolete files and folders
		$invoicingRemoveFiles = $this->invoicingRemoveFiles;
		$this->_removeObsoleteFilesAndFolders($invoicingRemoveFiles);

		$this->_copyCliFiles($parent);

		// Remove Professional version plugins from Akeeba Backup Core
		$this->_removeObsoletePlugins($parent);

		$juloaLibStatus = $this->_installJuloaLib($parent);
		$fofStatus = 0;

		// Show the post-installation page
		$this->_renderPostInstallation($status, $fofStatus, $juloaLibStatus, $parent);

		// update site
		$this->_updateSite($parent);
	}

	/**
	 * Runs on uninstallation
	 *
	 * @param JInstaller $parent
	 */
	function uninstall($parent)
	{
		
		// Uninstall subextensions
		$status = $this->_uninstallSubextensions($parent);

		// Show the post-uninstallation page
		$this->_renderPostUninstallation($status, $parent);
	}

	/**
	 * Removes the plugins which have been discontinued
	 *
	 * @param JInstaller $parent
	 */
	private function _removeObsoletePlugins($parent)
	{
		
		if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
			$src = $parent->getParent()->getPath('source');
		} else {
			$src = $parent->getPath('source');
		}
		$db = JFactory::getDbo();

		foreach($this->invoicingRemovePlugins as $folder => $plugins) {
			foreach($plugins as $plugin) {
				$sql = $db->getQuery(true)
					->select($db->qn('extension_id'))
					->from($db->qn('#__extensions'))
					->where($db->qn('type').' = '.$db->q('plugin'))
					->where($db->qn('element').' = '.$db->q($plugin))
					->where($db->qn('folder').' = '.$db->q($folder));
				$db->setQuery($sql);
				$id = $db->loadResult();
				if($id)
				{
					$installer = new JInstaller;
					$result = $installer->uninstall('plugin',$id,1);
				}
			}
		}
	}

	/**
	 * Copies the CLI scripts into Joomla!'s cli directory
	 *
	 * @param JInstaller $parent
	 */
	private function _copyCliFiles($parent)
	{
		
		if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
			$src = $parent->getParent()->getPath('source');
		} else {
			$src = $parent->getPath('source');
		}

		jimport("joomla.filesystem.file");
		jimport("joomla.filesystem.folder");

		if(empty($this->invoicingCliScripts)) {
			return;
		}

		foreach($this->invoicingCliScripts as $script) {
			if(JFile::exists(JPATH_ROOT.'/cli/'.$script)) {
				JFile::delete(JPATH_ROOT.'/cli/'.$script);
			}
			if(JFile::exists($src.'/cli/'.$script)) {
				JFile::copy($src.'/cli/'.$script, JPATH_ROOT.'/cli/'.$script);
			}
		}
	}

	/**
	 * Renders the post-installation message
	 */
	private function _renderPostInstallation($status, $fofStatus, $juloaLibStatus, $parent)
	{
		
?>
<?php if (!version_compare(PHP_VERSION, '5.3.0', 'ge')): ?>
	<div style="margin: 1em; padding: 1em; background: #ffff00; border: thick solid red; color: black; font-size: 14pt;" id="notfixedperms">
		<h1 style="margin: 1em 0; color: red; font-size: 22pt;">OUTDATED PHP VERSION</h1>
		<p>You are using an outdated version of PHP which is not properly supported by Juloa.com. Please upgrade to PHP 5.3 or later as soon as possible. Future versions of our software will not work at all on PHP 5.2.</p>
	</div>
<?php endif; ?>
<h1>Invoicing</h1>
<?php $rows = 1;?>
<img src="../media/com_invoicing/images/logofull.png" alt="Invoicing" align="left" />
<h2 style="font-size: 14pt; font-weight: black; padding: 0; margin: 0 0 0.5em;">Welcome to Invoicing!</h2>
<span>The easiest way to manage quotes, orders and invoices on your Joomla! site</span>

<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            This beta version only support Joomla4, if you see an issue or a bug, please report it to <a href="https://www.joomprod.com/support/support-tickets.html" target="_blank">https://www.joomprod.com/support/support-tickets.html</a> (You need to log in to add a ticket).
			<br/>
			When creating a ticket for this version of Invoicing, please prefix the ticket by [JOOMLA4].
			<br/><br/>
			For now, only new installation has been tested, the update from Joomla3 will be tested next.
			<br/>
			For the payment plugins, only paypal and the offlines ones have been tested.
			The others should work well, but issues might happen.
        </div>
    </div>
</div>

<table class="adminlist table table-striped" width="100%">
	<thead>
		<tr>
			<th class="title" colspan="2">Extension</th>
			<th width="30%">Status</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="3"></td>
		</tr>
	</tfoot>
	<tbody>
		<tr class="row0">
			<td class="key" colspan="2">
				<img src="../media/com_invoicing/images/logo.png" width="16" height="16" alt="Invoicing" align="left" />
				&nbsp;
				Invoicing component
			</td>
			<td><strong style="color: green">Installed</strong></td>
		</tr>
    <tr class="row1">
			<td class="key" colspan="2">
				<strong>Juloa Lib<?php echo $juloaLibStatus['version']?></strong> [<?php echo $juloaLibStatus['date'] ?>]
			</td>
			<td><strong>
				<span style="color: <?php echo $juloaLibStatus['required'] ? ($juloaLibStatus['installed']?'green':'red') : '#660' ?>; font-weight: bold;">
					<?php echo $juloaLibStatus['required'] ? ($juloaLibStatus['installed'] ?'Installed':'Not Installed') : 'Already up-to-date'; ?>
				</span>
			</strong></td>
		</tr>
		<?php $rows++;?>
		<?php if (count($status->modules)) : ?>
		<tr>
			<th>Module</th>
			<th>Client</th>
			<th></th>
		</tr>
		<?php foreach ($status->modules as $module) : ?>
		<tr class="row<?php echo ($rows++ % 2); ?>">
			<td class="key"><?php echo $module['name']; ?></td>
			<td class="key"><?php echo ucfirst($module['client']); ?></td>
			<td><strong style="color: <?php echo ($module['result'])? "green" : "red"?>"><?php echo ($module['result'])?'Installed':'Not installed'; ?></strong></td>
		</tr>
		<?php endforeach;?>
		<?php endif;?>
		<?php if (count($status->plugins)) : ?>
		<tr>
			<th>Plugin</th>
			<th>Group</th>
			<th></th>
		</tr>
		<?php foreach ($status->plugins as $plugin) : ?>
		<tr class="row<?php echo ($rows++ % 2); ?>">
			<td class="key"><?php echo ucfirst($plugin['name']); ?></td>
			<td class="key"><?php echo ucfirst($plugin['group']); ?></td>
			<td><strong style="color: <?php echo ($plugin['result'])? "green" : "red"?>"><?php echo ($plugin['result'])?'Installed':'Not installed'; ?></strong></td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>
	</tbody>
</table>
<?php
	}

	private function _renderPostUninstallation($status, $parent) {
		
?>
<?php $rows = 0;?>
<h2 style="font-size: 14pt; font-weight: black; padding: 0; margin: 0 0 0.5em;">&nbsp;Invoicing Uninstallation</h2>
<p>We are sorry that you decided to uninstall Invoicing. Please let us know why by using the Contact Us form on our site. We appreciate your feedback; it helps us develop better software!</p>

<table class="adminlist table table-striped"  width="100%">
	<thead>
		<tr>
			<th class="title" colspan="2"><?php echo JText::_('Extension'); ?></th>
			<th width="30%"><?php echo JText::_('Status'); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="3"></td>
		</tr>
	</tfoot>
	<tbody>
		<tr class="row0">
			<td class="key" colspan="2"><?php echo 'Invoicing '.JText::_('Component'); ?></td>
			<td><strong style="color: green"><?php echo JText::_('Removed'); ?></strong></td>
		</tr>
		<?php if (count($status->modules)) : ?>
		<tr>
			<th><?php echo JText::_('Module'); ?></th>
			<th><?php echo JText::_('Client'); ?></th>
			<th></th>
		</tr>
		<?php foreach ($status->modules as $module) : ?>
		<tr class="row<?php echo (++ $rows % 2); ?>">
			<td class="key"><?php echo $module['name']; ?></td>
			<td class="key"><?php echo ucfirst($module['client']); ?></td>
			<td><strong style="color: <?php echo ($module['result'])? "green" : "red"?>"><?php echo ($module['result'])?JText::_('Removed'):JText::_('Not removed'); ?></strong></td>
		</tr>
		<?php endforeach;?>
		<?php endif;?>
		<?php if (count($status->plugins)) : ?>
		<tr>
			<th><?php echo JText::_('Plugin'); ?></th>
			<th><?php echo JText::_('Group'); ?></th>
			<th></th>
		</tr>
		<?php foreach ($status->plugins as $plugin) : ?>
		<tr class="row<?php echo (++ $rows % 2); ?>">
			<td class="key"><?php echo ucfirst($plugin['name']); ?></td>
			<td class="key"><?php echo ucfirst($plugin['group']); ?></td>
			<td><strong style="color: <?php echo ($plugin['result'])? "green" : "red"?>"><?php echo ($plugin['result'])?JText::_('Removed'):JText::_('Not removed'); ?></strong></td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>
	</tbody>
</table>
<?php
	}

	/**
	 * Joomla! 1.6+ bugfix for "DB function returned no error"
	 */
	private function _bugfixDBFunctionReturnedNoError()
	{
		
		$db = JFactory::getDbo();

		// Fix broken #__assets records
		$query = $db->getQuery(true);
		$query->select('id')
			->from('#__assets')
			->where($db->qn('name').' = '.$db->q($this->_adsmanager_extension));
		$db->setQuery($query);
		$ids = $db->loadColumn();
		if(!empty($ids)) foreach($ids as $id) {
			$query = $db->getQuery(true);
			$query->delete('#__assets')
				->where($db->qn('id').' = '.$db->q($id));
			$db->setQuery($query);
			$db->execute();
		}

		// Fix broken #__extensions records
		$query = $db->getQuery(true);
		$query->select('extension_id')
			->from('#__extensions')
			->where($db->qn('element').' = '.$db->q($this->_adsmanager_extension));
		$db->setQuery($query);
		$ids = $db->loadColumn();
		if(!empty($ids)) foreach($ids as $id) {
			$query = $db->getQuery(true);
			$query->delete('#__extensions')
				->where($db->qn('extension_id').' = '.$db->q($id));
			$db->setQuery($query);
			$db->execute();
		}

		// Fix broken #__menu records
		$query = $db->getQuery(true);
		$query->select('id')
			->from('#__menu')
			->where($db->qn('type').' = '.$db->q('component'))
			->where($db->qn('menutype').' = '.$db->q('main'))
			->where($db->qn('link').' LIKE '.$db->q('index.php?option='.$this->_adsmanager_extension));
		$db->setQuery($query);
		$ids = $db->loadColumn();
		if(!empty($ids)) foreach($ids as $id) {
			$query = $db->getQuery(true);
			$query->delete('#__menu')
				->where($db->qn('id').' = '.$db->q($id));
			$db->setQuery($query);
			$db->execute();
		}
	}

	/**
	 * Joomla! 1.6+ bugfix for "Can not build admin menus"
	 */
	private function _bugfixCantBuildAdminMenus()
	{
		
		$db = JFactory::getDbo();

		// If there are multiple #__extensions record, keep one of them
		$query = $db->getQuery(true);
		$query->select('extension_id')
			->from('#__extensions')
			->where($db->qn('element').' = '.$db->q($this->_adsmanager_extension));
		$db->setQuery($query);
		$ids = $db->loadColumn();
		if(count($ids) > 1) {
			asort($ids);
			$extension_id = array_shift($ids); // Keep the oldest id

			foreach($ids as $id) {
				$query = $db->getQuery(true);
				$query->delete('#__extensions')
					->where($db->qn('extension_id').' = '.$db->q($id));
				$db->setQuery($query);
				$db->execute();
			}
		}

		// If there are multiple assets records, delete all except the oldest one
		$query = $db->getQuery(true);
		$query->select('id')
			->from('#__assets')
			->where($db->qn('name').' = '.$db->q($this->_adsmanager_extension));
		$db->setQuery($query);
		$ids = $db->loadObjectList();
		if(count($ids) > 1) {
			asort($ids);
			$asset_id = array_shift($ids); // Keep the oldest id

			foreach($ids as $id) {
				$query = $db->getQuery(true);
				$query->delete('#__assets')
					->where($db->qn('id').' = '.$db->q($id));
				$db->setQuery($query);
				$db->execute();
			}
		}

		// Remove #__menu records for good measure!
		$query = $db->getQuery(true);
		$query->select('id')
			->from('#__menu')
			->where($db->qn('type').' = '.$db->q('component'))
			->where($db->qn('menutype').' = '.$db->q('main'))
			->where($db->qn('link').' LIKE '.$db->q('index.php?option='.$this->_adsmanager_extension));
		$db->setQuery($query);
		$ids1 = $db->loadColumn();
		if(empty($ids1)) $ids1 = array();
		$query = $db->getQuery(true);
		$query->select('id')
			->from('#__menu')
			->where($db->qn('type').' = '.$db->q('component'))
			->where($db->qn('menutype').' = '.$db->q('main'))
			->where($db->qn('link').' LIKE '.$db->q('index.php?option='.$this->_adsmanager_extension.'&%'));
		$db->setQuery($query);
		$ids2 = $db->loadColumn();
		if(empty($ids2)) $ids2 = array();
		$ids = array_merge($ids1, $ids2);
		if(!empty($ids)) foreach($ids as $id) {
			$query = $db->getQuery(true);
			$query->delete('#__menu')
				->where($db->qn('id').' = '.$db->q($id));
			$db->setQuery($query);
			$db->execute();
		}
	}

	function _updateDatabase($parent) {
		if(version_compare(JVERSION, '2.5', '>=') && version_compare(JVERSION, '3.0', '<') ) {
			if (JError::$legacy)
				$tmp_legacy = true;
			else
				$tmp_legacy = false;

			JError::$legacy = false;
		}

		$this->fixNetGrossIssue();

		$db = JFactory::getDbo();

		$queries = array();

		$queries[] = "CREATE TABLE IF NOT EXISTS `#__invoicing_quotes` (
			`invoicing_quote_id` bigint(20) unsigned NOT NULL auto_increment,
			`quote_number` bigint(20),
			`user_id` int(11) NOT NULL,
			`vendor_id` int(11) NOT NULL,
			`created_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`created_by` int(11) NOT NULL DEFAULT 0,
			`due_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`notes` TEXT,
			`processor` varchar(255) NOT NULL,
			`processor_key` varchar(255) NOT NULL,
			`net_subamount` FLOAT NOT NULL,
			`tax_subamount` FLOAT NOT NULL,
			`gross_subamount` FLOAT NOT NULL,
			`custom_discount` FLOAT NOT NULL DEFAULT 0,
			`net_discount_amount` FLOAT NOT NULL,
			`gross_discount_amount` FLOAT NOT NULL,
			`tax_discount_amount` FLOAT NOT NULL,
			`coupon_id` int(11),
			`coupon_type` varchar(255) NULL DEFAULT NULL,
			`discount_type` VARCHAR(255) NOT NULL DEFAULT '',
			`discount_value`  FLOAT NOT NULL DEFAULT 0,
			`net_amount` FLOAT NOT NULL,
			`tax_amount` FLOAT NOT NULL,
			`gross_amount` FLOAT NOT NULL,
			`currency_id` int(11),
			`language` VARCHAR(6) NOT NULL,
			`ip_address` VARCHAR(39) NULL DEFAULT NULL,
			`generator` varchar(255) NULL DEFAULT NULL,
			`generator_key` varchar(255) NULL DEFAULT NULL,
			`params` TEXT NULL DEFAULT NULL,
			PRIMARY KEY (`invoicing_quote_id`)
			) DEFAULT CHARSET=utf8;";

		$queries[] = "CREATE TABLE IF NOT EXISTS `#__invoicing_references` (
			`invoicing_reference_id` bigint(20) unsigned NOT NULL auto_increment,
			`name` TEXT,
			`description` TEXT,
			`quantity` FLOAT NOT NULL,
			`gross_unit_price` FLOAT NOT NULL,
			`tax` FLOAT NOT NULL,
			`net_unit_price` FLOAT NOT NULL,
			`net_amount` FLOAT NOT NULL,
			`gross_amount` FLOAT NOT NULL,
			`source` varchar(255) NOT NULL,
			`source_key` varchar(255) NOT NULL,
			`ordering` bigint(20) unsigned NOT NULL,
			`params` TEXT,
			PRIMARY KEY (`invoicing_reference_id`)
			) DEFAULT CHARSET=utf8;";

		$queries[] = "CREATE TABLE IF NOT EXISTS `#__invoicing_quote_items` (
					`invoicing_quote_item_id` bigint(20) unsigned NOT NULL auto_increment,
					`quote_id` bigint(20) NOT NULL,
					`name` TEXT,
					`description` TEXT,
					`quantity` FLOAT NOT NULL,
					`gross_unit_price` FLOAT NOT NULL,
					`tax` FLOAT NOT NULL,
					`net_unit_price` FLOAT NOT NULL,
					`net_amount` FLOAT NOT NULL,
					`gross_amount` FLOAT NOT NULL,
					`source` varchar(255) NULL,
					`source_key` varchar(255) NULL DEFAULT '',
					`ordering` bigint(20) unsigned NOT NULL,
					`params` TEXT,
					PRIMARY KEY (`invoicing_quote_item_id`)
					) DEFAULT CHARSET=utf8;";

		$queries[] = "ALTER TABLE #__invoicing_currencies ADD COLUMN `number_decimals` int(11) NOT NULL DEFAULT 2;";

		$queries[] = "CREATE TABLE IF NOT EXISTS `#__invoicing_templates` (
					`invoicing_template_id` int(11) NOT NULL AUTO_INCREMENT,
					`description` text,
					`htmlcontent` text,
					`pdfcontent` text,
					`usehtmlforpdf` tinyint(1) NOT NULL DEFAULT '1',
					PRIMARY KEY (`invoicing_template_id`)
					) DEFAULT CHARSET=utf8 ;";

		$queries[] = "INSERT IGNORE INTO `#__invoicing_templates` (`invoicing_template_id`, `description`, `htmlcontent`, `pdfcontent`, `usehtmlforpdf`) VALUES
					(1, 'INVOICING_TEMPLATE_ORDER', '', '', 1),
					(2, 'INVOICING_TEMPLATE_INVOICE', '', '', 1),
					(3, 'INVOICING_TEMPLATE_QUOTE', '', '', 1);";

		$queries[] = "ALTER TABLE #__invoicing_users ADD COLUMN `firstname` varchar(255) NOT NULL DEFAULT '';";
		$queries[] = "ALTER TABLE #__invoicing_users ADD COLUMN `lastname` varchar(255) NOT NULL DEFAULT '';";
		$queries[] = "ALTER TABLE #__invoicing_users ADD COLUMN `mobile` varchar(255) NOT NULL DEFAULT '';";
		$queries[] = "ALTER TABLE #__invoicing_users ADD COLUMN `landline` varchar(255) NOT NULL DEFAULT '';";

		$queries[] = "ALTER TABLE #__invoicing_invoices ADD COLUMN `subject` varchar(255) NULL;";
		$queries[] = "ALTER TABLE #__invoicing_quotes ADD COLUMN `subject` varchar(255) NULL;";

		foreach($queries as $q) {
			$db->setQuery($q);
			try {
				$result = $db->execute();
			} catch(Exception $e) {

			}
		}

		$queries = array();

		//update email table structure
		$queries[] = "ALTER TABLE #__invoicing_emails CHANGE `content` `body` TEXT;";
		$queries[] = "ALTER TABLE #__invoicing_emails CHANGE `enabled` `published` TINYINT(1);";
		$queries[] = "ALTER TABLE #__invoicing_emails ADD COLUMN `key` varchar(255) NOT NULL DEFAULT '';";
		$queries[] = "ALTER TABLE #__invoicing_emails ADD COLUMN `language` varchar(10) NOT NULL DEFAULT '*';";
		$queries[] = "ALTER TABLE #__invoicing_emails ADD COLUMN `ordering` bigint(20) NOT NULL DEFAULT '0';";
		$queries[] = "ALTER TABLE #__invoicing_emails ADD COLUMN `created_on` datetime NOT NULL DEFAULT NOW();";
		$queries[] = "ALTER TABLE #__invoicing_emails ADD COLUMN `created_by` bigint(20) NOT NULL DEFAULT '0';";
		$queries[] = "ALTER TABLE #__invoicing_emails ADD COLUMN `modified_on` datetime DEFAULT NULL;";
		$queries[] = "ALTER TABLE #__invoicing_emails ADD COLUMN `modified_by` bigint(20) NOT NULL DEFAULT '0';";
		$queries[] = "ALTER TABLE #__invoicing_emails ADD COLUMN `locked_on` datetime DEFAULT NULL;";
		$queries[] = "ALTER TABLE #__invoicing_emails ADD COLUMN `locked_by` bigint(20) NOT NULL DEFAULT '0';";

		foreach($queries as $q) {
			$db->setQuery($q);
			try {
				$result = $db->execute();
			} catch(Exception $e) {

			}
		}

		$queries = array();

		//Update email key
		$queries[] = "UPDATE #__invoicing_emails SET `key` = 'invoiving_order_confirmation' WHERE invoicing_email_id = 1;";
		$queries[] = "UPDATE #__invoicing_emails SET `key` = 'invoiving_payment_confirmation' WHERE invoicing_email_id = 3;";
		$queries[] = "UPDATE #__invoicing_emails SET `key` = 'invoiving_payment_request' WHERE invoicing_email_id = 4;";
		$queries[] = "UPDATE #__invoicing_emails SET `key` = 'admin_invoiving_order_confirmation' WHERE invoicing_email_id = 5;";
		$queries[] = "UPDATE #__invoicing_emails SET `key` = 'admin_invoiving_payment_confirmation' WHERE invoicing_email_id = 6;";
		$queries[] = "UPDATE #__invoicing_emails SET `key` = 'admin_invoiving_payment_request' WHERE invoicing_email_id = 7;";

		foreach($queries as $q) {
			$db->setQuery($q);
			try {
				$result = $db->execute();
			} catch(Exception $e) {

			}
		}

		$queries = array();

		$queries[] = "INSERT IGNORE INTO `#__invoicing_emails` (`invoicing_email_id`, `key`,`subject`, `body`, `published`, `description`,`pdf`) VALUES
		(8, 'invoiving_quote', 'Quote', 'Write here your own content for quote, you can use predefined tags in the right', 1, 'INVOICING_QUOTE_MAIL_DESCRIPTION',1),
							(9, 'admin_invoiving_quote', '[Admin] Quote', '[admin] Write here your own content for quote, you can use predefined tags in the right', 1, 'INVOICING_QUOTE_MAIL_DESCRIPTION_ADMIN',1);";

	  $queries[] = "INSERT IGNORE INTO `#__invoicing_emails` (`invoicing_email_id`, `key`, `subject`, `body`, `published`, `description`,`pdf`) VALUES
		(10, 'invoiving_offline_payment_completed', 'Offline payment completed', 'Write here your own content for the reception of an offline payment, you can use predefined tags in the right', 1, 'INVOICING_OFFLINE_PAYMENT_MAIL_DESCRIPTION',1),
							(11, 'admin_invoiving_offline_payment_completed', '[Admin] Offline payment completed', '[admin] Write here your own content for the reception of an offline payment, you can use predefined tags in the right', 1, 'INVOICING_OFFLINE_PAYMENT_MAIL_DESCRIPTION_ADMIN',1),
							(12, 'invoiving_offline2_payment_completed', 'Offline2 payment completed', 'Write here your own content for the reception of an offline2 payment, you can use predefined tags in the right', 1, 'INVOICING_OFFLINE2_PAYMENT_MAIL_DESCRIPTION',1),
							(13, 'admin_invoiving_offline2_payment_completed', '[Admin] Offline2 payment completed', '[admin] Write here your own content for the reception of an offline2 payment, you can use predefined tags in the right', 1, 'INVOICING_OFFLINE2_PAYMENT_MAIL_DESCRIPTION_ADMIN',1);";

		foreach($queries as $q) {
			$db->setQuery($q);
			try {
				$result = $db->execute();
			} catch(Exception $e) {

			}
		}

		$this->fixItemName();


		$nameBaseTemplate = "template4.html";
		//TODO $nameBaseTemplate = "template1.html"; //INVOICING_TAG

		if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
			$src = $parent->getParent()->getPath('source');
		} else {
			$src = $parent->getPath('source');
		}

		$filenameInvoice = $src.'/backend/templates/invoice/'.$nameBaseTemplate ;
		$filenameOrder = $src.'/backend/templates/order/'.$nameBaseTemplate ;
		$filenameQuote = $src.'/backend/templates/quote/'.$nameBaseTemplate ;

	 	$defaultTemplateI = file_get_contents($filenameInvoice);
		$defaultTemplateO = file_get_contents($filenameOrder);
		$defaultTemplateQ = file_get_contents($filenameQuote);

		$idTemplate = 1;

		$query = "SELECT `htmlcontent`, `pdfcontent` FROM #__invoicing_templates WHERE `invoicing_template_id` = '$idTemplate'";

		$subQueryInvoice = '';
		$subQueryOrder = '';
		$subQueryQuote = '';
		$needQuery = false;

		$queries = array();

		$idTemplate = 1;
		$db->setQuery($query);
		$values = $db->loadObject();
        if ($values->htmlcontent == '' || $values->htmlcontent == 'null' || $values->htmlcontent == null) {
			$queries[] = "UPDATE `#__invoicing_templates`
                              SET htmlcontent = '$defaultTemplateO'
                              WHERE invoicing_template_id = ".$idTemplate;
		}
		$idTemplate = 2;
		$db->setQuery($query);
		$values = $db->loadObject();
		if ($values->htmlcontent == '' || $values->htmlcontent == 'null' || $values->htmlcontent == null) {
			$queries[] = "UPDATE `#__invoicing_templates`
                                SET htmlcontent = '$defaultTemplateI'
                                WHERE invoicing_template_id = ".$idTemplate;
		}
		$idTemplate = 3;
		$db->setQuery($query);
		$values = $db->loadObject();
		if ($values->htmlcontent == '' || $values->htmlcontent == 'null' || $values->htmlcontent == null) {
			$queries[] = "UPDATE `#__invoicing_templates`
                              SET htmlcontent = '$defaultTemplateQ'
                                WHERE invoicing_template_id = ".$idTemplate;
		}

		foreach($queries as $q) {
			$db->setQuery($q);
			try {
				$result = $db->execute();
			} catch(Exception $e) {

			}
		}

		if(version_compare(JVERSION, '2.5', '>=') && version_compare(JVERSION, '3.0', '<') ) {
			JError::$legacy = $tmp_legacy;
		}
	}

	function fixItemName() {
		$db = JFactory::getDbo();
		$app = JFactory::getApplication();

		$updateNeeded = 0;

		$db->setQuery("SELECT count(*) FROM INFORMATION_SCHEMA.COLUMNS
					 WHERE TABLE_SCHEMA = '".$app->getCfg('db')."'
					 AND TABLE_NAME = '".$db->getPrefix()."_invoicing_invoice_items'
						 AND COLUMN_NAME = 'name'");
		$count = $db->loadResult();
		if ($count == 0) {
			$queries = array();
			$queries[] = "ALTER TABLE #__invoicing_invoice_items CHANGE `description` `name` TEXT;";
			$queries[] = "ALTER TABLE #__invoicing_invoice_items ADD COLUMN `description` TEXT;";
			$updateNeeded = 1;
			foreach($queries as $q) {
				$db->setQuery($q);
				try {
					$result = $db->execute();
				} catch(Exception $e) {

				}
			}
		}
		$db->setQuery("SELECT count(*) FROM INFORMATION_SCHEMA.COLUMNS
					 WHERE TABLE_SCHEMA = '".$app->getCfg('db')."'
					 AND TABLE_NAME = '".$db->getPrefix()."_invoicing_quote_items'
						 AND COLUMN_NAME = 'name'");
		$count = $db->loadResult();
		if ($count == 0) {
			$queries = array();
			$queries[] = "ALTER TABLE #__invoicing_quote_items CHANGE `description` `name` TEXT;";
			$queries[] = "ALTER TABLE #__invoicing_quote_items ADD COLUMN `description` TEXT;";
			$updateNeeded = 1;
			foreach($queries as $q) {
				$db->setQuery($q);
				try {
					$result = $db->execute();
				} catch(Exception $e) {

				}
			}
		}
		$db->setQuery("SELECT count(*) FROM INFORMATION_SCHEMA.COLUMNS
					 WHERE TABLE_SCHEMA = '".$app->getCfg('db')."'
					 AND TABLE_NAME = '".$db->getPrefix()."_invoicing_references'
						 AND COLUMN_NAME = 'name'");
		$count = $db->loadResult();
		//var_dump($count);
		if ($count == 0) {
			$queries = array();
			$queries[] = "ALTER TABLE #__invoicing_references CHANGE `description` `name` TEXT;";
			$queries[] = "ALTER TABLE #__invoicing_references ADD COLUMN `description` TEXT;";
			$updateNeeded = 1;
			foreach($queries as $q) {
				$db->setQuery($q);
				//echo $q;
				try {
					$result = $db->execute();
				} catch(Exception $e) {

				}
			}
		}

		// Need to replace item_description by item_name
		if ($updateNeeded == 1) {
			$db->setQuery('SELECT * FROM #__invoicing_templates');
			$list = $db->loadObjectList();
			foreach($list as $obj) {
				$data = new stdClass();

				if ($obj->htmlcontent != "") {
					$content = str_replace("{item_description}","{item_name}",$obj->htmlcontent);
					$data->htmlcontent = $content;
				}

				if ($obj->pdfcontent != "") {
					$content = str_replace("{item_description}","{item_name}",$obj->pdfcontent);
					$data->pdfcontent = $content;
				}

				$data->invoicing_template_id = $obj->invoicing_template_id;
				$db->updateObject('#__invoicing_templates',$data,'invoicing_template_id');
			}
			$db->setQuery('SELECT * FROM #__invoicing_emails');
			$list = $db->loadObjectList();
			foreach($list as $obj) {
				$data = new stdClass();

				if ($obj->subject != "") {
					$content = str_replace("{item_description}","{item_name}",$obj->subject);
					$data->subject = $content;
				}

				if ($obj->body != "") {
					$body = str_replace("{item_description}","{item_name}",$obj->body);
					$data->body = $body;
				}

				$data->invoicing_email_id = $obj->invoicing_email_id;
				$db->updateObject('#__invoicing_emails',$data,'invoicing_email_id');
			}
		}
	}

	function fixNetGrossIssue() {
		$db = JFactory::getDbo();

		$db->setQuery('SELECT * FROM #__invoicing_invoices WHERE net_amount > gross_amount');
		$list = $db->loadObjectList();

		if (count($list) > 0) {
			foreach($list as $invoice) {
				$data = new stdClass();

				$data->net_subamount = $invoice->gross_subamount;
				$data->gross_subamount = $invoice->net_subamount;

				$data->net_discount_amount = $invoice->gross_discount_amount;
				$data->gross_discount_amount = $invoice->net_discount_amount;

				$data->net_amount = $invoice->gross_amount;
				$data->gross_amount = $invoice->net_amount;

				$data->invoicing_invoice_id = $invoice->invoicing_invoice_id;
				$db->updateObject('#__invoicing_invoices',$data,'invoicing_invoice_id');
			}

			$db->setQuery('SELECT * FROM #__invoicing_quotes WHERE net_amount > gross_amount');
			$list = $db->loadObjectList();
			foreach($list as $quote) {
				$data = new stdClass();

				$data->net_subamount = $quote->gross_subamount;
				$data->gross_subamount = $quote->net_subamount;

				$data->net_discount_amount = $quote->gross_discount_amount;
				$data->gross_discount_amount = $quote->net_discount_amount;

				$data->net_amount = $quote->gross_amount;
				$data->gross_amount = $quote->net_amount;

				$data->invoicing_quote_id = $quote->invoicing_quote_id;
				$db->updateObject('#__invoicing_quotes',$data,'invoicing_quote_id');
			}

			$db->setQuery('SELECT * FROM #__invoicing_quote_items WHERE net_unit_price > gross_unit_price');
			$list = $db->loadObjectList();
			foreach($list as $obj) {
				$data = new stdClass();

				$data->net_unit_price = $obj->gross_unit_price;
				$data->gross_unit_price = $obj->net_unit_price;

				$data->invoicing_quote_item_id = $obj->invoicing_quote_item_id;
				$db->updateObject('#__invoicing_quote_items',$data,'invoicing_quote_item_id');
			}

			$db->setQuery('SELECT * FROM #__invoicing_invoice_items WHERE net_unit_price > gross_unit_price');
			$list = $db->loadObjectList();
			foreach($list as $obj) {
				$data = new stdClass();

				$data->net_unit_price = $obj->gross_unit_price;
				$data->gross_unit_price = $obj->net_unit_price;

				$data->invoicing_invoice_item_id = $obj->invoicing_invoice_item_id;
				$db->updateObject('#__invoicing_invoice_items',$data,'invoicing_invoice_item_id');
			}

			$db->setQuery('SELECT * FROM #__invoicing_references WHERE net_unit_price > gross_unit_price');
			$list = $db->loadObjectList();
			foreach($list as $obj) {
				$data = new stdClass();

				$data->net_unit_price = $obj->gross_unit_price;
				$data->gross_unit_price = $obj->net_unit_price;

				$data->net_amount = $obj->gross_amount;
				$data->gross_amount = $obj->net_amount;

				$data->invoicing_reference_id = $obj->invoicing_reference_id;
				$db->updateObject('#__invoicing_references',$data,'invoicing_reference_id');
			}

			$db->setQuery('SELECT * FROM #__invoicing_templates');
			$list = $db->loadObjectList();
			foreach($list as $obj) {
				$data = new stdClass();

				if ($obj->htmlcontent != "") {
					$content = str_replace("net_","net2_",$obj->htmlcontent);
					$content = str_replace("gross_","net_",$content);
					$content = str_replace("net2_","gross_",$content);
					$data->htmlcontent = $content;
				}

				if ($obj->pdfcontent != "") {
					$content = str_replace("net_","net2_",$obj->pdfcontent);
					$content = str_replace("gross_","net_",$content);
					$content = str_replace("net2_","gross_",$content);
					$data->pdfcontent = $content;
				}

				$data->invoicing_template_id = $obj->invoicing_template_id;
				$db->updateObject('#__invoicing_templates',$data,'invoicing_template_id');
			}
			$db->setQuery('SELECT * FROM #__invoicing_emails');
			$list = $db->loadObjectList();
			foreach($list as $obj) {
				$data = new stdClass();

				if ($obj->subject != "") {
					$content = str_replace("net_","net2_",$obj->subject);
					$content = str_replace("gross_","net_",$content);
					$content = str_replace("net2_","gross_",$content);
					$data->subject = $content;
				}

				if ($obj->content != "") {
					$content = str_replace("net_","net2_",$obj->content);
					$content = str_replace("gross_","net_",$content);
					$content = str_replace("net2_","gross_",$content);
					$data->content = $content;
				}

				$data->invoicing_email_id = $obj->invoicing_email_id;
				$db->updateObject('#__invoicing_emails',$data,'invoicing_email_id');
			}
		}
	}

	/**
	 * Update XML files only for Joomla1.5
	 */
	public function updateXmlJoomla15($parent) {
		
		$src = $parent->getPath('source');

		$db = JFactory::getDbo();

		$status = new JObject();
		$status->modules = array();
		$status->plugins = array();

		$src = str_replace('backend','',$src);

		if(count($this->installation_queue['modules'])) {
			foreach($this->installation_queue['modules'] as $folder => $modules) {
				if(count($modules)) foreach($modules as $module => $modulePreferences) {
					// Install the module
					if(empty($folder)) $folder = 'site';
					$path = "$src/modules/$folder/$module";
					if(!is_dir($path)) {
						$path = "$src/modules/$folder/mod_$module";
					}
					if(!is_dir($path)) {
						$path = "$src/modules/$module";
					}
					if(!is_dir($path)) {
						$path = "$src/modules/mod_$module";
					}
					if(!is_dir($path)) continue;
					if(file_exists("$path/mod_".$module."_15.xml")) {
                        JFile::copy("$path/mod_".$module."_15.xml","$path/mod_".$module.".xml");
					}
				}
			}
		}



		// Plugins installation
		if(count($this->installation_queue['plugins'])) {
			foreach($this->installation_queue['plugins'] as $folder => $plugins) {
                if(count($plugins)) foreach($plugins as $plugin => $published) {
					$path = "$src/plugins/$folder/$plugin";
					//echo $path;
					if(!is_dir($path)) {
						$path = "$src/plugins/$folder/plg_$plugin";
					}
					if(!is_dir($path)) {
						$path = "$src/plugins/$plugin";
					}
					if(!is_dir($path)) {
						$path = "$src/plugins/plg_$plugin";
					}
					if(!is_dir($path)) continue;
					if(file_exists("$path/".$plugin."_15.xml")) {
						JFile::copy("$path/".$plugin."_15.xml","$path/".$plugin.".xml");
					}

				}
			}
		}
	}

	private function _installJuloaLib($parent)
	{
		
		if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
			$src = $parent->getParent()->getPath('source');
		} else {
			$src = $parent->getPath('source');
		}

		// Install the FOF framework
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		jimport('joomla.utilities.date');

		$source = $src.'/juloalib';

		if(!defined('JPATH_LIBRARIES')) {
			$target = JPATH_ROOT.'/libraries/juloalib';
		} else {
			$target = JPATH_LIBRARIES.'/juloalib';
		}

		$haveToInstallJuloaLib = false;
		if(!JFolder::exists($target)) {
			$haveToInstallJuloaLib = true;
		} else {
			$JuloaLibVersion = array();
			if(JFile::exists($target.'/version.txt')) {
				$rawData = file_get_contents($target.'/version.txt');
				$info = explode("\n", $rawData);
				$JuloaLibVersion['installed'] = array(
						'version'	=> trim($info[0]),
						'date'		=> new JDate(trim($info[1]))
				);
			} else {
				$JuloaLibVersion['installed'] = array(
						'version'	=> '0.0',
						'date'		=> new JDate('2011-01-01')
				);
			}
			$rawData = file_get_contents($source.'/version.txt');
			$info = explode("\n", $rawData);
			$JuloaLibVersion['package'] = array(
					'version'	=> trim($info[0]),
					'date'		=> new JDate(trim($info[1]))
			);

			$haveToInstallJuloaLib = $JuloaLibVersion['package']['date']->toUNIX() >= $JuloaLibVersion['installed']['date']->toUNIX();
		}

		//$installedJuloaLib = false;
		if($haveToInstallJuloaLib) {
			$versionSource = 'package';
			if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
				$installer = new JInstaller;
				$installedJuloaLib = $installer->install($source);
			} else {
				JFolder::create($target);
				$installedJuloaLib = true;
				$files = JFolder::files($source);
				if(!empty($files)) {
					foreach($files as $file) {
						$installedJuloaLib = $installedJuloaLib && JFile::copy($source.'/'.$file, $target.'/'.$file);
					}
				}
				$target2 = JPATH_ROOT.'/media/juloalib';
				JFolder::create($target2);
				if(!empty($files)) {
					foreach($files as $file) {
						$installedJuloaLib = $installedJuloaLib && JFile::copy($source.'/'.$file, $target.'/'.$file);
					}
				}
			}
		}
		else {
			$versionSource = 'installed';
		}



		if(!isset($JuloaLibVersion)) {
			$JuloaLibVersion = array();
			if(JFile::exists($target.'/version.txt')) {
				$rawData = file_get_contents($target.'/version.txt');
				$info = explode("\n", $rawData);
				$JuloaLibVersion['installed'] = array(
						'version'	=> trim($info[0]),
						'date'		=> new JDate(trim($info[1]))
				);
			} else {
				$JuloaLibVersion['installed'] = array(
						'version'	=> '0.0',
						'date'		=> new JDate('2011-01-01')
				);
			}
			$rawData = file_get_contents($source.'/version.txt');
			$info = explode("\n", $rawData);
			$JuloaLibVersion['package'] = array(
					'version'	=> trim($info[0]),
					'date'		=> new JDate(trim($info[1]))
			);
			$versionSource = 'installed';
		}

		if(!($JuloaLibVersion[$versionSource]['date'] instanceof JDate)) {
			$JuloaLibVersion[$versionSource]['date'] = new JDate();
		}

		if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
			$date = $JuloaLibVersion[$versionSource]['date']->format('Y-m-d');
		} else {
			$date = $JuloaLibVersion[$versionSource]['date']->toFormat('%Y-%m-%d');
		}

		return array(
				'required'	=> $haveToInstallJuloaLib,
				'installed'	=> $installedJuloaLib,
				'version'	=> $JuloaLibVersion[$versionSource]['version'],
				'date'		=> $date,
		);
	}

	/**
	 * Installs subextensions (modules, plugins) bundled with the main extension
	 *
	 * @param JInstaller $parent
	 * @return JObject The subextension installation status
	 */
	private function _installSubextensions($parent)
	{
		
		if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
			$src = $parent->getParent()->getPath('source');
		} else {
			$src = $parent->getPath('source');
		}

		$db = JFactory::getDbo();

		$status = new JObject();
		$status->modules = array();
		$status->plugins = array();
		$status->adsmanagerfields = array();

		$src = str_replace('backend','',$src);

		// Modules installation
		if(count($this->installation_queue['modules'])) {
			foreach($this->installation_queue['modules'] as $folder => $modules) {
				if(count($modules)) foreach($modules as $module => $modulePreferences) {
					// Install the module
					if(empty($folder)) $folder = 'site';
					$path = "$src/modules/$folder/$module";
					if(!is_dir($path)) {
						$path = "$src/modules/$folder/mod_$module";
					}
					if(!is_dir($path)) {
						$path = "$src/modules/$module";
					}
					if(!is_dir($path)) {
						$path = "$src/modules/mod_$module";
					}
					if(!is_dir($path)) continue;

					// Was the module already installed?
					if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
						$sql = $db->getQuery(true)
						->select('COUNT(*)')
						->from('#__modules')
						->where($db->qn('module').' = '.$db->q('mod_'.$module));
						$db->setQuery($sql);
						$count = $db->loadResult();
					} else {
						$count = 1;
					}
					$installer = new JInstaller;
					$result = $installer->install($path);
					$status->modules[] = array(
						'name'=>'mod_'.$module,
						'client'=>$folder,
						'result'=>$result
					);
					// Modify where it's published and its published state
					if(!$count) {
						// A. Position and state
						list($modulePosition, $modulePublished) = $modulePreferences;
						if($modulePosition == 'cpanel') {
							$modulePosition = 'icon';
						}
						if(version_compare(JVERSION, '3.0.0', 'ge')) {
							if ($modulePosition == "left") {
								$modulePosition = 'position-7';
							}
						}

						$sql = $db->getQuery(true)
							->update($db->qn('#__modules'))
							->set($db->qn('position').' = '.$db->q($modulePosition))
							->where($db->qn('module').' = '.$db->q('mod_'.$module));
						if($modulePublished) {
							$sql->set($db->qn('published').' = '.$db->q('1'));
						}
						$db->setQuery($sql);
						$db->execute();

						// B. Change the ordering of back-end modules to 1 + max ordering
						if($folder == 'admin') {
							$query = $db->getQuery(true);
							$query->select('MAX('.$db->qn('ordering').')')
								->from($db->qn('#__modules'))
								->where($db->qn('position').'='.$db->q($modulePosition));
							$db->setQuery($query);
							$position = $db->loadResult();
							$position++;

							$query = $db->getQuery(true);
							$query->update($db->qn('#__modules'))
								->set($db->qn('ordering').' = '.$db->q($position))
								->where($db->qn('module').' = '.$db->q('mod_'.$module));
							$db->setQuery($query);
							$db->execute();
						}

						// C. Link to all pages
						$query = $db->getQuery(true);
						$query->select('id')->from($db->qn('#__modules'))
							->where($db->qn('module').' = '.$db->q('mod_'.$module));
						$db->setQuery($query);
						$moduleid = $db->loadResult();

						$query = $db->getQuery(true);
						$query->select('*')->from($db->qn('#__modules_menu'))
							->where($db->qn('moduleid').' = '.$db->q($moduleid));
						$db->setQuery($query);
						$assignments = $db->loadObjectList();
						$isAssigned = !empty($assignments);
						if(!$isAssigned) {
							$o = (object)array(
								'moduleid'	=> $moduleid,
								'menuid'	=> 0
							);
							$db->insertObject('#__modules_menu', $o);
						}
					}
				}
			}
		}

        if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
			$this->installation_queue['plugins']['sh404sefextplugins'] = array(
                                                                        'sh404sefextplugincom_invoicing' => 1
                                                                    );
		} else {
			$this->installation_queue['plugins']['sh404sefextplugins'] = array(
                                                                        'com_invoicing' => 1
                                                                    );
            unset($this->installation_queue['plugins']['xmap']);
		}
		
		// Plugins installation
		if(count($this->installation_queue['plugins'])) {
			foreach($this->installation_queue['plugins'] as $folder => $plugins) {
				if(count($plugins)) foreach($plugins as $plugin => $published) {
					$path = "$src/plugins/$folder/$plugin";

					if(!is_dir($path)) {
						$path = "$src/plugins/$folder/plg_$plugin";
					}
					if(!is_dir($path)) {
						$path = "$src/plugins/$plugin";
					}
					if(!is_dir($path)) {
						$path = "$src/plugins/plg_$plugin";
					}
					if(!is_dir($path)) continue;

					// Was the plugin already installed?
					if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
						$query = $db->getQuery(true)
							->select('COUNT(*)')
							->from($db->qn('#__extensions'))
							->where($db->qn('element').' = '.$db->q($plugin))
							->where($db->qn('folder').' = '.$db->q($folder));
						$db->setQuery($query);
						$count = $db->loadResult();
					} else {
						$count = 1;
					}


					$installer = new JInstaller;
					$result = $installer->install($path);

					$status->plugins[] = array('name'=>'plg_'.$plugin,'group'=>$folder, 'result'=>$result);

					if($published && !$count) {
						$query = $db->getQuery(true)
							->update($db->qn('#__extensions'))
							->set($db->qn('enabled').' = '.$db->q('1'))
							->where($db->qn('element').' = '.$db->q($plugin))
							->where($db->qn('folder').' = '.$db->q($folder));
						$db->setQuery($query);
						$db->execute();
					}
				}
			}
		}
		
		return $status;
	}

	function rmdir_recurse($path,$onlycontent = false) {
		
		if (is_dir($path)){
			$path= rtrim($path, '/').'/';
			$handle = opendir($path);
			for (;false !== ($file = readdir($handle));)
				if($file != "." and $file != ".." ) {
				$fullpath= $path.$file;
				if( is_dir($fullpath) ) {
					self::rmdir_recurse($fullpath);
				} else {
					@unlink($fullpath);
				}
			}
			closedir($handle);
			if  ($onlycontent == false)
				rmdir($path);
		}
	}

	function recurse_copy($src,$dst) {
		
		$dir = opendir($src);
		if ($dir == false) {
			return false;
		}
		@mkdir($dst);
		while(false !== ( $file = readdir($dir)) ) {
			if (( $file != '.' ) && ( $file != '..' )) {
				if ( is_dir($src . '/' . $file) ) {
					$this->recurse_copy($src . '/' . $file,$dst . '/' . $file);
				}
				else {
					copy($src . '/' . $file,$dst . '/' . $file);
				}
			}
		}
		closedir($dir);
	}

	/**
	 * Uninstalls subextensions (modules, plugins) bundled with the main extension
	 *
	 * @param JInstaller $parent
	 * @return JObject The subextension uninstallation status
	 */
	private function _uninstallSubextensions($parent)
	{
		
		jimport('joomla.installer.installer');

		$db = JFactory::getDBO();

		$status = new JObject();
		$status->modules = array();
		$status->plugins = array();

		if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
			$src = $parent->getParent()->getPath('source');
		} else {
			$src = $parent->getPath('source');
		}

		// Modules uninstallation
		if(count($this->installation_queue['modules'])) {
			foreach($this->installation_queue['modules'] as $folder => $modules) {
				if(count($modules)) foreach($modules as $module => $modulePreferences) {
					// Find the module ID
					$sql = $db->getQuery(true)
						->select($db->qn('extension_id'))
						->from($db->qn('#__extensions'))
						->where($db->qn('element').' = '.$db->q('mod_'.$module))
						->where($db->qn('type').' = '.$db->q('module'));
					$db->setQuery($sql);
					$id = $db->loadResult();
					// Uninstall the module
					if($id) {
						$installer = new JInstaller;
						$result = $installer->uninstall('module',$id,1);
						$status->modules[] = array(
							'name'=>'mod_'.$module,
							'client'=>$folder,
							'result'=>$result
						);
					}
				}
			}
		}

		// Plugins uninstallation
		if(count($this->installation_queue['plugins'])) {
			foreach($this->installation_queue['plugins'] as $folder => $plugins) {
				if(count($plugins)) foreach($plugins as $plugin => $published) {
					$sql = $db->getQuery(true)
						->select($db->qn('extension_id'))
						->from($db->qn('#__extensions'))
						->where($db->qn('type').' = '.$db->q('plugin'))
						->where($db->qn('element').' = '.$db->q($plugin))
						->where($db->qn('folder').' = '.$db->q($folder));
					$db->setQuery($sql);

					$id = $db->loadResult();
					if($id)
					{
						$installer = new JInstaller;
						$result = $installer->uninstall('plugin',$id,1);
						$status->plugins[] = array(
							'name'=>'plg_'.$plugin,
							'group'=>$folder,
							'result'=>$result
						);
					}
				}
			}
		}

		return $status;
	}

	/**
	 * Removes obsolete files and folders
	 *
	 * @param array $invoicingRemoveFiles
	 */
	private function _removeObsoleteFilesAndFolders($invoicingRemoveFiles)
	{
		
		// Remove files
		jimport('joomla.filesystem.file');
		if(!empty($invoicingRemoveFiles['files'])) foreach($invoicingRemoveFiles['files'] as $file) {
			$f = JPATH_ROOT.'/'.$file;
			if(!JFile::exists($f)) continue;
			JFile::delete($f);
		}

		// Remove folders
		jimport('joomla.filesystem.file');
		if(!empty($invoicingRemoveFiles['folders'])) foreach($invoicingRemoveFiles['folders'] as $folder) {
			$f = JPATH_ROOT.'/'.$folder;
			if(!JFolder::exists($f)) continue;
			JFolder::delete($f);
		}
	}

	private function _updateSite($parent)
	{
	}
}
