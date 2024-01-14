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
$path="/plugins"."/"."system"."/"."imagesizer"."/"."lbscripts"."/"."magnificpopup"."/";

$document   = JFactory::getDocument();

$document->addStyleSheet(redim_imagesizer_class::clean_include_documentpath($path.'magnific-popup.css'),'text/css',"all");
$document->addScript(redim_imagesizer_class::clean_include_documentpath($path.'magnific.js'));

$document->addScriptDeclaration("
jQuery(document).ready(function() {
    jQuery('.imagesizer').magnificPopup({
      type: 'image',
      gallery:{
        enabled:true
      }
    });    
});
");

unset($path);


function imagesizer_addon_magnificpopup(&$ar,&$imagesizer){

	$output=plgSystemimagesizer::make_img_output($ar);

	$title="";
	if(isset($ar["title"])){
		#$title=' title="'.$ar["title"].'"';
		$title.='<strong>'.$ar["title"].'</strong>';
	}

	if(isset($ar["alt"])){
		#$title=' title="'.$ar["title"].'"';
		$title.='<p>'.$ar["alt"].'</p>';
	}	
	
	if(!empty($title)){
		$title=htmlspecialchars($title);
		$title=' title="'.$title.'"';
	}
	
	$id=0;
	
	if(isset($imagesizer->article->id)){
		$id=$imagesizer->article->id;
	}
	
    if(isset($ar["group"])){
        $group =' data-lightbox-gallery="'.$ar["group"].'"';
    }else{
        $group =' data-lightbox-gallery="image"';       
    }

	$output='<a'.$group.' class="'.trim("imagesizer ".$imagesizer->params->get("linkclass","linkthumb")).'" target="_blank"'.$title.' href="'.$ar["href"].'"><img '.$output.' /></a>';	

	return $output;

}


