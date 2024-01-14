<?php
/**
 * @package	ImageSizer - redim_imagesizer_class.php
 * @version	3.2.11
 * @author	reDim GmbH
 * @copyright	(C) 2009-2015 reDim GmbH All rights reserved.
 * @license	https://www.redim.de/redim-lizenz-v1
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class redim_imagesizer_class extends JPlugin {
    
	public $errors=array();
	public $created_pics=0;
	public $deleted_pics=0;
	public $counter=0;
	public $article=NULL;
	
	public $_imagesizer_readmore="";
	public $_lbfunction=false;
	
	public $load_java="";    

	public function clean_include_documentpath($file,$lang=true) {

		$document   = JFactory::getDocument();	
		#JPATH_BASE;
		if(substr($file,0,1)=="/"){$file=substr($file,1);}
		if(substr($file,-1,1)=="/"){$file=substr($file,0,-1);}
		
		$parts = pathinfo($file);	
		$path=$parts['dirname'];
		$name=$parts['basename'];

		$tmp=$path."/".$name;	
		
		if($lang==true){
			$lang = JFactory::getLanguage();
			$l=substr($lang->getTag(),0,2)."_";		
			if(file_exists(JPATH_SITE."/".$path."/".$l.$name)){
				$tmp=$path."/".$l.$name;
			}
		}
		
		if(substr($tmp,0,1)=="/") {
			$tmp=substr($tmp,1);
		}

		$root = JURI::root(true);
		
		if(substr($root,-1,1)!="/") {
			$root.= '/';
		}

		$tmp=$root.str_replace(DIRECTORY_SEPARATOR,'/',$tmp);

		return $tmp;
	
	}    
	
	public function Includefiles(){
		
		if(defined("imagesizer_filesload")){
			return;
		}
		
		jimport( 'joomla.html.parameter' );
		define("imagesizer_filesload",1);
	
		$file=$this->params->get("lbscript","default.php");
		
		$name="Imagesizer_addon_".substr($file,0,-4);
		if($file=="-1" or empty($file)){$file="default.php";}
		$file=JPATH_SITE.DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."system".DIRECTORY_SEPARATOR."imagesizer".DIRECTORY_SEPARATOR."lbscripts".DIRECTORY_SEPARATOR.$file;

		if(file_exists($file)){
			include_once($file);
			if (function_exists($name)) {
		 		$this->_lbfunction=$name;
		 	}			
		}		
		
	}

	 function send_helpdata($email="",$text=""){

		if(empty($email)){
			$user = JFactory::getUser();
			$email = $user->get("email");	
		}

		$text=trim($text);
		if(empty($text)){
			return false;
		}

		jimport('joomla.mail.helper');
		
	 	if(!JMailHelper::isEmailAddress($email)){
			return false;
		}

		
		$ar=$this->params->toarray();

		$config = JFactory::getConfig();

		#echo $config->getValue('sitename');
		
		$xml=JPATH_SITE.DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."system".DIRECTORY_SEPARATOR."imagesizer".DIRECTORY_SEPARATOR."imagesizer.xml";
		if(file_exists($xml)){
		#	$ar["redim_id"]=$this->_redim_id;
			$xml = JFactory::getXML($xml);
			$ar["redim_name"]=$xml->name;		
			$ar["redim_version"]=$xml->version;
		}


		$ar["joomla"]=JVERSION;
		$ar["site"]=JURI::root(false);
		$ar["file"]=__FILE__;	
		
		$ar["ftp_enable"]=$config->get('ftp_enable',"");
		
		$ar["thumbspath"]=$this->params->get("thumbspath","");
		$ar["chmod"]=JPath::clean(JPATH_SITE.DIRECTORY_SEPARATOR.$ar["thumbspath"]);
		$ar["chmod"]=substr(decoct(fileperms($ar["chmod"])),1);
		
		if ( is_writeable (JPATH_SITE.DIRECTORY_SEPARATOR.$ar["thumbspath"] ) ){
			$ar["dirwriteable"]="yes";	
		}else{
			$ar["dirwriteable"]="no";			
		}
		
		$body="";
		
		$body=$ar["redim_name"]." ".$ar["redim_version"] ."\n\n";
		
		$body.=$text."\n\n\n";
		
		foreach($ar as $k => $v){
			$body.=$k.": ".$v."\n";	
		}


		jimport('joomla.mail.mail');
		// Create a JMail object
		$mail	= JMail::getInstance();
		$mail->IsHTML(false);
		$mail->addRecipient( "support@redim.de" );	
		$mail->setSender(  $email , $email );
		$mail->addReplyTo(  $email , $email  );

		$title="Help&Support: ".$ar["redim_name"]." ".$ar["redim_version"];
	
		$mail->setSubject($title);
	#	$body=$this->sethtmlheader($title,$body);

		$mail->setBody( $body );

		return $mail->Send();	

	}

	function redim_support(){

	#	if($this->params->get("supportredim","1")!="1" ){	return;	}
	#	if( $this->counter==0){	return;	}
		if(defined("REDIM_DOWNLOADKEY")){return;}
		if(defined("redim_support")){return;}

		$buffer = JResponse::getBody();
		
		$uri = JURI::getInstance();
		$u=$uri->getPath();
		$c='';

		if(!strpos($_SERVER["REQUEST_URI"],"?") and ($u=='' OR $u=='/')) {
			/*  free version of imagesizer - do not remove this links */
			$c='<noscript>Diese Webseite nutzt die kostenlose Version ImageSizer für Joomla! - Dieses Joomla!-Plugin wurde von der <a title="ImageSizer entwickelt von der Internetagentur reDim GmbH" href="https://www.redim.de/">Internetagentur reDim</a> entwickelt - Bitte aktivieren Sie JavaScript.</noscript>';
		}

		$this->_crbl($c,$buffer);
			
		unset($buffer);
	}
	 
	 function _crbl($code,$buffer){
		define("redim_support",1);	
		if(!empty($code)){
			$c="\n".$code."\n";
			if($buffer = preg_replace('/<\/body>(?!.*<\/body>)/is',$c.'$0',$buffer,1)){	JResponse::setBody($buffer);}
		}
	}
	 
	 function _imagesizer_preg(&$text){

#		$regex="/\<a (.*?)>(.*?(?=\<img ).*?)\<\/a>/i";
		$regex="/\<a (.*?)>(.*?)\<\/a>/i";
		$text = preg_replace_callback($regex,array($this,"imagesizer"),$text);

	    $regex="/\<img (.*?)\>/i";
#		$searchImage	=	"(<img[^>]+>)";

	    $text = preg_replace_callback($regex,array($this,"imagesizer"),$text);	    
		$text = preg_replace("/<#img /i","<img ",	$text );

		$this->get_errors();
#		$text = preg_replace_callback('/\{(imagesizer)\s*(.*?)\}/i',array($this,"imagesizer_cmd"), $text);
		
	}
	
	 function imagesizer_cmd(&$matches){
		
		if(!isset($matches[2])){
			return $matches[0];
		}

		jimport('joomla.filesystem.file');

		$p=$this->match_to_params($matches[2]);
		
		$path=JPath::clean(trim($p->get("path","")));
		$limit=$p->get("limit","");
		$limit=explode(",",$limit);
		if(isset($limit[1])){
			$start=$limit[0];
			$limit=$limit[1];
		}else{
			$start=0;
			$limit=(int) $limit[0];
		}

		if(substr($path,-1,1)==DIRECTORY_SEPARATOR){
			$path=substr($path,0,-1);
		}
		jimport('joomla.filesystem.folder');
		$files	= JFolder::files(JPATH_SITE.DIRECTORY_SEPARATOR.$path, '\.png$|\.gif$|\.jpg$|\.PNG$|\.GIF$|\.JPG$',false,false);
        $LiveSite = JURI::root();
        
		$imagesizer2_x=$this->params->get("minsizex",120);
		$imagesizer2_y=$this->params->get("minsizey",120);
		$imgs=array();

		$class=$p->get("class",false);
		$style=$p->get("style",false);
		
		$class = ($class) ? ' class="'.$class.'"' : '';
		$style = ($style) ? ' style="'.$style.'"' : '';


		$ii=0;
		foreach($files as $i => $file){
			
			if( ($i>=$start and $ii<$limit) or $limit==0){
			
				if($info = @getimagesize(JPATH_SITE.DIRECTORY_SEPARATOR.$path.DIRECTORY_SEPARATOR.$file)){
				
					if(count($info)>2){
						
						$ii++;
	
			            if ($info[0] > $imagesizer2_x OR $info[1] > $imagesizer2_y){
			
			                $faktor = 0;
			
			   				if ($info[0]>$info[1] || $info[0]==$info[1]){
			   					$faktor = $info[0] / $imagesizer2_x ;
			   				}
			
			    			if ($info[0]<$info[1]){
			   					$faktor =  $info[1] / $imagesizer2_y ;
			   				}
			
			                if ($faktor>0){
			                   $xx = round( $info[0] / $faktor , 0);
			                   $yy = round( $info[1] / $faktor , 0);
			                }
						}else{
							$xx=$info[0];
							$yy=$info[1];
						}
		 
						$imgs[]='<img src="'.$LiveSite.str_replace("\\","/",$path.DIRECTORY_SEPARATOR.$file).'" width="'.$xx.'" height="'.$yy.'" '.$class.$style.' />';
		
					}			
				
				}
				
			}
			
			
		}

		if(count($imgs)){
			return implode("",$imgs);
		}
		
		return $matches[0];

	}

	 function calc_size($ar,$info){
		
		if(!is_array($ar)) {
			$ar=(array) $ar;
		}			
		
		if(!isset($ar["width"])) {
			$ar["width"]=0;	
		}
		if(!isset($ar["height"])) {
			$ar["height"]=0;	
		}

		 if(!is_array($info)) {
			 $info=(array) $info;
		 }

		 if(!isset($info[0])) {
			$info[0]=0;
		}
		 if(!isset($info[1])) {
			 $info[1]=0;
		 }
		
		$ar["width"]=trim($ar["width"]);
		$ar["height"]=trim($ar["height"]);

		if(substr($ar["width"],-1,1)=="%"){
			$ar["width"]=@round(($info[0]/100)*intval($ar["width"]));
		}

		if(substr($ar["height"],-1,1)=="%"){
			$ar["height"]=@round(($info[1]/100)*intval($ar["height"]));			
		}		
		
		$ar["width"]=intval($ar["width"]);
		$ar["height"]=intval($ar["height"]);
		
		if($ar["width"]>0 and $ar["height"]==0){
			$factor=@round($info[0] / $ar["width"], 2);
			$ar["height"]= @round($info[1] / $factor, 0);
			unset($factor);
		}elseif($ar["width"]==0 and $ar["height"]>0){		
			$factor=@round($info[1] / $ar["height"], 2);	
			$ar["width"]= @round($info[0] / $factor, 0);
			unset($factor);			
		}

		return $ar;		

	}

	 function check_imgparams($ar){

		$ar["ext"]="";
		if(!isset($ar["src"]) OR empty($ar["src"])){ return $ar; }
		$url=parse_url($ar["src"]);
		if(isset($url["path"])){$ar["src"]=$url["path"];}
		if(isset($url["scheme"])){$url["scheme"]="http";}
		if(substr($ar["src"],0,1)!="/"){$ar["src"]="/".$ar["src"];}
		if(strtolower(substr($ar["src"],0,11))=="/templates/"){	return $ar;	}
		if(isset($url["host"])){
			if(substr($ar["src"],0,1)=="/"){$ar["src"]=substr($ar["src"],1);}		 
			if(substr($url["host"],-1,1)!="/"){$url["host"]=$url["host"]."/";}		 
			$url2=parse_url(JURI::root());
			if(strtolower($url2["host"]."/")!=strtolower($url["host"])){$ar["ext"]=$url["scheme"]."://".$url["host"].$ar["src"];}
			unset($url2);
		}

		unset($url);
		return $ar;
		
	}

	 function checkmode_from_class($class,$default="equal"){

		$class="  ".$class."  ";

		if ( strpos ( $class , ' imgcut ' ) )
		{
		 	return "cut";
		}
		
		if ( strpos ( $class , ' imgzoom ' ) )
		{
		 	return "zoom";
		}
		
		if ( strpos ( $class , ' imgbig ' ) )
		{
		 	return "big";
		}
		
		if ( strpos ( $class , ' imgsmall ' ) )
		{
		 	return "small";
		}

		return $default;
		
	}

	
	 function clean_url($var){

	    $var = str_replace('&amp;', '&', $var);
	    $var = str_replace('&lt;', '<', $var);
	    $var = str_replace('&gt;', '>', $var);
	    $var = str_replace('&euro;', '€', $var);
	    $var = str_replace('&szlig;', 'ß', $var);
	    $var = str_replace('&uuml;', 'ü', $var);
	    $var = str_replace('&Uuml;', 'Ü', $var);
	    $var = str_replace('&ouml;', 'ö', $var);
	    $var = str_replace('&Ouml;', 'Ö', $var);
	    $var = str_replace('&auml;', 'ä', $var);
	    $var = str_replace('&Auml;', 'Ä', $var);
	    
	    if(substr($var,0,1)!="/"){
	     	if(substr($var,0,7)!="http://"){
				$var="/".$var;				
			}
		}
		
		$p=JURI::root();		
				
		$l=strlen($p);
		if(substr($var,0,$l)==$p){$var=substr($var,$l);}
		
		return $var;
		
	}

	 function get_baseurl(){
		
		if(!defined("imagesizer_uribase")){
		
			$url=JURI::base(true);
			if(!empty($url)){
				if(substr($url,-1,1)!="/"){
					$url.="/";	
				}	
			}
			
			define("imagesizer_uribase",$url);
			unset($url);
		
		}

		return imagesizer_uribase;

	}
	
	 function combine_path($a,$b){
		
		if(!empty($a)){
			if(substr($a,-1,1)!="/"){
				$a.="/";	
			}			
		}
		if(!empty($b)){	
			if(substr($b,0,1)=="/"){
				$b=substr($b,1);
			}	
		}
		
		return $a.$b;
		
	}

	 function imagesizer($matches){

		$sharpit=false;
		
		if(count($matches)>2){
			if(isset($matches[2])){
				$ar=$this->make_arrays($matches[2],'/([a-zA-Z0-9._-]+)="(.*?)"/');
				$sharpit=true;
			}else{
				return $matches[0];
			}
		}else{
			$ar=$this->make_arrays($matches[1],'/([a-zA-Z0-9._-]+)="(.*?)"/');			
		}

		if(!isset($ar["src"])){
			return $matches[0];
		}else{
		 	if(preg_match("/data\:image\//i",$ar["src"])){
				return $matches[0];
			}
			if(isset($ar["class"])){
		    	if(preg_match("/ nothumb /i"," ".$ar["class"]." ")){
					return $matches[0];
				}
			}
			
		}

        $LiveSite = JURI::root();

		$this->Includefiles();

		$cachefolder=$this->params->get("thumbspath","cache");

		$output=array();

		$ar["width"]=intval($ar["width"]);
		$ar["height"]=intval($ar["height"]);

		$ar=$this->check_imgparams($ar);

		if(empty($ar["src"])){
			return $matches[0];
		}else{
			$ar["src"]=$this->clean_url($ar["src"]);
		}
		$ar["data-old"]=$ar["src"];
		if(empty($ar["width"]) AND empty($ar["height"])){
			return $matches[0];
		}
	
		if(isset($ar["class"])){
			$ar["class"].=" ".$this->params->get("picclass","thumb");
		}else{
			$ar["class"]=$this->params->get("picclass","thumb");	
		}
		$ar["class"]=trim($ar["class"]);

		$mode=$this->checkmode_from_class($ar["class"].$this->params->get("imgmode","equal"));

		if(!empty($ar["ext"])){	
			$ar["src"]=$ar["ext"];			
		}
		
		$url_array = parse_url($ar["src"]);

		$ar["src"]=str_replace(JURI::base(true),"",$ar["src"]);
		$ar["href"]=$this->combine_path(JURI::base(true),$ar["src"]);
		
		$this->counter++;
           
	
		if(!empty($ar["ext"])){
			$info=@getimagesize($ar["ext"]);
		}else{
			#$info=@getimagesize(JPath::clean(JPATH_ROOT.DIRECTORY_SEPARATOR.$ar["src"]));	
			$info=@getimagesize(JPath::clean(JPATH_ROOT.DIRECTORY_SEPARATOR.urldecode($ar["src"])));			
		}
		if(!$info){
			$this->_loadLanguage();
			$this->set_error("ERROR-".$ar["src"],JText::sprintf('IMAGESIZER_ERR_ACCESS',JPath::clean($ar["src"]))); 
		}

		$ar=$this->calc_size($ar,$info);

		if($ar["width"]==$info[0] AND $ar["height"]==$info[1]){
			return $matches[0];
		}

		if(!isset($info[0]) OR !isset($info[1])) {
			return $matches[0];	
		}

		if($info[0]<2 AND $info[1]<2){
			return $matches[0];
		}

		if(isset($this->article->id)){
			$id=intval($this->article->id);
		}else{
			$id=0;
		}
		
		if($id==0){	$id="i".JRequest::getINT("Itemid");	}

		if(!empty($ar["ext"])){
			if($this->params->get("urldecode",1)==1){
				$file=urldecode($ar["ext"]);	
			}else{
				$file=$ar["ext"];
			}
		}else{		
			if($this->params->get("urldecode",1)==1){
				$file=urldecode($ar["src"]);	
			}else{
				$file=$ar["src"];
			}
		}

		$maxx=$this->params->get("maxsizex",800);
		$maxy=$this->params->get("maxsizey",800);
		$imgmode=$this->params->get("imgmode",800);
		$chmod=$this->params->get('chmod',"0775");

		if($info[0]>$maxx or $info[1]>$maxy){	
			$maxfile = $this->get_imagesrc($file,$maxx,$maxy,$imgmode,false,$cachefolder,$chmod);
		}else{
			$maxfile=$file;
		}
            

		if(empty($ar["ext"])){	 
			$file=JPath::clean(JPATH_ROOT.DIRECTORY_SEPARATOR.$file);
		}

		$tmp=$file;

		$thumbfile = $this->get_imagesrc($file,$ar["width"],$ar["height"],$mode,false,$cachefolder,$chmod);

		if($mode != "equal" and $mode !="zoom"){
			unset($ar["width"],$ar["height"]);
		}


		$temp_src=$ar["src"];
		$ar["src"]=str_replace('\\',"/",$thumbfile);
		$ar["href"]=$this->combine_path(JURI::base(true),$maxfile);


		if(preg_match("/ nolightbox /i"," ".$ar["class"]." ")){
			$sharpit=true;
		}


		if($sharpit==true){
			$output=$this->onlythumb($ar,$img);
			if(substr($temp_src,0,1)=="/"){
				$temp_src=substr($temp_src,1);
			}
			$output=str_replace($temp_src,$ar["src"],$matches[0]);
			$output = preg_replace("/<img /i","<#img ",	$output );
		}else{
#			$output=Imagesizer_addon_GetImageHTML($ar,$img,$this);	
			$output=plgSystemimagesizer::Imagesizer_addon_GetImageHTML($ar,$this);
			
		}
			
		unset($img);

        return $output;
		
	}
	

	public function Imagesizer_addon_GetImageHTML($ar,&$obj){

		if(!empty($obj->_imagesizer_readmore)){

			$output=plgSystemimagesizer::make_img_output($ar);
			$title="";
			if(isset($ar["title"])){
				if(!empty($ar["title"])){
					$title=' title="'.$ar["title"].'"';
				}
			}else{
				if(isset($ar["alt"])){
					if(!empty($ar["alt"])){
						$title=' title="'.$ar["alt"].'"';
					}
				}
			} 
		
			return '<a'.$title.' class="'.$obj->params->get("linkclass","linkthumb").'" href="'.$obj->_imagesizer_readmore.'"><img '.$output.' /></a>';	 

		}else{	

			if( $obj->_lbfunction){
				$name=$obj->_lbfunction;
		 		return $name($ar, $obj);
		 	}else{
				return "";
			}
			#return Imagesizer_addon_GetImageHTML($ar,$obj);	
		}
	}

	 function get_errors(){
		
		if($this->params->get("geterrors","1")!="1" ){	return;	}	
		
		if(count($this->errors)>0){
			foreach($this->errors as $k => $err){
				JError::raiseNotice($k,$err);		
			}
			$this->errors=array();			
		}
		return true;
		
	}

	 function set_error($id,$error){
		$id=JApplication::getHash($id);
		$this->errors[$id]=$error;
		
	}

	 function get_ReadmoreImageHTML($ar=array(),$img){

		$output=plgSystemimagesizer::make_img_output($ar);

		if(isset($ar["title"])){
			$title=' title="'.$ar["title"].'"';
		}else{
			$title="";
		} 

		$output='<a class="'.trim($this->params->get("linkclass","linkthumb")).'" target="_self" title="'.$ar["title"].'" href="'.$ar["href"].'"><img '.$output.' /></a>';	
	
		return $output;
		
	}

	 function onlythumb(&$ar,&$img){

		$output=plgSystemimagesizer::make_img_output($ar,true);
			
		return $output;
	
	}

	public static function make_img_output($ar,$protect=false){

		$output=array();

		foreach($ar as $key => $value){
		 
		 	if(trim($value)!=""){
		 	 
				switch($key){
					
					case 'src':
					$value=redim_imagesizer_class::check_url($value);
					$output[]=$key.'="'.$value.'"';
					break;
					

					case 'href':
					$value=redim_imagesizer_class::check_url($value);
					break;
					
					case 'owidth':
					case 'oheight':
					break;
					
					default:
					$output[]=$key.'="'.$value.'"';
					break;
				}
			 
			}
		}
		$output=implode(" ",$output);

		return $output;
	}

	public function make_arrays($matches,$regex='/([a-zA-Z0-9._-]+)=[\'\"](.*?)[\'\"]/'){
 			
 		$ar=array();
 		$matches2=array();
 
        preg_match_all($regex, $matches, $matches2);
				
        foreach($matches2[1] as $key => $value) {
            $value=trim($value);
            if (isset($ar[strtolower($value)])){
				$value=strtolower($value);
			}
            $ar[$value]=$matches2[2][$key];
        }
        
 		if (isset($ar["style"])){
			$ar2=plgSystemimagesizer::Get_WH_From_Style($ar["style"]);
			if (isset($ar2["width"])){$ar["width"]=$ar2["width"];}
			if (isset($ar2["height"])){$ar["height"]=$ar2["height"];}
			unset($ar2);
		}       
		
 		if (isset($ar["width"])){	
        	$ar["width"]=intval($ar["width"]);
        }else{
			$ar["width"]="";
		}
    
 		if (isset($ar["height"])){	
        	$ar["height"]=intval($ar["height"]);
        }else{
			$ar["height"]="";
		}
        
		return $ar;
	}


	 function Get_WH_From_Style($style){
		$style.=";";
		
		$matches=array();
		$ar=array();

		$regex='/(border-width|width|height):(.*?)(\;)/i';
 		preg_match_all($regex, $style, $matches);

		foreach($matches[1] as $key => $value) {
			if (isset($matches[2][$key])){
			 	$matches[2][$key]=trim($matches[2][$key]);
			 	if(substr($matches[2][$key],-1,1)!="%"){
					$k=strtolower(trim($value));
					$ar[$k]=trim($matches[2][$key]);
				}
			}
		}		
	
		return $ar;
	}



	 function match_to_params($match){

		$ar=array();
		$ar["style"]="";
		$m=array();
		$str="";
		
		preg_match_all('/(.*?)=(.*?)[\'\"](.*?)[\'\"]/', $match, $m);	
		
		if (count($m[1])>0){
			foreach($m[1] as $key => $value) {
				$ar[strtolower(trim($value))]=$m[3][$key];
			   	$str.=strtolower(trim($value))."=".$m[3][$key]."\n";
			   
			}
		}
		
		preg_match_all("/(.*?)=(.*?)[\'\"](.*?)[\'\"]/", $match, $m);

		if ($ar["style"]!=""){
			$b=plgSystemimagesizer::Get_WH_From_Style($ar["style"]);
			if(count($b)>0){
				foreach($b as $key => $value) {
				 $m[1][$key]=$key;
				 $m[3][$key]=$value;
				}				
			}
		}

		if (count($m[1])>0){
			foreach($m[1] as $key => $value) {
			  	 $ar[strtolower(trim($value))]=$m[3][$key];
				 $str.=strtolower(trim($value))."=".$m[3][$key]."\n";
			}

		}

		$params = new JRegistry($str);
		$params->img_data=$ar;

		return $params;  		
	}



	public function get_folderandfile(&$file,$width=0,$height=0,$modus="big",$cachefolder="cache",$chmod="0775"){

		$l=strlen(JPATH_SITE);
		if(substr($file,0,$l)==JPATH_SITE){
			$file=substr($file,$l);
		}
		$temp=substr($file,0,1);
		if($temp=="/" or $temp=="\\"){
			$file=substr($file,1);
		}
		
		#	$typename = substr(strrchr($file,'.'),1);
		#	$newfile=JApplication::getHash($file.$width."x".$height."-".$modus).".".$typename;	
		#	$c=substr(strtolower($newfile),1,1);

		$a=@dirname($file);
		$b=@basename($file);
		$b=@explode('.',$b);
		$typename=@array_pop($b);
		$b=@implode('.',$b);
		$newfile=$b.'_'.$width."x".$height."-".$modus.'.'.$typename;
		
		if(strpos(' '.$a,$cachefolder)==1) {
			$c=$a;
		}else{
			$c=$cachefolder.DIRECTORY_SEPARATOR.$a;
		}
		
		

		if(!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.$c)){

		 	jimport('joomla.filesystem.folder'); 
			jimport('joomla.client.helper');

			$FTPOptions=JClientHelper::getCredentials('ftp');
			if($FTPOptions['enabled']==1){
			 	$chmod=0777;
			}else{
				$chmod=base_convert(intval($chmod), 8, 10);
			}

			if(isset($this->params)){
				if($this->params->get("jfile",1)==1){
			        if(!JFolder::create(JPATH_SITE.DIRECTORY_SEPARATOR.$c,$chmod)){	 
			 			$this->_loadLanguage();
						$this->set_error("ERROR-".JPath::clean($c),JText::sprintf('IMAGESIZER_ERR_ACCESS',JPath::clean($c))); 			 
					}
				}else{				
					if(!@mkdir(JPATH_SITE.DIRECTORY_SEPARATOR.$c)){
			 			$this->_loadLanguage();
						$this->set_error("ERROR-".JPath::clean($c),JText::sprintf('IMAGESIZER_ERR_ACCESS',JPath::clean($c))); 			 
					}				
				}
			}else{
				@mkdir(JPATH_SITE.DIRECTORY_SEPARATOR.$c);
                $chmod=$this->params->get('chmod',"0775");
                @chmod(JPATH_SITE.DIRECTORY_SEPARATOR.$c,base_convert(intval($chmod), 8, 10));
			}

		}
		
		$newfile=$c.DIRECTORY_SEPARATOR.$newfile;		
		
		return $newfile;
	}

	 function _loadLanguage($extension = '', $basePath = JPATH_ADMINISTRATOR)
	{
		if(defined("IMAGESIER_LANG_LOAD")){
			return;
		}
		$lang = JFactory::getLanguage();
 		$lang->load("plg_system_imagesizer.sys",JPATH_ADMINISTRATOR);
		define("IMAGESIER_LANG_LOAD",1);

	}

 	public function onSizeImage($file,$width=0,$height=0,$modus="big",$updatecache=false,$cachefolder='',$chmod="0775"){
	    /*  use this :
        $dispatcher = JEventDispatcher::getInstance();
        $temp = $dispatcher->trigger( 'onSizeImage', array( &$pic,$x,$y,$mod ) );        
        */
		if(empty($cachefolder)) {
			$cachefolder=$this->params->get("thumbspath","cache");
		}

        $file=$this->get_imagesrc($file,$width,$height,$modus,$updatecache,$cachefolder,$chmod);
        
        return $file;
    
    }
	
	public static function check_url($url){
	
		if(substr($url,0,1)!="/"){
			if(substr($url,0,5)!="http:" and substr($url,0,6)!="https:"){
				$url="/".$url;
			}
		}
		
		$base=JURI::base(true);
		$l=strlen($base);
		if(substr($url,0,$l)!=$base){
			$url=$base.$url;
		}
		#echo JURI::base(true);
		
		
		#$x[$c]=rawurlencode($x[$c]);
		#$x=explode("/",$ar["href"]);
		#$c=count($x)-1;
		#$x[$c]=rawurlencode($x[$c]);
		#$x="/".implode("/",$x);		
		
		#$url=JPath::clean($url);
		$url=str_replace(DIRECTORY_SEPARATOR,'/',$url);

		return $url;
	
	}


	 function get_imagesrc($file,&$width=0,&$height=0,$modus="equal",$updatecache=false,$cachefolder="cache",$chmod="0775"){

        if(empty($file)){
            return;
        }

		if($width>0 and $height==0){
			$height=$width;
		}elseif($width==0 and $height>0){
			$width=$height;
		}		
		
		self::loadLanguage();

		if(isset($this)){
			$pro=(int) $this->params->get("pro",85);
		}else{
			$pro=85;
		}
		
		$newfile=plgSystemimagesizer::get_folderandfile($file,$width,$height,$modus,$cachefolder,$chmod);			

		if(!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.$newfile) or $updatecache==true){

		 	if($width>0 and $height>0){
					
				$temp=plgSystemimagesizer::get_folderandfile($file,$width,$height,$modus,$cachefolder,$chmod);

				if (substr($file,0,7)=="http://"){		
					$content = file_get_contents($file);
					$fp = @fopen(JPATH_SITE.DIRECTORY_SEPARATOR.$temp, "w"); 
					@fwrite($fp, $content); 
					@fclose($fp);
					unset($content,$fp);
				 	#if(@copy($file,JPATH_SITE.DIRECTORY_SEPARATOR.$temp)){
				 	if(file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.$temp)){
						$file=$temp;
					}else{
						return $file;
					}
				}else{
					if($this->params->get("jfile",1)==1){
	                    jimport('joomla.filesystem.file');

	                    if( JFile::copy(JPATH_SITE.DIRECTORY_SEPARATOR.$file, JPATH_SITE.DIRECTORY_SEPARATOR.$temp)){
							$file=$temp;
						}else{
							return $file;
						}
					}else{
						if(@copy($file,$temp)){
							$file=$temp;
						}else{
							return $file;
						}
					}

				}

			#	include_once(JPATH_SITE.DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."system".DIRECTORY_SEPARATOR."imagesizer".DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR."redim_img.php");
			#	$img= new PicEdit(JPATH_SITE.DIRECTORY_SEPARATOR.urldecode($file));
			#	$img->create($width,$height,$modus,JPATH_SITE.DIRECTORY_SEPARATOR.$newfile);
				$image = new JImage();
				$image->loadFile(JPATH_SITE.DIRECTORY_SEPARATOR.$file);
				$properties = JImage::getImageFileProperties(JPATH_SITE.DIRECTORY_SEPARATOR.$file);

				$mime = $properties->mime;
				switch($mime){
				 	case 'image/jpeg':
				 	$type = IMAGETYPE_JPEG;
				 	break;

				 	case 'image/png':
				 	$type = IMAGETYPE_PNG;
				 	break;
				 	
				 	case 'image/gif':
			 	  	$type = IMAGETYPE_GIF;
				 	break;
				}


				switch($modus){
					case 'cut';
				#	$resizedImage=$image->crop($width, $height,true);
                    $resizedImage=$image->cropResize($width, $height, true);
					break;

					case 'equal';
					$resizedImage=$image->resize($width, $height,true, JImage::SCALE_INSIDE);
					break;

					case 'zoom';
					$resizedImage=$image->resize($width, $height,true, JImage::SCALE_FILL);
					break;

					case 'small';
					$resizedImage=$image->resize($width, $height,true, JImage::SCALE_INSIDE);
				#	$resizedImage=$resizedImage->crop($width, $height,false, JImage::SCALE_OUTSIDE);
					break;

					case 'big';
					$resizedImage=$image->resize($width, $height,true, JImage::SCALE_OUTSIDE);
					$resizedImage=$resizedImage->crop($width, $height,true, JImage::SCALE_INSIDE);
					break;
					
					default:
					$resizedImage=$image->resize($width, $height,true,JImage::SCALE_FILL);	
					break;	
						
				}
				$option=array();
				if($type==IMAGETYPE_PNG){
					$option["quality"]=round(($pro/10),0);
					if($option["quality"]>9) {
						$option["quality"]=9;	
					}
				}else{
					$option["quality"]=$pro;
				}
                $resizedImage->toFile(JPATH_SITE.DIRECTORY_SEPARATOR.$newfile,  $type,$option);
                
                $copyrightpic=JPATH_SITE.DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."system".DIRECTORY_SEPARATOR."imagesizer".DIRECTORY_SEPARATOR."copyright.png";
                if(file_exists($copyrightpic)){
                    if($width>600 and $height>600){
                    
                    $info1 = getimagesize(JPATH_SITE.DIRECTORY_SEPARATOR.$newfile);
		            $info2 = getimagesize($copyrightpic);
                    $w=$info1[0] - $info2[0];
                    $h=$info1[1] - $info2[1];
				
    				switch($mime){
    				 	case 'image/jpeg':
  				 	    $Grafik1=imagecreatefromjpeg(JPATH_SITE.DIRECTORY_SEPARATOR.$newfile);
    				 	break;
    
    				 	case 'image/png':
    				    $Grafik1=imagecreatefrompng(JPATH_SITE.DIRECTORY_SEPARATOR.$newfile);
    				 	break;
    				 	
    				 	case 'image/gif':
 			 	  	    $Grafik1=imagecreatefromgif(JPATH_SITE.DIRECTORY_SEPARATOR.$newfile);
    				 	break;
    				}



                    $Grafik2=imagecreatefrompng($copyrightpic);
                    #imagecopy($Grafik1, $Grafik2, $w, $h,0,0,$width,$height);      

					imagecopy($Grafik1, $Grafik2,$w,$h,0,0,$info2[0],$info2[1] );
    				switch($mime){
    				 	case 'image/jpeg':
    				 	 $check=@imagejpeg($Grafik1, JPATH_SITE.DIRECTORY_SEPARATOR.$newfile ,85);
    				 	break;
    
    				 	case 'image/png':
    				 	$check=@imagepng($Grafik1, JPATH_SITE.DIRECTORY_SEPARATOR.$newfile);
    				 	break;
    				 	
    				 	case 'image/gif':
    			 	  	$check=@imagegif($Grafik1, JPATH_SITE.DIRECTORY_SEPARATOR.$newfile , 85);
    				 	break;
    				}
                        
                
             

                 #   echo $width."x".$height;
                #    echo $copyrightpic;
  
 #                   die();
  
                    }
  
                }

               
                
				if(file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.$newfile)){
					$this->created_pics++;					
				}else{
					$this->set_error("ERR-".$newfile,JText::sprintf("IMAGE_CREATE_ERROR",$newfile));
				}


				
		        if (!empty($chmod)){
		          @chmod(JPATH_SITE.DIRECTORY_SEPARATOR.$newfile,base_convert(intval($chmod), 8, 10));
		        }	


	/*
				if(count($img->err)>0){
		 			$this->_loadLanguage();
					foreach($img->err as $temp){
						$this->set_error("ERR-".$newfile,JText::sprintf("IMAGE_CREATE_ERROR",$newfile));
					} 
				}else{
					$this->created_pics++;
					$width=$img->new_width;
					$height=$img->new_height;
				}				
	*/						
				unset($image,$resizedImage);

				
			}else{
				$newfile=$file;
			}
		}
		
		$config = JFactory::getConfig();
		if($config->get('sef_rewrite')==1){
			if(substr($newfile,0,1)!="/"){
			#	$newfile="/".$newfile;				
			}		
		}
	

		return $newfile;
		
	}

    
    
    
}