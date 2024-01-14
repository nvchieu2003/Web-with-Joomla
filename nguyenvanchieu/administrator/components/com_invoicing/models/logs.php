<?php
/**
 * @package		Invoicing
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/cparams.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/mail.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/models/default.php');

class InvoicingModelLogs extends InvoicingModelDefault {
	protected $isDatabase = false;

	public function __construct($config = array()) {
	    // This is a dirty trick to avoid getting warning PHP messages by the
	    // JDatabase layer
	    $config['table'] = 'shops';
	    parent::__construct($config);
	}
	
	public function getLog() {
		$config = \JFactory::getConfig();
		if(version_compare(JVERSION, '3.0', 'ge')) {
			$logpath = $config->get('log_path');
		} else {
			$logpath = $config->getValue('log_path');
		}
		
		$logFilenameBase = $logpath.'/invoicepayment.php';
		
		if (file_exists($logFilenameBase)) {
			$content = file_get_contents($logFilenameBase);
		} else {
			$content = "No log file, check file permission $logFilenameBase";
		}
		$content = str_replace('<?php die(); ?>','',$content);
		return $content;
	}
}
