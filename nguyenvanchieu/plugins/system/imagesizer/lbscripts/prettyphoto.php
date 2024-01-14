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

$path="/plugins"."/"."system"."/"."imagesizer"."/"."lbscripts"."/"."prettyphoto"."/";

$document   = JFactory::getDocument();
$document->addStyleSheet(redim_imagesizer_class::clean_include_documentpath($path.'css/prettyPhoto.css'),'text/css',"all");
$document->addScript(redim_imagesizer_class::clean_include_documentpath($path.'js/jquery.prettyPhoto.js'));

$document->addScriptDeclaration("
jQuery(document).ready(function() {
		jQuery('a.lightbox').prettyPhoto({
			animationSpeed: 'normal', 
			opacity: 0.80, 
			showTitle: true,
            social_tools: ''
		});
});
");


unset($path);


function imagesizer_addon_prettyphoto(&$ar,&$imagesizer){

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


    if(isset($ar["group"])){
        $group =' rel="prettyPhoto['.$ar["group"].']"'; 
    }else{
        $group =' rel="prettyPhoto[imagesizer]"';       
    }
	
	$output='<a'.$group.' class="lightbox '.trim($imagesizer->params->get("linkclass","linkthumb")).'" target="_blank"'.$title.' href="'.$ar["href"].'"><img '.$output.' /></a>';	

	return $output;

}


