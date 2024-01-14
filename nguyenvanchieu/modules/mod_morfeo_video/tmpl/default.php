<?php // no direct access

defined('_JEXEC') or die('Restricted access'); ?>

<?php

//////////// rest color function



if($params->get('skin')!="") $skin=JURI::base().$params->get('skin');
else $skin="";

$texto='title='.$params->get('title').'&controls='.$params->get('controls').'&color1='.$params->get('color1').'&color2='.$params->get('color2').'&round='.$params->get('round').'&autoplay='.$params->get('autoplay').'&skin='.$skin.'&youtube='.$params->get('youtube').'&columns='.$params->get('columns').'&rows='.$params->get('rows').'&tumb='.$params->get('tumb').'&round='.$params->get('round').'&op1='.$params->get('op1').'&op2='.$params->get('op2').'&op3='.$params->get('op3').'&op4='.$params->get('op4').'&op5='.$params->get('op5').'&imagealign='.$params->get('sizethumbnail').'&color3='.$params->get('color3').'&color4='.$params->get('time').'&sizetitle='.$params->get('tborder').'&sizedescription='.$params->get('font');





$twidth=$params->get('columns');
$theight=$params->get('rows');


$links = array();
$titles = array();
$imagest= array();
$descriptionst= array();
$datest=array();

$mobpag=0;


$mobrow=0;
$mobcolumn=0;
$textovidmob="";
$firstimage="";
$firstlink="";
$firsttitle="";
$width=$params->get('width');
$height=$params->get('height');

$height=str_replace("px", "", $height);
$height=str_replace("%", "", $height);

$heightimage=round(((100-$params->get('tumb'))*$height)/100);
$heightthumb=round((($params->get('tumb'))*$height)/100);
$heightimage-=50;

$id=$module->id;
if($params->get('video')!="") $links=preg_split ("/\n/", $params->get('video'));
if($params->get('titles')!="") $titles=preg_split ("/\n/", $params->get('titles'));
if($params->get('images')!="") $imagest=preg_split ("/\n/", $params->get('images'));
if($params->get('descriptions')!="") $descriptionst=preg_split ("/\n/", $params->get('descriptions'));
if($params->get('dates')!="") $datest=preg_split ("/\n/", $params->get('dates'));

$cont1=0;

while($cont1<count($links)) {
	$auxititle="";
	$auxivideo="";
	$imageultimate="";
	$description="";
	$auxtipo=0;
	$datesaux="";
	if(isset($titles[$cont1])) $auxititle=$titles[$cont1];
	if(isset($links[$cont1])) $auxivideo=$links[$cont1];
	if(isset($imagest[$cont1])) $imageultimate=$imagest[$cont1];
	if(isset($descriptionst[$cont1])) $description=$descriptionst[$cont1];
	if(isset($datest[$cont1])) $datesaux=$datest[$cont1];
	
	$auxivideourl=$auxivideo;
	if($auxivideo!="") {
		$auxtipo=1;
		if(strstr($auxivideo, "http")) {
			if(strpos($auxivideo, "youtube")>0) {
				
				if($imageultimate=="") {
						
						
						$imageultimate='http://ytimg.googleusercontent.com/vi/'.getYTid($auxivideo).'/hqdefault.jpg';
					
					
					}
				
				$auxivideo=getYTid($auxivideo);
				$auxtipo=2;
				
			}
			else $auxtipo=1;
		}
		else {
			if($imageultimate=="") $imageultimate='http://ytimg.googleusercontent.com/vi/'.$auxivideo.'/hqdefault.jpg';
			$auxtipo=2;
		}
		

	}
	$texto.='&video'.$cont1.'='.$auxivideo.'&title'.$cont1.'='.$auxititle.'&tipo'.$cont1.'='.$auxtipo.'&image'.$cont1.'='.$imageultimate.'&description'.$cont1.'='.$description.'&date'.$cont1.'='.$datesaux;
	
	
	
	
	
	// mobile detect
	
							
						
						if($firstimage=="") $firstimage=$imageultimate;
						if($firstlink=="") $firstlink=$auxivideourl;
						if($firsttitle=="") $firsttitle=$auxititle;
						if($mobcolumn==0 && $mobrow==0) {
							
							 if($mobpag==0) $textovidmob.='<div id="ulpag'.$id.'-'.$mobpag.'" name="ulpag'.$id.'-'.$mobpag.'" ><table width="100%" >';
							 else $textovidmob.='<div id="ulpag'.$id.'-'.$mobpag.'" name="ulpag'.$id.'-'.$mobpag.'" style="display:none"><table width="100%">';
						}
						
						if($mobcolumn==0)  $textovidmob.='<tr>';
						
						
						
						$auxivideourl=trim($auxivideourl);
						$auxititle=trim($auxititle);
						$imageultimate=trim($imageultimate);
						
						$textovidmob.='<td width="'.round(100/$twidth).'%" height="'.round($heightthumb/$theight).'px" onclick="changevideo'.$id.'(\''.$imageultimate.'\', \''.$auxivideourl.'\', \''.$auxititle.'\');" style="background: url('.$imageultimate.') no-repeat center; text-align:center; padding:4px;vertical-align:middle;color: #'.$params->get('color3').'; font-size:'.($tborder).'px;">'.$auxititle.'<br/><img src="'.JURI::base().'modules/mod_morfeo_video/img/play.png" /></td>';
											
						
						 $mobcolumn++;
						 
						 if($mobcolumn>=$twidth) {
							 $mobcolumn=0;
							 $mobrow++;
							 $textovidmob.='</tr>';
						 }
						 if($mobrow>=$theight) {
							 $mobrow=0;
							 $mobpag++;
							 $textovidmob.='</table></div>';
						 }
						
	$cont1++;
	
	
	///////////////////////////////
	
	
}

  if($mobcolumn>0) {
	 
	 while($mobcolumn<$twidth) {
		 
		 $textovidmob.='<td></td>';
		 $mobcolumn++;
	 }
	  $textovidmob.='</tr>';
 }
 
 if($mobrow<$theight) {
							
							 $textovidmob.='</table></div>';
							 $mobpag++;
		}


$texto.='&cantidad='.$cont1;
function getYTid($ytURL) {
#
 
#
$ytvIDlen = 11; // This is the length of YouTube's video IDs
#
 
#
// The ID string starts after "v=", which is usually right after
#
// "youtube.com/watch?" in the URL
#
$idStarts = strpos($ytURL, "?v=");
#
 
#
// In case the "v=" is NOT right after the "?" (not likely, but I like to keep my
#
// bases covered), it will be after an "&":
#
if($idStarts === FALSE)
#
$idStarts = strpos($ytURL, "&v=");
#
// If still FALSE, URL doesn't have a vid ID
#
if($idStarts === FALSE)
#
die("YouTube video ID not found. Please double-check your URL.");
#
 
#
// Offset the start location to match the beginning of the ID string
#
$idStarts +=3;
#
 
#
// Get the ID string and return it
#
$ytvID = substr($ytURL, $idStarts, $ytvIDlen);
#
 
#
return $ytvID;
#
 
#
}


$ta1='
<style type="text/css">

			.movie'.$module->id.' {
				width: '.$params->get('width').';
				height: '.$params->get('height').';
			}
			div.movie'.$module->id.' {
				width: '.$params->get('width').';
				height: '.$params->get('height').';
				
			

			}
		</style>
';




////// mobile support

 require_once 'Mobile_Detect.php';
 $detect = new Mobile_Detect;
 $deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');


if($deviceType=='phone' || $deviceType=='tablet') {
	
	$output='
	
	<script>
	
	var pag=0;
	var anpag=0;
	var maxpag='.$mobpag.';
	
	function changevideo'.$id.'(urli, link, title) {
		
		
		jQuery("#mainvideomob'.$id.'").css("backgroundImage", "url("+urli+")");
		jQuery("#mainlinkmob'.$id.'").attr("href",link);
		jQuery("#maintitlemob'.$id.'").html(title);
	}
	
	
	
	
	function changepag'.$id.'(pasb) {
		
		if(pasb==1) pag++;
		else pag--;
		if(pag>=maxpag) pag=0;
		if(pag<0) pag=maxpag-1;
		jQuery("#ulpag'.$id.'-"+anpag).css("display", "none");
		jQuery("#ulpag'.$id.'-"+pag).show();
	
		anpag=pag;
	}
	
	
	</script>

	<div style="width:'.$width.'; margin:0px;overflow:hidden;background-color:#'.$params->get('color1').';" id="ultimate'.$id.'-'.$contador.'">
	
	
	<table width="100%" height="'.$heightimage.'px" style=" margin:0px;">
	<tr><td id="mainvideomob'.$id.'" name="mainvideomob'.$id.'" style="background: url('.$firstimage.') no-repeat center; text-align:center; vertical-align:middle;">
	
	<a href="'.$firstlink.'" id="mainlinkmob'.$id.'" name="mainlinkmob'.$id.'" target="_blank">
	
	<img src="'.JURI::base().'modules/mod_morfeo_video/img/play2.png" height="'.round($heightimage/2).'px" />
	</a>';
	
	
	if($mobpag>1) $output.='<img src="'.JURI::base().'modules/mod_morfeo_video/img/next2.png" height="'.round($heightimage/2).'px" style="position:absolute; right:0;z-index:999;" onclick="changepag'.$id.'(1);" />
	<img src="'.JURI::base().'modules/mod_morfeo_video/img/prev2.png" height="'.round($heightimage/2).'px" style="position:absolute; left:0;z-index:999;" onclick="changepag'.$id.'(0);" />';
	
	
	
	$output.='</td></tr></table>
<div id="maintitlemob'.$id.'" name="maintitlemob'.$id.'" style="height:50px; width:100%; color:#'.$color3.'; font-size:'.($tborder*2).'px; text-align: center;">'.$firsttitle.'</div>
	<div id="mainthumbmob'.$id.'" name="mainthumbmob'.$id.'">
  '.$textovidmob.'
  </div>
</div>
';
	
	
	echo $output;
	
}


else {

?>

<?php echo $ta1; ?>

<div class="movie<?php echo $module->id; ?><?php echo $params->get('moduleclass_sfx'); ?>">
<!-- IE (et. al) Object -->
<object  id="mod_morfeo_videoie<?php echo $module->id; ?>" class="movie<?php echo $module->id; ?>"
	classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"
	codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0">
	<param name="flashvars" value="<?php
	
	echo $texto;
	
	
	?>" />
	<param name="movie" value="<?php echo JURI::base(); echo 'modules/mod_morfeo_video/tmpl/'; ?>mod_morfeo_video.swf" />
	<param name="quality" value="high" />
<param name="scale" value="exactfit" />
	            <param name="wmode" value="transparent" />
	 <param name="allowFullScreen" value="true" />
		<!--[if !IE]>-->

		<!-- Firefox (et. al) Object -->
		<object class="movie<?php echo $module->id; ?>" id="mod_morfeo_videoff<?php echo $module->id; ?>"
			type="application/x-shockwave-flash"
			data="<?php echo JURI::base(); echo 'modules/mod_morfeo_video/tmpl/'; ?>mod_morfeo_video.swf">
			
			<param name="scale" value="exactfit" />
            
			<param name="movie" value="<?php echo JURI::base(); echo 'modules/mod_morfeo_video/tmpl/'; ?>mod_morfeo_video.swf" />

			<param name="flashvars" value="<?php
	
	echo $texto;
	
	
	?>" />
			<param name="quality" value="high" />
                        <param name="wmode" value="transparent" />
            <param name="allowFullScreen" value="true" />
			
			<param name="pluginurl" value="http://www.macromedia.com/go/getflashplayer"/>
			
				<!-- No plugin -->
				<p>This page require <a href="http://www.adobe.com/">Adobe Flash 9.0</a> (or higher) plug in.</p>

		</object>
		
		<!--<![endif]-->
</object>
</div>

<?php 
}

?>