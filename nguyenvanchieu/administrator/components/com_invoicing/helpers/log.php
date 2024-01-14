<?php
/**
 *  @package Invoicing
 *  @copyright Copyright (c)2012 JoomPROD
 *  @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();
	
class InvoicingHelperLog {
	
	/**
	 * Log
	 * @param string $data string or array of strings
	 */
	public static final function log($data)
	{
		$config = \JFactory::getConfig();
		if(version_compare(JVERSION, '3.0', 'ge')) {
			$logpath = $config->get('log_path');
		} else {
			$logpath = $config->getValue('log_path');
		}
	
		$logFilenameBase = $logpath.'/invoicepayment';
	
		$logFile = $logFilenameBase.'.php';
		jimport('joomla.filesystem.file');
		if(!file_exists($logFile)) {
			$dummy = "<?php die(); ?>\n";
			$file = fopen($logFile, "w");
			fwrite($file, $dummy);
		} else {
			if(@filesize($logFile) > 1048756) {
				$altLog = $logFilenameBase.'-1.php';
				if(file_exists($altLog)) {
					unlink($altLog);
				}
				$file = fopen($altLog, "w");
				copy($logFile, $altLog);
				unlink($logFile);
				$dummy = "<?php die(); ?>\n";
				$file = fopen($logFile, "w");
				fwrite($file, $dummy);
			}
		}
		$logData = file_get_contents($logFile);
		if($logData === false) 
			$logData = '';
		
		if (is_array($data)) {
			$logData .= "\n";
			foreach($data as $d) {
				$logData = gmdate('Y-m-d H:i:s')." GMT: $d";
				$logData .= "\n";
			}
		} else {
			$logData .= gmdate('Y-m-d H:i:s')." GMT: $data";
			$logData .= "\n";
		}
		$file = fopen($logFile, "w");
		fwrite($file, $logData);
	}
}