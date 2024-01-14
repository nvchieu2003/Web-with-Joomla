<?php
defined('JPATH_PLATFORM') or die;

class JFormFieldredimlizenz extends JFormField
{

	protected $type = 'redimlizenz';


	protected function getLabel()
	{

		return '';
	
	}


	protected function getInput()
	{
		$licensekey = (string) $this->element['licensekey'];

		$domain=@$_SERVER["HTTP_HOST"];
				
		$img=$licensekey.':'.$domain;	
		
		$img=base64_encode($img);
		$img=strrev($img);
		$img=str_replace('=','_',$img);		

		$img='https://www.redim.de/license/'.$img.'.png';
		
		return '<img src="'.$img.'" />';
	}
}
