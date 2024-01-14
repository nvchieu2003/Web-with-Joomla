<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');

$token = JHtml::_('form.token');
?>
<div class="juloawrapper">
    <div class="login">
        <div class="page-header">
            <h1>
                <?php echo \JText::_('INVOICING_PAGE_LOGIN'); ?>
            </h1>
        </div>

        <form action="<?php echo \JRoute::_('index.php?option=com_users'); ?>" method="post" class="form-horizontal">

            <fieldset class="well">
                <div class="control-group">
                    <div class="control-label">
                        <?php echo \JText::_('INVOICING_USERNAME'); ?>
                    </div>
                    <div class="controls">
                        <input type="text" size="14" class="inputbox form-select" id="mod_login_username" name="username" />
                    </div>
                </div>

                <div class="control-group">
                    <div class="control-label">
                        <?php echo \JText::_('INVOICING_USER_PASSWORD'); ?>
                    </div>
                    <div class="controls">
                        <input type="password" size="14" class="inputbox form-select" id="mod_login_password" name="password" />
                    </div>
                </div>

                <div class="controls">
                    <button type="submit" class="btn btn-primary">
                        <?php echo \JText::_('JLOGIN'); ?>
                    </button>
                </div>

                <input type="hidden" name="task" value="user.login" />
                <input type="hidden" name="return" value="<?php echo $this->return_url; ?>" />
                <?php echo $token ?>
            </fieldset>
        </form>
    </div>

    <?php
    JHtml::_('behavior.formvalidation');
    ?>
    <div class="registration">
        <div class="page-header">
            <h1><?php echo \JText::_('INVOICING_PAGE_REGISTRATION'); ?></h1>
        </div>

        <form id="member-registration" action="<?php echo \JRoute::_('index.php?option=com_invoicing&view=user&task=registerUser'); ?>" method="post" class="form-validate form-horizontal" enctype="multipart/form-data">
            <fieldset class="well">
                <div class="control-group">
                    <div class="control-label">
                        <label id="jform_name-lbl" for="jform_name" class="hasTooltip required" title=""><?php echo \JText::_('INVOICING_COMMON_NAME')?>:<span class="star">&#160;*</span>
                        </label>										
                    </div>
                    <div class="controls">
                        <input type="text" name="jform[name]" id="jform_name" value="<?php echo @htmlspecialchars($this->data['name']) ?>" size="30" required aria-required="true" />	
                    </div>
                </div>
                                                <div class="control-group">
                    <div class="control-label">
                    <label id="jform_username-lbl" for="jform_username" class="hasTooltip required" title=""><?php echo \JText::_('INVOICING_USERNAME')?>:<span class="star">&#160;*</span></label>										</div>
                    <div class="controls">
                        <input type="text" name="jform[username]" id="jform_username" value="<?php echo @htmlspecialchars($this->data['username']) ?>" class="validate-username" size="30" required aria-required="true" />					</div>
                </div>
                                                <div class="control-group">
                    <div class="control-label">
                    <label id="jform_password1-lbl" for="jform_password1" class="hasTooltip required" title=""><?php echo \JText::_('INVOICING_USER_PASSWORD')?>:<span class="star">&#160;*</span></label>										</div>
                    <div class="controls">
                        <input type="password" name="jform[password1]" id="jform_password1" value="<?php echo @htmlspecialchars($this->data['password1']) ?>" autocomplete="off" class="validate-password" size="30" maxlength="99" required aria-required="true" />					</div>
                </div>
                                                <div class="control-group">
                    <div class="control-label">
                    <label id="jform_password2-lbl" for="jform_password2" class="hasTooltip required" title=""><?php echo \JText::_('INVOICING_USER_CONFIRM_PASSWORD')?>:<span class="star">&#160;*</span></label>										</div>
                    <div class="controls">
                        <input type="password" name="jform[password2]" id="jform_password2" value="<?php echo @htmlspecialchars($this->data['password2']) ?>" autocomplete="off" class="validate-password" size="30" maxlength="99" required aria-required="true" />					</div>
                </div>
                                                <div class="control-group">
                    <div class="control-label">
                    <label id="jform_email1-lbl" for="jform_email1" class="hasTooltip required" title=""><?php echo \JText::_('INVOICING_USER_EMAIL')?>:<span class="star">&#160;*</span></label>										</div>
                    <div class="controls">
                        <input type="email" name="jform[email1]" class="validate-email" id="jform_email1" value="<?php echo @htmlspecialchars($this->data['email1']) ?>" size="30" required aria-required="true" />					</div>
                </div>
                                                <div class="control-group">
                    <div class="control-label">
                    <label id="jform_email2-lbl" for="jform_email2" class="hasTooltip required" title=""><?php echo \JText::_('INVOICING_USER_CONFIRM_EMAIL')?>:<span class="star">&#160;*</span></label>										</div>
                    <div class="controls">
                        <input type="email" name="jform[email2]" class="validate-email" id="jform_email2" value="<?php echo @htmlspecialchars($this->data['email2']) ?>" size="30" required aria-required="true" />					</div>
                </div>
                
                <div class="control-group">
                    <div class="control-label">
                        <label id="jform_address1-lbl" for="jform_address1" class="hasTooltip required" title=""><?php echo \JText::_('INVOICING_COMMON_ADDRESS1')?>:<span class="star">&#160;*</span>
                        </label>										
                    </div>
                    <div class="controls">
                        <input type="text" name="jform[address1]" id="jform_address1" value="<?php echo @htmlspecialchars($this->data['address1']) ?>" size="30" required aria-required="true" />	
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <label id="jform_address2-lbl" for="jform_address2" class="hasTooltip" title=""><?php echo \JText::_('INVOICING_COMMON_ADDRESS2'); ?>:<span class="star">&#160;</span>
                        </label>										
                    </div>
                    <div class="controls">
                        <input type="text" name="jform[address2]" id="jform_address2" value="<?php echo @htmlspecialchars($this->data['address2']) ?>" size="30" />	
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <label id="jform_zip-lbl" for="jform_zip" class="hasTooltip required" title=""><?php echo \JText::_('INVOICING_COMMON_ZIP'); ?>:<span class="star">&#160;*</span>
                        </label>										
                    </div>
                    <div class="controls">
                        <input type="text" name="jform[zip]" id="jform_zip" value="<?php echo @htmlspecialchars($this->data['zip']) ?>" size="30" required aria-required="true" />	
                    </div>
                </div>
                
                <div class="control-group">
                    <div class="control-label">
                        <label id="jform_zip-lbl" for="jform_city" class="hasTooltip required" title=""><?php echo \JText::_('INVOICING_COMMON_CITY'); ?>:<span class="star">&#160;*</span>
                        </label>										
                    </div>
                    <div class="controls">
                        <input type="text" name="jform[city]" id="jform_city" value="<?php echo @htmlspecialchars($this->data['city']) ?>" size="30" required aria-required="true" />	
                    </div>
                </div>
                
                <div class="control-group">
                    <div class="control-label">
                        <label id="jform_country-lbl" for="jform_country" class="hasTooltip required" title=""><?php echo \JText::_('INVOICING_COMMON_COUNTRY'); ?>:<span class="star">&#160;*</span>
                        </label>										
                    </div>
                    <div class="controls">
                    	<?php echo InvoicingHelperSelect::countries(@$this->data['country'],'jform[country]',array('id'=>'jform_country','required'=>'true')) ?>
                    </div>
                </div>
              
                
                <div class="controls">
                    <button type="submit" class="btn btn-primary validate"><?php echo \JText::_('JREGISTER');?></button>
                    <?php echo $token ?>
                    <input type="hidden" name="Itemid" value="<?php echo $this->itemid?>" />
                    <input type="hidden" name="return" value="<?php echo $this->return_url; ?>" />
                </div>
            </fieldset>
        </form>
    </div>
</div>