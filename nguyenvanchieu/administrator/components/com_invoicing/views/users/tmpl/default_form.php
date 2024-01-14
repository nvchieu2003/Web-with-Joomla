<?php
/**
 * @package		Invoicing
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

//JHtml::_('behavior.tooltip');
if(version_compare(JVERSION, '3.0', 'ge')) {
	JHTML::_('jquery.framework');
} else {
	JHTML::_('behavior.mootools');
}
////JHtml::_('behavior.modal');

$document = \JFactory::getDocument();
$document->addScript(JURI::root().'media/com_invoicing/js/jquery.validate.js');

include_once (JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/select.php');

//$this->loadHelper('Select');
?>
<style>
<?php if (@(int)$this->item->user_id == "") {?>
#account_form {
	display:none;
}
<?php } ?>
#assign_form {
	display:none;
}
</style>
<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm form form formValidation">
	<input type="hidden" name="option" value="com_invoicing" />
	<input type="hidden" name="view" value="users" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="invoicing_user_id" value="<?php echo $this->item->invoicing_user_id ?>" />
	<input type="hidden" name="returnurl" value="<?php echo $this->returnurl ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
	<div class="mb-3 row">
		<?php if (@$this->item->user_id == 0) {?>
			<div class="col">
				<div class="form-check">
					<input class="form-check-input" type="checkbox" id="create_user" name="create_user">
					<label for="create_user" class="form-check-label"><?php echo \JText::_('INVOICING_CREATE_JOOMLA_ACCOUNT'); ?></label>
				</div>
			</div>
		<?php } ?>
		<?php if (@$this->item->user_id == 0) {?>
			<div class="col">
				<div class="form-check">
					<input class="form-check-input" type="checkbox" id="assign_user" name="assign_user">
					<label for="assign_user" class="form-check-label"><?php echo \JText::_('INVOICING_ASSIGN_JOOMLA_ACCOUNT'); ?></label>
				</div>
			</div>
		<?php } ?>
	</div>

	<div class="adminlist" id="account_form">
			<div class="mb-3 row">
				<div class="col col-3"><?php echo \JText::_('INVOICING_USER_USERNAME'); ?></div>
				<div class="col">
					<?php if (@$this->item->user_id != 0) {?>
					<input type="hidden" name="user_id" value="<?php echo $this->escape($this->item->user_id); ?>" />
					<?php echo $this->escape($this->item->username); ?>
					<?php } else {?>
					<input class="form-control" type="text" size="50" maxsize="255" id="username" name="username" value="" />
					<?php }?>		
				</div>
			</div>
			<div class="mb-3 row">
				<div class="col col-3"><?php echo \JText::_('INVOICING_USER_PASSWORD'); ?></div>
					<div class="col">
						<input class="form-control" type="password" size="50" maxsize="255" id="password" name="password" value="" />
						<p class="aside-desc"><?php echo \JText::_('INVOICING_USER_PASSWORD_HELP')?></p>
					</div> 

			</div>
			<div class="mb-3 row">
				<div class="col col-3"><?php echo \JText::_('INVOICING_USER_EMAIL'); ?></div>
				<div class="col"><input class="form-control" type="email" size="50" maxsize="255" name="email" id="email" value="<?php echo $this->escape(@$this->item->email); ?>" /></div>
			</div>
	</div>

	<?php if (@$this->item->user_id == 0) {?>
		<div class="adminlist" id="assign_form">
			<div class="mb-3 row">
				<div class="col col-3"><?php echo \JText::_('INVOICING_USER_USERNAME'); ?></div>
				<div class="col"><?php echo InvoicingHelperSelect::joomlausers('user_id',null,array('class'=>'form-select'));?></div>
			</div>
		</div>
	<?php } ?>

	<div class="adminlist">
			<?php /*<div class="mb-3 row">
				<div class="col col-3"><?php echo \JText::_('INVOICING_USER_ISBUSINESS'); ?></div>
				<div class="col"><?php echo JHTML::_('select.booleanlist', 'isbusiness', null, $this->item->isbusiness); ?></div>
			</div>
			*/?>
			<div class="mb-3 row">
				<div class="col col-3"><?php echo \JText::_('INVOICING_USER_BUSINESSNAME'); ?></div>
				<div class="col"><input class="form-control" type="text" size="50" maxsize="255" name="businessname" value="<?php echo $this->escape($this->item->businessname); ?>" /></div>
			</div>
			<div class="mb-3 row">
				<div class="col col-3"><?php echo \JText::_('INVOICING_USER_FIRSTNAME'); ?></div>
				<div class="col"><input class="form-control" type="text" size="50" maxsize="255" name="firstname" value="<?php echo $this->escape($this->item->firstname); ?>" /></div>
			
			</div>
			<div class="mb-3 row">
				<div class="col col-3"><?php echo \JText::_('INVOICING_USER_LASTNAME'); ?></div>
				<div class="col"><input class="form-control" type="text" size="50" maxsize="255" name="lastname" value="<?php echo $this->escape($this->item->lastname); ?>" /></div>
			
			</div>
			<?php /*<div class="mb-3 row">
				<div class="col col-3"><?php echo \JText::_('INVOICING_USER_OCCUPATION'); ?></div>
				<div class="col"><input class="form-control" type="text" size="50" maxsize="255" name="occupation" value="<?php echo $this->escape($this->item->occupation); ?>" /></div> 
				
			</div>
			<!-- <div class="mb-3 row">
				<div class="col col-3"><?php echo \JText::_('INVOICING_USER_VATNUMBER'); ?></div>
				<div class="col"><input class="form-control" type="text" size="50" maxsize="255" name="vatnumber" value="<?php echo $this->escape($this->item->vatnumber); ?>" /></div>
				
			</div>
			<div class="mb-3 row"> 
				<div class="col col-3"><?php echo \JText::_('INVOICING_USER_VIESREGISTERED'); ?></div>
				<div class="col"><?php echo JHTML::_('select.booleanlist', 'viesregistered', null, $this->item->viesregistered); ?></div>
				
			</div>
			<div class="mb-3 row">
				<div class="col col-3"><?php echo \JText::_('INVOICING_USER_TAXAUTHORITY'); ?></div>
				<div class="col"><input class="form-control" type="text" size="50" maxsize="255" name="taxauthority" value="<?php echo $this->escape($this->item->taxauthority); ?>" /></div>
				
			</div>-->
			*/?>
			<div class="mb-3 row">
				<div class="col col-3"><?php echo \JText::_('INVOICING_COMMON_ADDRESS1'); ?></div>
				<div class="col"><input class="form-control" type="text" size="50" maxsize="255" name="address1" value="<?php echo $this->escape($this->item->address1); ?>" /></div>
				
			</div>
			<div class="mb-3 row">
				<div class="col col-3"><?php echo \JText::_('INVOICING_COMMON_ADDRESS2'); ?></div>
				<div class="col"><input class="form-control" type="text" size="50" maxsize="255" name="address2" value="<?php echo $this->escape($this->item->address2); ?>" /></div>
				
			</div>
			<div class="mb-3 row">
				<div class="col col-3"><?php echo \JText::_('INVOICING_COMMON_CITY'); ?></div>
				<div class="col"><input class="form-control" type="text" size="50" maxsize="255" name="city" value="<?php echo $this->escape($this->item->city); ?>" /></div>
				
			</div>
			<?php /*<div class="mb-3 row">
				<div class="col col-3"><?php echo \JText::_('INVOICING_COMMON_STATE'); ?></div>
				<div class="col"><input class="form-control" type="text" size="50" maxsize="255" name="state" value="<?php echo $this->escape($this->item->state); ?>" /></div>
				
			</div>
			*/?>
			<div class="mb-3 row">
				<div class="col col-3"><?php echo \JText::_('INVOICING_COMMON_ZIP'); ?></div>
				<div class="col"><input class="form-control" type="text" size="50" maxsize="255" name="zip" value="<?php echo $this->escape($this->item->zip); ?>" /></div>
				
			</div>
			<div class="mb-3 row">
				<div class="col col-3"><?php echo \JText::_('INVOICING_COMMON_COUNTRY'); ?></div>
				<div class="col"><?php echo InvoicingHelperSelect::countries($this->item->country,'country',array('class'=>'form-select')) ?></div>
				
			</div>
			<div class="mb-3 row">
				<div class="col col-3"><?php echo \JText::_('INVOICING_USER_LANDLINE'); ?></div>
				<div class="col"><input class="form-control" type="text" size="50" maxsize="255" name="landline" value="<?php echo $this->escape($this->item->landline); ?>" /></div>
				
			</div>
			<div class="mb-3 row">
				<div class="col col-3"><?php echo \JText::_('INVOICING_USER_MOBILE'); ?></div>
				<div class="col"><input class="form-control" type="text" size="50" maxsize="255" name="mobile" value="<?php echo $this->escape($this->item->mobile); ?>" /></div>
				
			</div>
			<div class="mb-3 row">
				<div class="col col-3"><?php echo \JText::_('INVOICING_COMMON_NOTES'); ?></div>
				<div class="col"><textarea class="form-control" rows="10" cols="50" name="notes"><?php echo $this->escape($this->item->notes) ?> </textarea></div>
								
			</div>
		</div>


</form>
<?php
	$script = "
	jQ(document).ready(function() {
		Joomla.submitbutton = function(pressbutton) {
			if (pressbutton == 'cancel') {
				Joomla.submitform(pressbutton);	
					return;
			}
				if(jQ('#adminForm').valid()){
					Joomla.submitform(pressbutton);	
				}else{
						return false;
				}
		}
		
		emailrules ={required : true};
		passwordrules = {required : true};
		usernamerules = {required : true};
		
		jQ('#create_user').click( function(){
			if( jQ(this).is(':checked') ) {
			jQ('#assign_form').hide();
			jQ('#assign_user').removeAttr('checked');
			jQ('#account_form').show();
			jQ('#email').rules('add',emailrules);  
			jQ('#password').rules('add',passwordrules);  
			jQ('#username').rules('add',usernamerules);  
				} 
				else {
			jQ('#account_form').hide();
			jQ('#password').rules('remove'); 
			jQ('#email').rules('remove');  
			jQ('#username').rules('remove');  
				}
		});
		
		jQ('#assign_user').click( function(){
			if( jQ(this).is(':checked') ) {
				jQ('#account_form').hide();
				jQ('#create_user').removeAttr('checked');
				jQ('#assign_form').show();
				} 
				else {
				jQ('#assign_form').hide();
				}
		});
		
		jQ().ready(function() { 
			var validator =jQ('#adminForm').validate({ 
						rules: { 
			},
						messages: { ";
					if (isset($this->item->username) && $this->item->username == "")  {
						$script .= "password : {
							required: '".\JText::_('INVOICING_VALIDATION_FIELD_REQUIRED')."'
						},";
					}
					$script .= "email: { 
										required: '".\JText::_('INVOICING_VALIDATION_FIELD_REQUIRED')."'
								},
					username: {
						required: '".\JText::_('INVOICING_VALIDATION_FIELD_REQUIRED')."'
					}
						}
			});        
		});
	});";

	\JFactory::getApplication()->getDocument()->addScriptDeclaration($script);
	