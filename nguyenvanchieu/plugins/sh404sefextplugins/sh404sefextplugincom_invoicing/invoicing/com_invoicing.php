<?php
/**
 * @package		Invoicing
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license GNU General Public License version 3, or later
 */
// Security check to ensure this file is being included by a parent file.
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

//require_once(JPATH_ROOT.DS."components".DS."com_invoicing".DS."lib".DS."core.php");

// ------------------ standard plugin initialize function - don't change -------------------
global $sh_LANG, $sefConfig ;
$shLangName = '';;
$shLangIso = '';
$title = array();
$shItemidString = '';
$dosef = shInitializePlugin( $lang, $shLangName, $shLangIso, $option);
// ------------------ standard plugin initialize function - don't change -----------------

if (!function_exists("parseJLanguage")) {
	function parseJLanguage($filename)
	{
		if (!file_exists($filename))
			return;
		$version = phpversion();
	
		// Capture hidden PHP errors from the parsing.
		$php_errormsg = null;
		$track_errors = ini_get('track_errors');
		ini_set('track_errors', true);
	
		if ($version >= '5.3.1')
		{
			$contents = file_get_contents($filename);
			$contents = str_replace('_QQ_', '"\""', $contents);
			$strings = @parse_ini_string($contents);
		}
		else
		{
			$strings = @parse_ini_file($filename);
	
			if ($version == '5.3.0' && is_array($strings))
			{
				foreach ($strings as $key => $string)
				{
					$strings[$key] = str_replace('_QQ_', '"', $string);
				}
			}
		}
	
		// Restore error tracking to what it was before.
		ini_set('track_errors', $track_errors);
	
		if (!is_array($strings))
		{
			$strings = array();
		}
	
		return $strings;
	}
}

global $invoicinglanguages;
if (!function_exists("parseInvoicingLanguage")) {
	function parseInvoicingLanguage($langcode) {
		if (file_exists(JPATH_ROOT.'/language/'.$langcode.'/'.$langcode.'.com_invoicing.ini'))
			$langs= parseJLanguage(JPATH_ROOT.'/language/'.$langcode.'/'.$langcode.'.com_invoicing.ini');
		else
			$langs= parseJLanguage(JPATH_ROOT.'/language/en-GB/en-GB.com_invoicing.ini');
		if (file_exists(JPATH_ROOT.'/language/overrides/'.$langcode.'.override.ini')) {
			$langs2 =  parseJLanguage(JPATH_ROOT.'/language/overrides/'.$langcode.'.override.ini');
			$langs = array_merge($langs,$langs2);	
		}
		return $langs;
	}
	
	jimport( 'joomla.language.language' );
	$languages = JLanguage::getKnownLanguages();
	
	$invoicinglanguages = array();
	foreach($languages as $lang) {
		$invoicinglanguages[substr($lang['tag'],0,strpos($lang['tag'],'-'))] = parseInvoicingLanguage($lang['tag']);
	}
}
foreach($invoicinglanguages as $lang => $list) {
	if (!isset($sh_LANG[$lang])) {
		$sh_LANG[$lang] = array();
	}
	$sh_LANG[$lang] =  array_merge($sh_LANG[$lang],$list);
}


// remove common URL from GET vars list, so that they don't show up as query string in the URL
shRemoveFromGETVarsList('option');
shRemoveFromGETVarsList('lang');

$l = \JFactory::getLanguage();
$l->load("com_invoicing",JPATH_ROOT);

if (!empty($Itemid))
	shRemoveFromGETVarsList('Itemid');

if (!empty($limit)) 
	shRemoveFromGETVarsList('limit');
if (isset($limitstart)) {
	shRemoveFromGETVarsList('limitstart');
} else {
	// no limistart, insert one with value 0
	// to counter session-based storage of limitstart
	$limitstart = 0;
	shAddToGETVarsList( 'limitstart', $limitstart);
	shRemoveFromGETVarsList('limitstart');
}


//var_dump($sh_LANG[$shLangIso]);
if (isset($view)) {
	switch ($view) {
		case 'invoice':
			if (@$layout == 'payment') {
				$title[] = $sh_LANG[$shLangIso]['INVOICING_SEF_CHECKOUT'];
			} else {
				$title[] = $sh_LANG[$shLangIso]['INVOICING_SEF_INVOICE'];
			}
			shRemoveFromGETVarsList('layout');
			break;
			
		case 'invoices':
			$title[] = $sh_LANG[$shLangIso]['INVOICING_SEF_INVOICES'];
			break;
			
		case 'message':
			if (@$layout == 'complete') {
				$title[] = $sh_LANG[$shLangIso]['INVOICING_SEF_THANKYOU'];
			} else {
				$title[] = $sh_LANG[$shLangIso]['INVOICING_SEF_CANCELLED'];
			}
			shRemoveFromGETVarsList('layout');
			break;
			
		case 'payment':
			$title[] = $sh_LANG[$shLangIso]['INVOICING_SEF_PAYMENT'];
			break;
		default:
			$title[] = $view;
	}
	
	shRemoveFromGETVarsList('view');
}

// ------------------ standard plugin finalize function - don't change ---------------------------

if ($dosef){
	$string = shFinalizePlugin( $string, $title, $shAppendString, $shItemidString,
	(isset($limit) ? @$limit : null), (isset($limitstart) ? @$limitstart : null),
	(isset($shLangName) ? @$shLangName : null));
}
// ------------------ standard plugin finalize function - don't change ---------------------------