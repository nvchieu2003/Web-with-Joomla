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
#jimport('joomla.html.html.bootstrap');
#JHTML::_('behavior.bootstrap'); 
#JHtml::_('jquery.framework');
#JHTml::_('behavior.jquery');

$path="/plugins"."/"."system"."/"."imagesizer"."/"."lbscripts"."/"."cerabox"."/";

$document   = JFactory::getDocument();
$document->addStyleSheet(redim_imagesizer_class::clean_include_documentpath($path.'cerabox.css'),'text/css',"all");
$document->addScript(redim_imagesizer_class::clean_include_documentpath($path.'cerabox.min.js'));


$document->addScriptDeclaration("
window.addEvent('domready', function(){
	$$('a.ceraBox').cerabox({
		animation: 'ease',
		loaderAtItem: true
	});	
});
");



unset($path);


function imagesizer_addon_cerabox(&$ar,&$imagesizer){

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
	
	$output='<a class="ceraBox '.trim($imagesizer->params->get("linkclass","linkthumb")).'" target="_blank"'.$title.' rel="ceraBox[id_'.$id.']" href="'.$ar["href"].'"><img '.$output.' /></a>';	

	return $output;

}

