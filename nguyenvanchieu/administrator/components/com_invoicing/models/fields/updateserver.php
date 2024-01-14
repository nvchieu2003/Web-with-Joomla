<?php
/**
 * @package     Bruce
 * @subpackage  com_bruce
 *
 * @copyright   Copyright (C) 2014 JULOA, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

/**
 * Update Server Extension XML .
 *
 * @package     Bruce
 * @subpackage  com_bruce
 */
class JFormFieldUpdateServer extends JFormField
{
        /**
         * The form field type.
         *
         * @var         string
         * @since   1.6
         */
        protected $type = 'UpdateServer';

        /**
         * Method to get the field input markup.
         *
         * @return  string      The field input markup.
         *
         * @since   1.6
         */
        protected function getInput()
        {
        	JHTML::_('jquery.framework');
        	
                ob_start();
                $script = '
                jQuery("#apply_'.$this->id.'").click(function() {
        	        dlid = jQuery("#'.$this->id.'").val();
            	        url = "index.php?option=com_invoicing&view=ajax&task=updateserverxml&dlid="+dlid;
        		jQuery.get(url,
                                        {},
                                        function() {
                                                alert('.json_encode(\JText::_('COM_INVOICING_SERVER_XML_UPDATED')).');
                                        });
                });';
                
                \JFactory::getApplication()->getDocument()->addScriptDeclaration($script);
                
        	$js = ob_get_clean();
            return
                        '<input class="input-xlarge" type="text" name="' . $this->name . '" id="' . $this->id . '" value="'
                        . htmlspecialchars($this->value) . '" /> <a class="btn" id="apply_' . $this->id . '"><i class="icon-refresh"></i> '
                        . \JText::_('COM_INVOICING_UPDATE_SERVER_XML') . '</a>'.$js;
        }
}
