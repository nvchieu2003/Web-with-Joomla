<?php
/*
 * ------------------------------------------------------------------------
 * VietPublic Ty Gia & Gia Vang module for Joomla 2.5, 3.x
 * ------------------------------------------------------------------------
 * Copyright (C) 2013 - 2013 VietPublic. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: VietPublic
 * Websites: http://www.vietpublic.net
 * ------------------------------------------------------------------------
*/
defined('_JEXEC') or die('Restricted access');
// GET EXCHANGE RATE FROM EXIMBANK FUN
function vp_eximbank_rate($url) {	  
	return 1;
}
//GET EXCHANGE RATE FUN
function vp_get_tygia($url) {
	$ch = curl_init();
	$timeout = 15;
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}
// GET GOLD RATE FUN
function vp_get_giavang($url) {
	$ch = curl_init();
	$timeout = 15;
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$data2 = curl_exec($ch);
	curl_close($ch);
	return $data2;
}
// SAVE REMOTE XML TO LOCAL XML FUN
function SaveToXML ($url, $path, $fName = null)
{
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_HEADER, 0);
	curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; rv:2.0.1) Gecko/20100101 Firefox/4.0.1');
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	//curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1 );
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0);

	if(false == file_exists($path)) {
		mkdir($path, 0777, true);
	}

	if ($fName    !==  null)
	{
		$parts           =  pathinfo($url);
		$fileName   =  $path. '/'.  $fName;
	}
	else
	{
		$fileName   =  $path. '/'. basename($url);
	}

	$fp               =  fopen($fileName, 'w+');

	curl_setopt($curl, CURLOPT_FILE, $fp);
	curl_exec_follow($curl); 
	curl_close($curl);

	fclose($fp); unset($fp);

	return $fileName;
}

function curl_exec_follow(/*resource*/ $ch, /*int*/ &$maxredirect = null) { 
		$mr = $maxredirect === null ? 5 : intval($maxredirect); 
		if (ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')) { 
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $mr > 0); 
			curl_setopt($ch, CURLOPT_MAXREDIRS, $mr); 
		} else { 
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); 
			if ($mr > 0) { 
				$newurl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL); 

				$rch = curl_copy_handle($ch); 
				curl_setopt($rch, CURLOPT_HEADER, true); 
				curl_setopt($rch, CURLOPT_NOBODY, true); 
				curl_setopt($rch, CURLOPT_FORBID_REUSE, false); 
				curl_setopt($rch, CURLOPT_RETURNTRANSFER, true); 
				do { 
					curl_setopt($rch, CURLOPT_URL, $newurl); 
					$header = curl_exec($rch); 
					if (curl_errno($rch)) { 
						$code = 0; 
					} else { 
						$code = curl_getinfo($rch, CURLINFO_HTTP_CODE); 
						if ($code == 301 || $code == 302) { 
							preg_match('/Location:(.*?)\n/', $header, $matches); 
							$newurl = trim(array_pop($matches)); 
						} else { 
							$code = 0; 
						} 
					} 
				} while ($code && --$mr); 
				curl_close($rch); 
				if (!$mr) { 
					if ($maxredirect === null) { 
						trigger_error('Too many redirects. When following redirects, libcurl hit the maximum amount.', E_USER_WARNING); 
					} else { 
						$maxredirect = 0; 
					} 
					return false; 
				} 
				curl_setopt($ch, CURLOPT_URL, $newurl); 
			} 
		} 
		return curl_exec($ch); 
	} 

?>