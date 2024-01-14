<?php
/**
 *  @package JuloaLib
 *  @copyright Copyright (c)2013 JoomProd / Akeeba
 *  @license GNU General Public License version 3, or later
 *  Inspired by FOF/AkeebaStrapper
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

class JuloaLib {
	static function loadCSS($type='bootstrap') {
        if($type == 'bootstrap')
            self::addCSS('media://juloalib/css/juloabootstrap.css');
        else
            self::addCSS('media://juloalib/css/juloabootstrap2.css');
	}
	
	static function loadJquery() {
		if (version_compare(JVERSION, '3.0', 'gt'))
		{
			//JHtml::_('jquery.framework');
			self::addJS('media://juloalib/js/jquery.min.js');
			self::addJS('media://juloalib/js/fullnoconflict.js');
		}
		else
		{
			self::addJS('media://juloalib/js/jquery.min.js');
			self::addJS('media://juloalib/js/fullnoconflict.js');
		}
	}
	
	static function loadJqueryUI() {
		self::addJS('media://juloalib/js/jquery-ui.min.js');
		self::addCSS('media://juloalib/css/ui-lightness/jquery-ui.css');
	}
	
	/**
	 * Add a CSS file to the page generated by the CMS
	 *
	 * @param   string  $path  A fancy path definition understood by parsePath
	 *
	 * @return  void
	 */
	public static function addCSS($path)
	{
		$document = JFactory::getDocument();
	
		if ($document instanceof JDocument)
		{
			if (method_exists($document, 'addStyleSheet'))
			{
				$url = self::parsePath($path);
				$document->addStyleSheet($url);
			}
		}
	}
	
	/**
	 * Add a JS script file to the page generated by the CMS.
	 *
	 * There are three combinations of defer and async (see http://www.w3schools.com/tags/att_script_defer.asp):
	 * * $defer false, $async true: The script is executed asynchronously with the rest of the page
	 *   (the script will be executed while the page continues the parsing)
	 * * $defer true, $async false: The script is executed when the page has finished parsing.
	 * * $defer false, $async false. (default) The script is loaded and executed immediately. When it finishes
	 *   loading the browser continues parsing the rest of the page.
	 *
	 * When you are using $defer = true there is no guarantee about the load order of the scripts. Whichever
	 * script loads first will be executed first. The order they appear on the page is completely irrelevant.
	 *
	 * @param   string   $path   A fancy path definition understood by parsePath
	 * @param   boolean  $defer  Adds the defer attribute, meaning that your script
	 *                           will only load after the page has finished parsing.
	 * @param   boolean  $async  Adds the async attribute, meaning that your script
	 *                           will be executed while the resto of the page
	 *                           continues parsing.
	 *
	 * @see FOFTemplateUtils::parsePath
	 *
	 * @return  void
	 */
	public static function addJS($path, $defer = false, $async = false)
	{
		$document = JFactory::getDocument();
	
		if ($document instanceof JDocument)
		{
			if (method_exists($document, 'addScript'))
			{
				$url = self::parsePath($path);
				$document->addScript($url, "text/javascript", $defer, $async);
			}
		}
	}
	
	/**
	 * Parse a fancy path definition into a path relative to the site's root,
	 * respecting template overrides, suitable for inclusion of media files.
	 * For example, media://com_foobar/css/test.css is parsed into
	 * media/com_foobar/css/test.css if no override is found, or
	 * templates/mytemplate/media/com_foobar/css/test.css if the current
	 * template is called mytemplate and there's a media override for it.
	 *
	 * The valid protocols are:
	 * media://		The media directory or a media override
	 * admin://		Path relative to administrator directory (no overrides)
	 * site://		Path relative to site's root (no overrides)
	 *
	 * @param   string   $path       Fancy path
	 * @param   boolean  $localFile  When true, it returns the local path, not the URL
	 *
	 * @return  string  Parsed path
	 */
	protected static function parsePath($path, $localFile = false)
	{
		if ($localFile)
		{
			$url = rtrim(JPATH_ROOT, DIRECTORY_SEPARATOR) . '/';
		}
		else
		{
			$url = JURI::root();
		}

		$altPaths = self::getAltPaths($path);
		$filePath = $altPaths['normal'];

		// If JDEBUG is enabled, prefer that path, else prefer an alternate path if present
		if (defined('JDEBUG') && JDEBUG && isset($altPaths['debug']))
		{
			if (file_exists(JPATH_SITE . '/' . $altPaths['debug']))
			{
				$filePath = $altPaths['debug'];
			}
		}
		elseif (isset($altPaths['alternate']))
		{
			if (file_exists(JPATH_SITE . '/' . $altPaths['alternate']))
			{
				$filePath = $altPaths['alternate'];
			}
		}

		$url .= $filePath;

		return $url;
	}
	
	/**
	 * Parse a fancy path definition into a path relative to the site's root.
	 * It returns both the normal and alternative (template media override) path.
	 * For example, media://com_foobar/css/test.css is parsed into
	 * array(
	 *   'normal' => 'media/com_foobar/css/test.css',
	 *   'alternate' => 'templates/mytemplate/media/com_foobar/css//test.css'
	 * );
	 *
	 * The valid protocols are:
	 * media://		The media directory or a media override
	 * admin://		Path relative to administrator directory (no alternate)
	 * site://		Path relative to site's root (no alternate)
	 *
	 * @param   string  $path  Fancy path
	 *
	 * @return  array  Array of normal and alternate parsed path
	 */
	protected static function getAltPaths($path)
	{
		$protoAndPath = explode('://', $path, 2);

		if (count($protoAndPath) < 2)
		{
			$protocol = 'media';
		}
		else
		{
			$protocol = $protoAndPath[0];
			$path = $protoAndPath[1];
		}

		$path = ltrim($path, '/' . DIRECTORY_SEPARATOR);

		switch ($protocol)
		{
			case 'media':
				// Do we have a media override in the template?
				$pathAndParams = explode('?', $path, 2);

				$ret = array(
					'normal'	 => 'media/' . $pathAndParams[0],
					'alternate'	 => self::getTemplateOverridePath('media:/' . $pathAndParams[0], false),
				);
				break;

			case 'admin':
				$ret = array(
					'normal' => 'administrator/' . $path
				);
				break;

			default:
			case 'site':
				$ret = array(
					'normal' => $path
				);
				break;
		}

		// For CSS and JS files, add a debug path if the supplied file is compressed
		JLoader::import('joomla.filesystem.file');
		$ext = JFile::getExt($ret['normal']);

		if (in_array($ext, array('css', 'js')))
		{
			$file = basename(JFile::stripExt($ret['normal']));

			/*
			 * Detect if we received a file in the format name.min.ext
			 * If so, strip the .min part out, otherwise append -uncompressed
			 */

			if (strlen($file) > 4 && strrpos($file, '.min', '-4'))
			{
				$position = strrpos($file, '.min', '-4');
				$filename = str_replace('.min', '.', $file, $position);
			}
			else
			{
				$filename = $file . '-uncompressed.' . $ext;
			}

			// Clone the $ret array so we can manipulate the 'normal' path a bit
			$temp = (array) (clone (object) $ret);
			$normalPath = explode('/', $temp['normal']);
			array_pop($normalPath);
			$normalPath[] = $filename;
			$ret['debug'] = implode('/', $normalPath);
		}

		return $ret;
	}
	
	/**
	 * Return the absolute path to the application's template overrides
	 * directory for a specific component. We will use it to look for template
	 * files instead of the regular component directorues. If the application
	 * does not have such a thing as template overrides return an empty string.
	 *
	 * @param   string   $component  The name of the component for which to fetch the overrides
	 * @param   boolean  $absolute   Should I return an absolute or relative path?
	 *
	 * @return  string  The path to the template overrides directory
	 */
	protected static function getTemplateOverridePath($component, $absolute = true)
	{
		list($isCli, $isAdmin) = self::isCliAdmin();
	
		if (!$isCli)
		{
			if ($absolute)
			{
				$path = JPATH_THEMES . '/';
			}
			else
			{
				$path = $isAdmin ? 'administrator/templates/' : 'templates/';
			}
	
			if (substr($component, 0, 7) == 'media:/')
			{
				$directory = 'media/' . substr($component, 7);
			}
			else
			{
				$directory = 'html/' . $component;
			}
	
			$path .= JFactory::getApplication()->getTemplate() .
			'/' . $directory;
		}
		else
		{
			$path = '';
		}
	
		return $path;
	}

	/**
	 * Main function to detect if we're running in a CLI environment and we're admin
	 *
	 * @return  array  isCLI and isAdmin. It's not an associtive array, so we can use list.
	 */
protected static function isCliAdmin()
	{
		static $isCLI   = null;
		static $isAdmin = null;
	
		if (is_null($isCLI) && is_null($isAdmin))
		{
			if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
				try
				{
					if (is_null(JFactory::$application))
					{
						$isCLI = true;
					}
					else
					{
						$isCLI = JFactory::getApplication() instanceof JException;
					}
				}
				catch (Exception $e)
				{
					$isCLI = true;
				}
				

				if ($isCLI)
				{
					$isAdmin = false;
				}
				else
				{
					$isAdmin = !JFactory::$application ? false : JFactory::getApplication()->isClient('administrator');
				}
				
			} else {
				$isCLI = false;
				$isAdmin = JFactory::getApplication()->isClient('administrator');
			}
	
		}
	
		return array($isCLI, $isAdmin);
	}
	
}