<?php
/**
 * @package	ImageSizer for Joomla! 3.x
 * @version	3.2.11
 * @author	reDim GmbH
 * @copyright	(C) 2009-2015 reDim GmbH All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
 
 /*
	26.02.2018 - Lukas Pleger 
	Patch Notes v3.2.8:
	- Ein Fehler wurde behoben, bei dem das automatische Bild skalieren beim einfügen nicht wirkte.
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
define('_IMAGESIZER_IS_LOAD',true);
jimport( 'joomla.plugin.plugin' );

if(!defined("DS")){	define("DS",DIRECTORY_SEPARATOR);}

include_once("libraries".DIRECTORY_SEPARATOR."redim_imagesizer_class.php");

class plgSystemimagesizer extends redim_imagesizer_class {


	public function __construct(&$subject, $config=array())
	{
	 	$file=JPATH_SITE.DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."system".DIRECTORY_SEPARATOR."imagesizer".DIRECTORY_SEPARATOR."libraries".DIRECTORY_SEPARATOR."redim_key.php";
		if(file_exists($file)){	include_once($file);}
		unset($file);
		parent::__construct($subject, $config);

    }

	public function onContentBeforeSave($context, $article=false, $isNew=false)
	{
	 

		if ($context != 'com_content.article' or $article==false) {
			return true;
		}		
	
		if(isset($article->introtext)){
			$article->introtext = preg_replace_callback('/\{(imagesizer)\s*(.*?)\}/i',array($this,"imagesizer_cmd"), $article->introtext);
		}
		if(isset($article->fulltext)){
			$article->fulltext = preg_replace_callback('/\{(imagesizer)\s*(.*?)\}/i',array($this,"imagesizer_cmd"), $article->fulltext);
		}
		if(isset($article->text)){
			$article->text = preg_replace_callback('/\{(imagesizer)\s*(.*?)\}/i',array($this,"imagesizer_cmd"), $article->text);
		}

	}

	public function onCheckupdate(){

		return $this->params->get("checkupdate",0);
	
	}

	public function onContentAfterSave($context=false, $article=false, $isNew=false)
	{
		
		if ($context != 'com_content.article' or $article==false) {
			return true;
		}		
	
		$app = JFactory::getApplication();
		$this->_loadLanguage();

	 	$text="";
	 	if(isset($article->introtext)){$text.=$article->introtext;}
	 	if(isset($article->fulltext)){$text.=$article->fulltext;}
	 	if(isset($article->description)){$text.=$article->description;}

		if($this->params->get("generate",2)!=2){
			return true;
		}
	
		$text=strip_tags($text,"<img>");
			
	    $regex="/\<img (.*?)\>/i";
	    $text=preg_replace_callback($regex,array($this,"imagesizer"),$text);	    
		unset($text);

		if($this->created_pics>0){
			$app->enqueueMessage(JText::sprintf('IMAGESIZER_X_IMAGES_CREATED',$this->created_pics));
		}
	

		$this->get_errors();
	
		return true;
	}
	

	public function onAfterInitialise()
	{
		
		$app = JFactory::getApplication();

		if ($app->getName() == 'administrator') {
			if(JRequest::getCMD("code","")=="redim-helper"){

				$user = JFactory::getUser();
		#		$lang = JFactory::getLanguage();

				$this->_loadLanguage();
				if($user->id>0){
					$email=JRequest::getVAR("email","");
					$text=JRequest::getVAR("text","");		
					
					if($this->send_helpdata($email,$text)){
						echo JText::_("IMAGESIZER_HELP_EMAIL_ISSEND"); 
					}else{
						echo JText::_("IMAGESIZER_HELP_EMAIL_NOTSEND");					
					}
				
					die();
				}
			}
		}
	
	
		if($this->params->get("generate2","prepare")!="render"){
			return;
		}

		if ($app->getName() != 'site') {
			return true;
		}


		$this->Includefiles();

	}

	public function onAfterDispatch()
	{

		if($this->params->get("insert","0")!="1"){return;}

		#$app = JFactory::getApplication();

		$ch=strtolower(JRequest::getVar('option','').".".JRequest::getVar('view',''));	
		
		if($ch!="com_media.images"){
			return;
		}


		$document   = JFactory::getDocument();		
		$js = '
			document.addEventListener("DOMContentLoaded", function(){
				window.parent.jInsertEditorText = (function() {
					var oldfunction = window.parent.jInsertEditorText;
					return function(text, editor) {
						if(text.substring(0, 4) == "<img") {

							var img = new Image();
							var regex = /<img.*?src="(.*?)"/;
							var src = regex.exec(text);

							img.src = "../"+src[1];

							if(img.width==0 || img.height==0){
								img.src = src[1];
							}

							var xx = img.width;
							var yy = img.height;
							var imagesizerW = '.$this->params->get("minsizex",122).';
							var imagesizerH = '.$this->params->get("minsizey",122).';

							if (xx > imagesizerW || yy > imagesizerH){

								var faktor = 0;

								if (xx > yy || xx == yy){ 
									faktor = img.width / imagesizerW ;
								}else if (xx < yy){
									faktor =  img.height / imagesizerH ;
								}
								
								if (faktor > 0){
								   xx = Math.round( img.width / faktor , 0);
								   yy = Math.round( img.height / faktor , 0);
								}
								
								var attributes = {};
								text.match(/[\w-]+=".+?"/g).forEach(function(attribute) {
									attribute = attribute.match(/([\w-]+)="(.+?)"/);
									attributes[attribute[1]] = attribute[2];
								});
								
								if(typeof attributes["width"] === "undefined"){
									attributes["width"] = xx;
								}

								if(typeof attributes["height"] === "undefined"){
									attributes["height"] = yy;
								}
								
								text = "<img ";
								for(var a in attributes) {
									text += a+"=\""+attributes[a]+"\" ";
								}
								text += "/>";
								console.log(text);
							}   
						}
						oldfunction.apply(this, arguments);
					};
				}());
			}, false);
		';	
		$document->addScriptDeclaration($js);		
		unset($js);
	}


	public function onAfterRender(){

		$app = JFactory::getApplication();
			 	
		if ($app->getName() != 'site') {
			return true;
		}
		
		$this->redim_support();		
		
		if($this->params->get("generate2","prepare")!="render"){
			return;
		}
	
		$buffer = JResponse::getBody();
		$this->_imagesizer_preg($buffer);
		JResponse::setBody($buffer);
	
		unset($buffer);

	}
	
	
	public function onContentBeforeDisplay($context, &$item, &$params, $page = 0){	


		if($this->params->get("readmore",0)!=1){
			return;
		}
		#echo JRequest::getVAR("view");
		$view=JRequest::getVAR("view");
		
		if($context!="com_content.category" and ($context!="com_content.article" and $view !="category" ) ){
			return;	
		}		
		
		if($view=="article"){
			return;	
		}
		

		if(isset($item->introtext)){
			#JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
			$this->_imagesizer_readmore=JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catid)); 
			$this->_imagesizer_preg($item->introtext);	
			$this->counter++;
		}else{
			$this->_imagesizer_readmore="";	
		}
		
	 
	}

 
	public function onContentPrepare($context, &$row, &$params, $page = 0){

		if($this->params->get("generate2","prepare")!="prepare"){
			return;
		}
		
		if($this->params->get("readmore",0)==1){
			if($context=="com_content.category"){
				return;	
			}
		}


 	#   $regex="/\<img (.*?)\>/i";
	#	$regex="/\<a (.*?)>(.*?(?=<img ).*?)\<\/a>/i";
	#	$regex="/(?=<a )\<img (.*?)\>/i";
		if(!isset($row->id)){
			$row->id=$this->counter;
		}

		$this->article=$row;

		if(isset($row->text)){
			$this->_imagesizer_preg($row->text);			
		}
		
		if(isset($row->introtext)){
			$this->_imagesizer_preg($row->introtext);			
		}
		
		if(isset($row->fulltext)){
			$this->_imagesizer_preg($row->fulltext);			
		}	

		$this->counter++;
		
				
	}		
	

}

