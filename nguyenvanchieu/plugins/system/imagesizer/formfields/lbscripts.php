<?php
/**
 * @package	ImageSizer for Joomla! 3.x
 * @version	3.2.4
 * @author	reDim GmbH
 * @copyright	(C) 2009-2015 reDim GmbH All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('JPATH_PLATFORM') or die;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
JFormHelper::loadFieldClass('list');

/**
 * Supports an HTML select list of files
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldlbscripts extends JFormFieldList
{

	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = 'lbscripts';

	/**
	 * Method to get the list of files for the field options.
	 * Specify the target directory with a directory attribute
	 * Attributes allow an exclude mask and stripping of extensions from file name.
	 * Default attribute may optionally be set to null (no file) or -1 (use a default).
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   11.1
	 */
	protected function getOptions()
	{
		$options = array();

		// Initialize some field attributes.
		$filter = (string) $this->element['filter'];
		$exclude = (string) $this->element['exclude'];
		$stripExt = (string) $this->element['stripext'];
		$hideNone = (string) $this->element['hide_none'];
		$hideDefault = (string) $this->element['hide_default'];

		// Get the path in which to search for file options.
		$path = (string) $this->element['directory'];
		if (!is_dir($path))
		{
			$path = JPATH_ROOT . '/' . $path;
		}
		// Prepend some default options based on field attributes.
		if (!$hideNone)
		{
			$options[] = JHtml::_('select.option', '-1', JText::alt('JOPTION_DO_NOT_USE', preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)));
		}
	#	if (!$hideDefault)
	#	{
	#		$options[] = JHtml::_('select.option', '', JText::alt('JOPTION_USE_DEFAULT', preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)));
	#	}

		// Get a list of files in the search path with the given filter.
		$files = JFolder::files($path, $filter);
/*
		if($this->element['directory']=="plugins/system/imagesizer/lbscripts/"){
		 	if(!defined('REDIM_DOWNLOADKEY')){
				$files=array();

			#	$files[]="yoxview.php";
			#	$files[]="superbox.php";
			#	$files[]="magnificpopup.php";


			#	$files[]="slimbox.php";
				$files[]="lightbox2.php";
				$files[]="modal.php";			
				$files[]="onlythumb.php";
				$files[]="link.php";
                
			}
		}
*/
        
		// Build the options list from the list of files.
		if (is_array($files))
		{
		  
            $e="yoxview.php,superbox.php,chocolatmaster.php,shadowbox.php,prettyphoto.php,magnificpopup.php,responsivelightbox.php,nivolightbox.php,lightbox2.php";
            $e=explode(",",$e);
          
            sort($files);
          
			foreach ($files as $file)
			{

				// Check to see if the file is in the exclude mask.
				if ($exclude)
				{
					if (preg_match(chr(1) . $exclude . chr(1), $file))
					{
						continue;
					}
				}

				// If the extension is to be stripped, do it.
				if ($stripExt)
				{
					$file = JFile::stripExt($file);
				}
                $name=substr($file,0,-4);
                if(in_array($file,$e)){
                    $name.=" (responsve)";
                }
                
				$options[] = JHtml::_('select.option', $file, $name);
			}
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
