<?PHP
/**
 * @package	ImageSizer for Joomla! 3.x
 * @version	3.2.4
 * @author	reDim GmbH
 * @copyright	(C) 2009-2015 reDim GmbH All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('JPATH_BASE') or die;

JHtml::_('jquery.framework');

$path="/plugins"."/"."system"."/"."imagesizer"."/"."lbscripts"."/"."superbox"."/";

$document   = JFactory::getDocument();
$document->addStyleSheet(redim_imagesizer_class::clean_include_documentpath($path.'jquery.superbox.css'),'text/css',"all");
$document->addScript(redim_imagesizer_class::clean_include_documentpath($path.'jquery.superbox.js'));

$document->addScriptDeclaration("
jQuery(document).ready(function() {
   jQuery.superbox();
});
");

unset($path);


function imagesizer_addon_superbox(&$ar,&$imagesizer){

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
		$ar["group"]=$imagesizer->article->id;
	}
    
    if(isset($ar["group"])){
        $group =' rel="superbox[gallery]['.$ar["group"].']"'; 
    }else{
        $group =' rel="superbox[gallery][imagesizer]"';       
    }
    
	
	$output='<a'.$group.' class="'.trim("imagesizer ".$imagesizer->params->get("linkclass","linkthumb")).'" target="_blank"'.$title.' href="'.$ar["href"].'"><img '.$output.' /></a>';	

	return $output;

}


