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

$path="/plugins"."/"."system"."/"."imagesizer"."/"."lbscripts"."/"."mooimagelayer"."/";

$document   = JFactory::getDocument();

$document->addStyleSheet(redim_imagesizer_class::clean_include_documentpath($path.'mooimagelayer.css'),'text/css',"all");
$document->addScript(redim_imagesizer_class::clean_include_documentpath($path.'mooimagelayer.js'));

$java="window.addEvent('domready',function() {
	var myImageLayer = new mooImageLayer({
		resize: true
	});
});
";
$document->addScriptDeclaration($java);

unset($path);


function imagesizer_addon_mooimagelayer(&$ar,&$imagesizer){

	$output=plgSystemimagesizer::make_img_output($ar);

	if(isset($ar["title"])){
		$title=' title="'.$ar["title"].'"';
	}else{
		$title="";
	} 
	
	$id=0;
	
	if(isset($imagesizer->article->id)){
		$id=$imagesizer->article->id;
	}
	
	$output='<a class="'.trim($imagesizer->params->get("linkclass","linkthumb")."").' mil-imagelink" target="_blank"'.$title.' href="'.$ar["href"].'"><img '.$output.' /></a>';	

	return $output;

}


