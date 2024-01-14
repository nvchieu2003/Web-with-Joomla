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
$path="/plugins"."/"."system"."/"."imagesizer"."/"."lbscripts"."/"."chocolatmaster"."/";

$document   = JFactory::getDocument();

$document->addStyleSheet(redim_imagesizer_class::clean_include_documentpath($path.'chocolat.css'),'text/css',"all");
$document->addScript(redim_imagesizer_class::clean_include_documentpath($path.'jquery.chocolat.js'));

$base=JURI::base(true);
$document->addScriptDeclaration("
jQuery(document).ready(function() {
  var opt=
  {
	leftImg               : '".$base."/plugins/system/imagesizer/lbscripts/chocolatmaster/left.gif',	
	rightImg              : '".$base."/plugins/system/imagesizer/lbscripts/chocolatmaster/right.gif',	
	closeImg              : '".$base."/plugins/system/imagesizer/lbscripts/chocolatmaster/close.gif',		
	loadingImg            : '".$base."/plugins/system/imagesizer/lbscripts/chocolatmaster/loading.gif'
  }
   jQuery('a.imagesizer').Chocolat(opt);   
});
");

unset($path);


function imagesizer_addon_chocolatmaster(&$ar,&$imagesizer){

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

	if(isset($imagesizer->article->id)){
		$ar["group"]=$imagesizer->article->id;
	}

    if(isset($ar["data-group"])){
        $group =' data-group="'.(int)$ar["data-group"].'"';
    }else{
        $group ='';       
    }

	$output='<a'.$group.' class="'.trim("imagesizer ".$imagesizer->params->get("linkclass","linkthumb")).'" target="_blank"'.$title.' href="'.$ar["href"].'"><img '.$output.' /></a>';	


	return $output;

}


