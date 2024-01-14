<?PHP
/**
 * @package	ImageSizer for Joomla! 3.x
 * @version	3.2.4
 * @author	reDim GmbH
 * @copyright	(C) 2009-2015 reDim GmbH All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('JPATH_BASE') or die;
#JHtml::_('behavior.framework', true);
#jimport('joomla.html.html.bootstrap');
#JHTML::_('behavior.bootstrap'); 
JHtml::_('jquery.framework');
$path="/plugins"."/"."system"."/"."imagesizer"."/"."lbscripts"."/"."greybox"."/";

$document   = JFactory::getDocument();

$document->addStyleSheet(redim_imagesizer_class::clean_include_documentpath($path.'gb_styles.css'),'text/css',"all");


$document->addScriptDeclaration("
	var GB_ROOT_DIR= '".JURI::base(true)."/plugins/system/imagesizer/lbscripts/greybox/';
");


$document->addScript(redim_imagesizer_class::clean_include_documentpath($path.'AJS.js'));
$document->addScript(redim_imagesizer_class::clean_include_documentpath($path.'AJS_fx.js'));
$document->addScript(redim_imagesizer_class::clean_include_documentpath($path.'gb_scripts.js'));

unset($path);


function imagesizer_addon_greybox(&$ar,&$imagesizer){

	$output=plgSystemimagesizer::make_img_output($ar);

	if(isset($ar["title"])){
		$title=' title="'.$ar["title"].'"';
	}else{
		if(isset($ar["alt"])){
			$title=' title="'.$ar["alt"].'"';
		}else{
			$title="";
		} 
	} 


	$id=0;
	
	if(isset($imagesizer->article->id)){
		$id=$imagesizer->article->id;
	}
	


	$output='<a class="'.trim($imagesizer->params->get("linkclass","linkthumb")).'" target="_blank"'.$title.' rel="gb_imageset[id_'.$id.']" href="'.$ar["href"].'"><img '.$output.' /></a>';	



	return $output;

}
