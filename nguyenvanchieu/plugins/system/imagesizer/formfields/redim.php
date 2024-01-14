<?php
/**
 * @package	ImageSizer for Joomla! 3.x
 * @version	3.2.4
 * @author	reDim GmbH
 * @copyright	(C) 2009-2015 reDim GmbH All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

# reDim - InfoBox V1.0
// Check to ensure this file is within the rest of the framework
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldreDim extends JFormField
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	public $type  = 'reDim';
	public $_version = '1.6';

	protected function ScanFolder($dir,$ext=0){
		$the_files=array();
	
		// check if directory exists
		if (is_dir($dir))
		{
			if ($handle = opendir($dir)) {
				while (false !== ($file = readdir($handle))) {
					if ($file != '.' && $file != '..' ) {
						$files[] = $dir .DS.$file;
					}
				}
			}
			closedir($handle);

			foreach ($files as $file)
			{
				if (is_dir($file)){
					if($ext==0){
				 	 	$file=str_replace(DS.DS,DS,$file);
						$ar=JFormFieldreDim::ScanFolder($file,0);
						if (is_array($ar)){
							$the_files=array_merge ($the_files,$ar);			 
						}												

					}
			 	}else{			 	 
			 	 	$file=str_replace(DS.DS,DS,$file);
					$the_files[] = $file;					
				}
			}
				
			
			unset($files);
		}		
			
			
		return $the_files;	
	}

	protected function getPluginFiles($dir="",$base=""){

		if($base==""){
			$base=$dir;
		}

		$files=JFormFieldreDim::ScanFolder($dir,$base);
		$html='<files>'."\n";
		foreach ($files as $file){
			$file=str_replace($base.DS,"",$file);
		 	$file="   <filename>".$file."</filename>";	 	 
			$html.= $file."\n";
		}
		$html.='</files>'."\n\n\n\n";
				
		$html='<textarea style="width:100%" rows="23" name="S1" cols="51">'.$html.'</textarea>';
		return $html;
	}


	protected function getInput()
	{
	#	$view =  $node->attributes('view');
		$view =  $this->element['view'];
        $html="";
		switch ($view){

		case 'pluginfiles':
			$html=JFormFieldreDim::getPluginFiles(JPATH_SITE.DS.$this->element['path']);
		break;
		
		case 'infomode':
		#	$img=JURI::root()."plugins/system/imagesizer/formfields/infomode.png";
		#	$html='<br style="clear: both" />'.JText::_("IMAGESIZER_INFO_MODE");
		#	$html.='<br /><img src="'.$img.'" />';
		#	$html.='<br style="clear: both" /><br />'.JText::_("IMAGESIZER_INFO_COMMAND");
			$html='<div>'.JText::_($this->element['description']).'</div>';
		break;

        case 'facebook':
        
        $plugin = JPluginHelper::getPlugin('system', 'imagesizer');
        $params = new JRegistry();
        $params->loadString($plugin->params);
        $onoff=$params->get('facebookonoff','1');
        
        if( $onoff==0 AND defined("REDIM_DOWNLOADKEY") ) {        
            $html="reDim-Facebook";       
        }else{
            $html='<div style="padding: 20px 20px 20px 0px">'.JText::_("IMAGESIZER_FACEBOOKTEXT").'<iframe src="//www.facebook.com/plugins/likebox.php?href=https%3A%2F%2Fwww.facebook.com%2Fredim.de&amp;width=450&amp;height=258&amp;colorscheme=light&amp;show_faces=true&amp;header=false&amp;stream=false&amp;show_border=false&amp;appId=345749765488564" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:450px; height:258px;" allowTransparency="true"></iframe>        </div>';
        }
        
        break;
        


		case 'updatecheck':
			jimport( 'joomla.plugin.helper' );
			$dispatcher = JDispatcher::getInstance();
			$checkupdate = $dispatcher->trigger('onCheckupdate');

			if(@$checkupdate[0]==1){
				$eid= JRequest::getINT("extension_id",0);
				jimport('joomla.updater.update');
				$updater = JUpdater::getInstance();
				$results = $updater->findUpdates($eid, 86400);
		
				$db =  JFactory::getDBO();
				$db->setQuery("SELECT update_id  FROM #__updates AS a WHERE a.extension_id='".$eid."' LIMIT 1");
				$eid=$db->LoadResult();
	
				$key=JPATH_SITE.DIRECTORY_SEPARATOR.$this->element['path']."/redim_key.php";
	
				if(file_Exists($key)){
					include_once($key);
					if(defined("REDIM_DOWNLOADKEY")){
						$key=REDIM_DOWNLOADKEY;
					}else{
						$key="";
					}
				}else{
					$key="";
				}
	
		
				if($eid>0){
					jimport('joomla.updater.update');
					$updaterow = JTable::getInstance('update');
					$updaterow->load($eid);
	
					$html=JText::_("REDIM_IS_UPDATE").": ".$updaterow->name." ".$updaterow->version;
							
					$update = new JUpdate;
					$update->loadFromXML($updaterow->detailsurl);
					if(isset($update->downloadurl->_data)){
						$link= $update->downloadurl->_data;
						if(!empty($key)){
							$link.="?did=".$key;
						}
						$html= '<a title="'.JText::_("Download").'" href="'.$link.'">'.$html.'</a>';
					}
					
				}else{
					$html=JText::_("REDIM_NO_UPDATE");
	
				}
			}

		break;


        case 'list':

		 	if(defined('REDIM_DOWNLOADKEY')){
		 	    
    		$html = array();
    		$attr = '';
    
    		// Initialize some field attributes.
    		$attr .= !empty($this->class) ? ' class="' . $this->class . '"' : '';
    		$attr .= !empty($this->size) ? ' size="' . $this->size . '"' : '';
    		$attr .= $this->multiple ? ' multiple' : '';
    		$attr .= $this->required ? ' required aria-required="true"' : '';
    		$attr .= $this->autofocus ? ' autofocus' : '';
    
    		// To avoid user's confusion, readonly="true" should imply disabled="true".
    		if ((string) $this->readonly == '1' || (string) $this->readonly == 'true' || (string) $this->disabled == '1'|| (string) $this->disabled == 'true')
    		{
    			$attr .= ' disabled="disabled"';
    		}
    
    		// Initialize JavaScript field attributes.
    		$attr .= $this->onchange ? ' onchange="' . $this->onchange . '"' : '';
    
    		// Get the field options.
    		$options = (array) $this->getOptions();
    
    		// Create a read-only list (no name) with a hidden input to store the value.
    		if ((string) $this->readonly == '1' || (string) $this->readonly == 'true')
    		{
    			$html[] = JHtml::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $this->value, $this->id);
    			$html[] = '<input type="hidden" name="' . $this->name . '" value="' . $this->value . '"/>';
    		}
    		else
    		// Create a regular list.
    		{
    			$html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
    		}
    
    		return implode($html);
            
            }
            
        break;


		case 'logo':
		$html='<a href="http://www.redim.de" target=_blank><img src="http://www.redim.de/redim_j_logo.gif" border="0" width="198" height="67"></a><br style="clear:both"/>';
		break;


		case 'help':
            $html= JText::_("HELP1");
		break;

		}

		return $html;

	}
    
	protected function getOptions()
	{
		$options = array();

		foreach ($this->element->children() as $option)
		{
			// Only add <option /> elements.
			if ($option->getName() != 'option')
			{
				continue;
			}

			// Filter requirements
			if ($requires = explode(',', (string) $option['requires']))
			{
				// Requires multilanguage
				if (in_array('multilanguage', $requires) && !JLanguageMultilang::isEnabled())
				{
					continue;
				}

				// Requires associations
				if (in_array('associations', $requires) && !JLanguageAssociations::isEnabled())
				{
					continue;
				}
			}

			$value = (string) $option['value'];

			$disabled = (string) $option['disabled'];
			$disabled = ($disabled == 'true' || $disabled == 'disabled' || $disabled == '1');

			$disabled = $disabled || ($this->readonly && $value != $this->value);

			// Create a new option object based on the <option /> element.
			$tmp = JHtml::_(
				'select.option', $value,
				JText::alt(trim((string) $option), preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)), 'value', 'text',
				$disabled
			);

			// Set some option attributes.
			$tmp->class = (string) $option['class'];

			// Set some JavaScript option attributes.
			$tmp->onclick = (string) $option['onclick'];

			// Add the option object to the result set.
			$options[] = $tmp;
		}

		reset($options);

		return $options;
	}
        
}