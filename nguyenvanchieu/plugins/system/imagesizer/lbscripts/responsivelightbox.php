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

$path="/plugins"."/"."system"."/"."imagesizer"."/"."lbscripts"."/"."responsivelightbox"."/";

$document   = JFactory::getDocument();
$document->addStyleSheet(redim_imagesizer_class::clean_include_documentpath($path.'jquery.lightbox.min.css'),'text/css',"all");
$document->addScript(redim_imagesizer_class::clean_include_documentpath($path.'jquery.lightbox.min.js'));

$document->addScriptDeclaration("
jQuery(document).ready(function() {
    jQuery('.lightbox').lightbox(); 
});
");

unset($path);


function imagesizer_addon_responsivelightbox(&$ar,&$imagesizer){

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
	
	$output='<a class="lightbox '.trim($imagesizer->params->get("linkclass","linkthumb")).'" target="_blank"'.$title.' href="'.$ar["href"].'"><img '.$output.' /></a>';	

	return $output;

}


