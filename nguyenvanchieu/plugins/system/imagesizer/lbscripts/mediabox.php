<?PHP
/**
 * @package	ImageSizer for Joomla! 3.x
 * @version	3.2.4
 * @author	reDim GmbH
 * @copyright	(C) 2009-2015 reDim GmbH All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('JPATH_BASE') or die;

JHtml::_('behavior.framework', true);

$path="/plugins"."/"."system"."/"."imagesizer"."/"."lbscripts"."/"."mediabox"."/";

$document   = JFactory::getDocument();
$document->addStyleSheet(redim_imagesizer_class::clean_include_documentpath($path.'mediaboxAdvBlack.css'),'text/css',"all");
$document->addScript(redim_imagesizer_class::clean_include_documentpath($path.'mediaboxAdv.js'));


unset($path);


function imagesizer_addon_mediabox(&$ar,&$imagesizer){

	$output=plgSystemimagesizer::make_img_output($ar);
	
	$title="";
	if(!empty($ar["title"])){
		$title.=$ar["title"];
	}
	if(!empty($ar["alt"])){
	 	if($title!=""){$title.="::";}
		$title.=$ar["alt"];
	}

	$id=0;
	
	if(isset($imagesizer->article->id)){
		$id=$imagesizer->article->id;
	}

	$output='<a class="'.trim($imagesizer->params->get("linkclass","linkthumb")).'" target="_blank" title="'.$title.'" rel="lightbox[id'.$id.']" href="'.$ar["href"].'"><img '.$output.' /></a>';	

	return $output;

}


