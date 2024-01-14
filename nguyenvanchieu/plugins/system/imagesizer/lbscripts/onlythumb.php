<?PHP
/**
 * @package	ImageSizer for Joomla! 3.x
 * @version	3.2.4
 * @author	reDim GmbH
 * @copyright	(C) 2009-2015 reDim GmbH All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined( '_JEXEC' ) or die( 'Restricted access' );


function imagesizer_addon_onlythumb(&$ar,&$imagesizer){

	$output=plgSystemimagesizer::make_img_output($ar);
	$output='<img '.$output.' />';			

	return $output;

}
