<?PHP
/**
 * @package	ImageSizer for Joomla! 3.x
 * @version	3.2.4
 * @author	reDim GmbH
 * @copyright	(C) 2009-2015 reDim GmbH All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

JHtml::_('behavior.framework', true);

$path="/plugins"."/"."system"."/"."imagesizer"."/"."lbscripts"."/"."shadowbox"."/";

$document   = JFactory::getDocument();
$document->addStyleSheet(redim_imagesizer_class::clean_include_documentpath($path.'shadowbox.css'),'text/css',"all");
$document->addScript(redim_imagesizer_class::clean_include_documentpath($path.'shadowbox.js'));

unset($path);


$java='Shadowbox.init({handleOversize: "drag", modal: true});';
$document->addScriptDeclaration($java);


function imagesizer_addon_shadowbox(&$ar,&$imagesizer){

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

	$output='<a class="'.trim($imagesizer->params->get("linkclass","linkthumb")).'" target="_blank" title="'.$title.'" rel="shadowbox[id'.$id.']" href="'.$ar["href"].'"><img '.$output.' /></a>';	

	return $output;

}
