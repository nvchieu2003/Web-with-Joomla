<?php
/**
 * @package		Invoicing
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license GNU General Public License version 3, or later
 */

// Check to ensure this file is within the rest of the framework
defined('_JEXEC') or die();

include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/pdf.php');
 
 
/**
 * DocumentPDF class, provides an easy interface to parse and display a pdf document
 *
 * @package             Joomla.Framework
 * @subpackage  Document
 * @since               1.5
 */
class TViewPdf extends \JViewLegacy
{
        protected $engine = null;
 
        protected $name = 'joomla';
 
        /**
         * Class constructore
         * @param       array   $options Associative array of options
         */
 
        function __construct($config = array())
        {
                parent::__construct($config);
 
                //set mime type
                $this->_mime = 'application/pdf';
 
                //set document type
                $this->_type = 'pdf';
        }
 
      
 
        /**
         * Sets the document name
         * @param   string   $name      Document name
         * @return  void
         */
 
        public function setName($name = 'joomla')
        {
                $this->name = $name;
        }
 
        /**
         * Returns the document name
         * @return      string
         */
 
        public function getName()
        {
                return $this->name;
        }
 
        function  display($tpl = null) {
        
	        // Get the task set in the model
			$app = JFactory::getApplication();
			$task = $app->input->getCmd('task','read');

            $method_name = 'onBefore'.ucfirst($task);
            if(method_exists($this, $method_name)) {
                $this->$method_name($tpl);
            }

            // Call the relevant method
            /*$method_name = 'on'.ucfirst($task);
            if(method_exists($this, $method_name)) {
                $this->$method_name($tpl);
            } else {
                $this->onDisplay();
            }*/
                
            $document = \JFactory::getDocument();
            $isAdmin = \JFactory::getApplication()->isClient('administrator');

			$input = \JFactory::getApplication()->input;
			$option = $input->getCmd('option', '');
			$view = $input->getCmd('view', '');
        	
        	$basePath = $isAdmin ? 'admin:' : 'site:';
        	$basePath .= $option.'/';
        	$basePath .= $view.'/';
        	$path = $basePath.$this->getLayout();
        	
        	if($tpl){
        		$path .= '_'.$tpl;
        	}
        	//$data = $this->loadAnyTemplate($path);
			$data = $this->content;
        
        	if($data instanceof JException) {
        		echo $data->getMessage();
        		return false;
        	} else {
                InvoicingHelperPDF::createPDF($data,$this->getName(),true);
                return false;
        	}
        }

        /**
	 * Loads a template given any path. The path is in the format:
	 * [admin|site]:com_foobar/viewname/templatename
	 * e.g. admin:com_foobar/myview/default
	 *
	 * This function searches for Joomla! version override templates. For example,
	 * if you have run this under Joomla! 3.0 and you try to load
	 * admin:com_foobar/myview/default it will automatically search for the
	 * template files default.j30.php, default.j3.php and default.php, in this
	 * order.
	 *
	 * @param string $path
	 * @param array $forceParams A hash array of variables to be extracted in the local scope of the template file
	 */
	public function loadAnyTemplate($path = '', $forceParams = array())
	{
		// Automatically check for a Joomla! version specific override
		$throwErrorIfNotFound = true;

		$jversion = new JVersion();
		$versionParts = explode('.', $jversion->getLongVersion());
		$majorVersion = array_shift($versionParts);
		$suffixes = array(
			'.j'.str_replace('.', '', $jversion->getHelpVersion()),
			'.j'.$majorVersion,
		);
		unset($jversion, $versionParts, $majorVersion);

		foreach($suffixes as $suffix) {
			if(substr($path, -strlen($suffix)) == $suffix) {
				$throwErrorIfNotFound = false;
				break;
			}
		}

		if($throwErrorIfNotFound) {
			foreach($suffixes as $suffix) {
				$result = $this->loadAnyTemplate($path.$suffix, $forceParams);
				if($result !== false) {
					return $result;
				}
			}
		}

		$template = JFactory::getApplication()->getTemplate();
		if(version_compare(JVERSION, '1.6.0', 'ge')) {
			$layoutTemplate = $this->getLayoutTemplate();
		}

		// Parse the path
		$templateParts = $this->_parseTemplatePath($path);

		// Get the default paths
		$paths = array();
		$paths[] = ($templateParts['admin'] ? JPATH_ADMINISTRATOR : JPATH_SITE).'/templates/'.
			$template.'/html/'.$templateParts['component'].'/'.$templateParts['view'];
		$paths[] = ($templateParts['admin'] ? JPATH_ADMINISTRATOR : JPATH_SITE).'/components/'.
			$templateParts['component'].'/views/'.$templateParts['view'].'/tmpl';
		if(isset($this->_path) || property_exists($this, '_path')) {
			$paths = array_merge($paths, $this->_path['template']);
		} elseif(isset($this->path) || property_exists($this, 'path')) {
			$paths = array_merge($paths, $this->path['template']);
		}

		// Look for a template override
		if (isset($layoutTemplate) && $layoutTemplate != '_' && $layoutTemplate != $template)
		{
			$apath = array_shift($paths);
			array_unshift($paths, str_replace($template, $layoutTemplate, $apath));
		}

		$filetofind = $templateParts['template'].'.php';
		jimport('joomla.filesystem.path');
		$this->_tempFilePath = JPath::find($paths, $filetofind);
		if($this->_tempFilePath) {
			// Unset from local scope
			unset($template); unset($layoutTemplate); unset($paths); unset($path);
			unset($filetofind);

			// Never allow a 'this' property
			if (isset($this->this)) {
				unset($this->this);
			}

			// Force parameters into scope
			if(!empty($forceParams)) {
				extract($forceParams);
			}

			// Start capturing output into a buffer
			ob_start();
			// Include the requested template filename in the local scope
			// (this will execute the view logic).
			include $this->_tempFilePath;

			// Done with the requested template; get the buffer and
			// clear it.
			$this->_output = ob_get_contents();
			ob_end_clean();

			return $this->_output;
		} else {
			if($throwErrorIfNotFound) {
				return new Exception(JText::sprintf('JLIB_APPLICATION_ERROR_LAYOUTFILE_NOT_FOUND', $path), 500);
			}
			return false;
		}
	}

    private function _parseTemplatePath($path = '')
	{
		$input = \JFactory::getApplication()->input;
		$option = $input->getCmd('option', '');
		$view = $input->getCmd('view', '');

		$parts = array(
			'admin'		=> 0,
			'component'	=> $option,
			'view'		=> $view,
			'template'	=> 'default'
		);

		if(substr($path,0,6) == 'admin:') {
			$parts['admin'] = 1;
			$path = substr($path,6);
		} elseif(substr($path,0,5) == 'site:') {
			$path = substr($path,5);
		}

		if(empty($path)) return;

		$pathparts = explode('/', $path, 3);
		switch(count($pathparts)) {
			case 3:
				$parts['component'] = array_shift($pathparts);

			case 2:
				$parts['view'] = array_shift($pathparts);

			case 1:
				$parts['template'] = array_shift($pathparts);
				break;
		}

		return $parts;
	}
}