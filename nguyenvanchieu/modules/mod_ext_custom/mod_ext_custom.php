<?php 
/*
# ------------------------------------------------------------------------
# Extensions for Joomla 2.5.x - Joomla 3.x - Joomla 4.x
# ------------------------------------------------------------------------
# Copyright (C) 2011-2020 Eco-Joom.com. All Rights Reserved.
# @license - PHP files are GNU/GPL V2.
# Author: Eco-Joom.com
# Author: Makeev Vladimir
#Author email: v.v.makeev@icloud.com
# Websites:  http://eco-joom.com
# Date modified: 05/05/2020 - 13:00
# ------------------------------------------------------------------------
*/

// no direct access
defined('_JEXEC') or die;
$doc 				= JFactory::getDocument();
$moduleclass_sfx	= $params->get('moduleclass_sfx');
$html 				= $params->get('html');
$css 				= $params->get('css'); 
$js 				= $params->get('js');

if (strlen($css) > 0) { $doc->addStyleDeclaration($css); }
if (strlen($js) > 0)  { $doc->addScriptDeclaration($js); }

require JModuleHelper::getLayoutPath('mod_ext_custom', $params->get('layout', 'default'));
?>