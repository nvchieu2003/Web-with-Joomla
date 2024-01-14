<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/select.php');

JHtml::_('behavior.keepalive');

$token = JHtml::_('form.token'); 
?>
<div class="juloawrapper">
    <?php
    //JHtml::_('behavior.formvalidation');
    ?>
    <div class="registration">
        <div class="page-header">
            <h1><?php echo \JText::_("INVOICING_PAGE_USER_PROFILE"); ?></h1>
        </div>

        <form id="member-registration" action="<?php echo \JRoute::_('index.php?option=com_invoicing&view=user&task=saveUser'); ?>" method="post" class="form-validate form-horizontal" enctype="multipart/form-data">
            <fieldset class="well">
                <div class="control-group">
                    <div class="control-label">
                        <label id="jform_name-lbl" for="jform_name" class="hasTooltip required" title=""><?php echo \JText::_('INVOICING_COMMON_NAME')?>:<span class="star">&#160;*</span>
                        </label>										
                    </div>
                    <div class="controls">
                        <input class="form-control" type="text" name="jform[name]" id="jform_name" value="<?php echo @htmlspecialchars($this->data['businessname']) ?>" size="30" required aria-required="true" />	
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <label id="jform_address1-lbl" for="jform_address1" class="hasTooltip required" title=""><?php echo \JText::_('INVOICING_COMMON_ADDRESS1')?>:<span class="star">&#160;*</span>
                        </label>										
                    </div>
                    <div class="controls">
                        <input class="form-control" type="text" name="jform[address1]" id="jform_address1" value="<?php echo @htmlspecialchars($this->data['address1']) ?>" size="30" required aria-required="true" />	
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <label id="jform_address2-lbl" for="jform_address2" class="hasTooltip" title=""><?php echo \JText::_('INVOICING_COMMON_ADDRESS2'); ?>:<span class="star">&#160;</span>
                        </label>										
                    </div>
                    <div class="controls">
                        <input class="form-control" type="text" name="jform[address2]" id="jform_address2" value="<?php echo @htmlspecialchars($this->data['address2']) ?>" size="30" />	
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <label id="jform_zip-lbl" for="jform_zip" class="hasTooltip required" title=""><?php echo \JText::_('INVOICING_COMMON_ZIP'); ?>:<span class="star">&#160;*</span>
                        </label>										
                    </div>
                    <div class="controls">
                        <input class="form-control" type="text" name="jform[zip]" id="jform_zip" value="<?php echo @htmlspecialchars($this->data['zip']) ?>" size="30" required aria-required="true" />	
                    </div>
                </div>
                
                <div class="control-group">
                    <div class="control-label">
                        <label id="jform_zip-lbl" for="jform_city" class="hasTooltip required" title=""><?php echo \JText::_('INVOICING_COMMON_CITY'); ?>:<span class="star">&#160;*</span>
                        </label>										
                    </div>
                    <div class="controls">
                        <input class="form-control" type="text" name="jform[city]" id="jform_city" value="<?php echo @htmlspecialchars($this->data['city']) ?>" size="30" required aria-required="true" />	
                    </div>
                </div>
                
                <div class="control-group">
                    <div class="control-label">
                        <label id="jform_country-lbl" for="jform_country" class="hasTooltip required" title=""><?php echo \JText::_('INVOICING_COMMON_COUNTRY'); ?>:<span class="star">&#160;*</span>
                        </label>										
                    </div>
                    <div class="controls">
                    	<?php echo InvoicingHelperSelect::countries(@$this->data['country'],'jform[country]',array('class'=>'form-select')) ?>
                    </div>
                </div>
              
                
                <div class="controls text-center">
                    <button type="submit" class="btn btn-primary validate"><?php echo \JText::_('JSAVE');?></button>
                    <?php echo $token ?>
                    <input type="hidden" name="Itemid" value="<?php echo $this->itemid?>" />
                    <input type="hidden" name="return" value="<?php echo $this->return_url; ?>" />
                </div>
            </fieldset>
        </form>
    </div>
</div>