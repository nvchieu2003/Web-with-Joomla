<?php
/*
 * ------------------------------------------------------------------------
 * VietPublic Ty Gia & Gia Vang module for Joomla 2.5, 3.x
 * ------------------------------------------------------------------------
 * Copyright (C) 2013 - 2015 VietPublic. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: VietPublic
 * Websites: http://www.vietpublic.net
 * ------------------------------------------------------------------------
*/
/* BEGIN: Define DS */
JLoader::import( "joomla.version" );
$version = new JVersion();
if (!version_compare( $version->RELEASE, "2.5", "<=")):
   if (!defined("DS")):
      define("DS", DIRECTORY_SEPARATOR);
   endif;
endif;
/* END: Define DS */
defined('_JEXEC') or die('Restricted access');

$doc = JFactory::getDocument();

require_once("css".DS."vp_style.min.php");

$ShowNguon = $params->get('ShowNguon');
$ShowTyGia = $params->get('ShowTyGia');
$ShowTyGiaTitle = $params->get('ShowTyGiaTitle');
$ShowGiaVang = $params->get('ShowGiaVang');
$ShowGiaVangTitle = $params->get('ShowGiaVangTitle');
$ShowCopyright = $params->get('ShowCopyright');
$RateInterval = $params->get('RateInterval');

require_once("functions.php");

if ($ShowTyGia) {
	if ($ShowNguon) {
		$url="http://www.vietcombank.com.vn/exchangerates/ExrateXML.aspx";
		$file ="data.xml";
		$path = "modules".DS."mod_vp_tygia".DS."xml".DS;

		//Check xml file
		if (file_exists($path.$file)) {
			$xml=simplexml_load_file($path.$file);
			
			$date1 = $xml->DateTime; 
			$date2 = date("m/d/Y h:i:s A", time());		
			$interval = strtotime($date2)-strtotime($date1);
			// 43200 seconds ~ 12h
			if ($interval > $RateInterval) {
				SaveToXML ($url, $path, $file);
				$xml=simplexml_load_file($path.$file);
			}	

			$usd = number_format((float)trim($xml->Exrate[18]["Sell"]),2);	
			$gbp = number_format((float)trim($xml->Exrate[5]["Sell"]),2);		
			$hkd = number_format((float)trim($xml->Exrate[6]["Sell"]),2);		
			$chf = number_format((float)trim($xml->Exrate[2]["Sell"]),2);		
			$jpy = number_format((float)trim($xml->Exrate[8]["Sell"]),2);		
			$aud = number_format((float)trim($xml->Exrate[0]["Sell"]),2);		
			$cad = number_format((float)trim($xml->Exrate[1]["Sell"]),2);		
			$sgd = number_format((float)trim($xml->Exrate[16]["Sell"]),2);		
			$eur = number_format((float)trim($xml->Exrate[4]["Sell"]),2);		
			$thb = number_format((float)trim($xml->Exrate[17]["Sell"]),2);		
			$nok = number_format((float)trim($xml->Exrate[12]["Sell"]),2);	     
			
		} else {
			exit('Failed to open data.xml.');
		}
	}
	else {
		$url="http://www.eximbank.com.vn/WebsiteExrate/ExchangeRate_vn_2012.aspx";    
		$html=vp_get_tygia($url);	
		preg_match_all('/<span id="ExchangeRateRepeater_lblCSHSELLRT_0">.*?<\/span>/is',$html,$usd);
		$usd=strip_tags($usd[0][0],'<span id=>');	
		preg_match_all('/<span id="ExchangeRateRepeater_lblCSHSELLRT_3">.*?<\/span>/is',$html,$grb); 
		$gbp=strip_tags($grb[0][0],'<span id=>');	
		preg_match_all('/<span id="ExchangeRateRepeater_lblCSHSELLRT_4">.*?<\/span>/is',$html,$hkd);
		$hkd=strip_tags($hkd[0][0],'<span id=>');	
		preg_match_all('/<span id="ExchangeRateRepeater_lblCSHSELLRT_5">.*?<\/span>/is',$html,$chf);
		$chf=strip_tags($chf[0][0],'<span id=>');	
		preg_match_all('/<span id="ExchangeRateRepeater_lblCSHSELLRT_6">.*?<\/span>/is',$html,$jpy);
		$jpy=strip_tags($jpy[0][0],'<span id=>');	
		preg_match_all('/<span id="ExchangeRateRepeater_lblCSHSELLRT_7">.*?<\/span>/is',$html,$aud);
		$aud=strip_tags($aud[0][0],'<span id=>');	
		preg_match_all('/<span id="ExchangeRateRepeater_lblCSHSELLRT_8">.*?<\/span>/is',$html,$cad);
		$cad=strip_tags($cad[0][0],'<span id=>');	
		preg_match_all('/<span id="ExchangeRateRepeater_lblCSHSELLRT_9">.*?<\/span>/is',$html,$sgd);
		$sgd=strip_tags($sgd[0][0],'<span id=>');	
		preg_match_all('/<span id="ExchangeRateRepeater_lblCSHSELLRT_10">.*?<\/span>/is',$html,$eur);
		$eur=strip_tags($eur[0][0],'<span id=>');	
		preg_match_all('/<span id="ExchangeRateRepeater_lblCSHSELLRT_12">.*?<\/span>/is',$html,$thb);
		$thb=strip_tags($thb[0][0],'<span id=>');	
		preg_match_all('/<span id="ExchangeRateRepeater_lblCSHSELLRT_13">.*?<\/span>/is',$html,$nok);
		$nok=strip_tags($nok[0][0],'<span id=>');
	}
}	

if ($ShowGiaVang){
	$url="http://www.eximbank.com.vn/WebsiteExrate/Gold_vn_2012.aspx";
	$html=vp_get_giavang($url);	
	preg_match_all('/<span id="GoldRateRepeater_lblCSHSELLRT_0">.*?<\/span>/is',$html,$banle);
	$banle=number_format(intval(str_replace(',', '',strip_tags($banle[0][0],'<span id=>'))));	
}

require JModuleHelper::getLayoutPath('mod_vp_tygia',$params->get('layout','default'));

?>


