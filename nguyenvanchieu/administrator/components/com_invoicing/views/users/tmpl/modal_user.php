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

$input = \JFactory::getApplication()->input;
$document = \JFactory::getDocument();
$document->addScript(JURI::root().'media/com_invoicing/js/jquery.form.js');
$document->addScript(JURI::root().'media/com_invoicing/js/jquery.validate.js');
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
	<input type="hidden" name="view" value="user" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="ajaxcall" value="1" />
	<input type="hidden" name="invoicing_user_id" value="<?php echo $this->item->invoicing_user_id ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
	<?php if (@$this->item->user_id == 0) {?>
	<input type="checkbox" id="create_user" name="create_user">&nbsp;<?php echo \JText::_('INVOICING_CREATE_JOOMLA_ACCOUNT'); ?><br/>
	<?php } ?>
	<?php if (@$this->item->user_id == 0) {?>
	<input type="checkbox" id="assign_user" name="assign_user">&nbsp;<?php echo \JText::_('INVOICING_ASSIGN_JOOMLA_ACCOUNT'); ?><br/>
	<?php } ?>
	<table id="account_form" cellspacing="0" cellpadding="0" width="100%">
	<tr valign="top"><td width="60%">	
	
	
	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
			<tr>
				<td><?php echo \JText::_('INVOICING_USER_USERNAME'); ?></td>
				<td>
					<?php if (@$this->item->user_id != 0) {?>
					<input type="hidden" name="user_id" value="<?php echo $this->escape($this->item->user_id); ?>" />
					<?php echo $this->escape(@$this->item->username); ?>
					<?php } else {?>
					<input type="text" size="50" maxsize="255" id="username" name="username" value="" />
					<?php }?>
					
				</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><?php echo \JText::_('INVOICING_USER_PASSWORD'); ?></td>
				<td><input type="password" size="50" maxsize="255" id="password" name="password" value="" /></td> 
				<td><?php echo \JText::_('INVOICING_USER_PASSWORD_HELP')?></td>
			</tr>
			<tr>
				<td><?php echo \JText::_('INVOICING_USER_EMAIL'); ?></td>
				<td><input type="email" size="50" maxsize="255" name="email" id="email" value="<?php echo $this->escape(@$this->item->email); ?>" /></td>
				<td>&nbsp;</td>
			</tr>
		</table>
	</td></tr>
	</table>
	<br/>
	<?php if (@$this->item->user_id == 0) {?>
	<table id="assign_form" cellspacing="0" cellpadding="0" width="100%">
	<tr valign="top"><td width="60%">	
	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
			<tr>
				<td><?php echo \JText::_('INVOICING_USER_USERNAME'); ?></td>
				<td>
				<?php echo InvoicingHelperSelect::joomlausers('user_id');?>	
				</td>
				<td>&nbsp;</td>
			</tr>
		</table>
	</td></tr>
	</table>
	<br/>
	<?php } ?>
	<table cellspacing="0" cellpadding="0" width="100%">
	<tr valign="top"><td width="60%">	
	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
	<tr valign="top"><td width="60%">	
	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
			<?php /*<tr>
				<td><?php echo \JText::_('INVOICING_USER_ISBUSINESS'); ?></td>
				<td><?php echo JHTML::_('select.booleanlist', 'isbusiness', null, $this->item->isbusiness); ?></td>
				<td>&nbsp;</td>
			</tr>
			*/?>
			<tr>
				<td><?php echo \JText::_('INVOICING_USER_BUSINESSNAME'); ?></td>
				<td><input type="text" size="50" maxsize="255" name="businessname" value="<?php echo $this->escape($this->item->businessname); ?>" /></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><?php echo \JText::_('INVOICING_USER_FIRSTNAME'); ?></td>
				<td><input type="text" size="50" maxsize="255" name="firstname" value="<?php echo $this->escape($this->item->firstname); ?>" /></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><?php echo \JText::_('INVOICING_USER_LASTNAME'); ?></td>
				<td><input type="text" size="50" maxsize="255" name="lastname" value="<?php echo $this->escape($this->item->lastname); ?>" /></td>
				<td>&nbsp;</td>
			</tr>
			<?php /*<tr>
				<td><?php echo \JText::_('INVOICING_USER_OCCUPATION'); ?></td>
				<td><input type="text" size="50" maxsize="255" name="occupation" value="<?php echo $this->escape($this->item->occupation); ?>" /></td> 
				<td>&nbsp;</td>
			</tr>
			<!-- <tr>
				<td><?php echo \JText::_('INVOICING_USER_VATNUMBER'); ?></td>
				<td><input type="text" size="50" maxsize="255" name="vatnumber" value="<?php echo $this->escape($this->item->vatnumber); ?>" /></td>
				<td>&nbsp;</td>
			</tr>
			<tr> 
				<td><?php echo \JText::_('INVOICING_USER_VIESREGISTERED'); ?></td>
				<td><?php echo JHTML::_('select.booleanlist', 'viesregistered', null, $this->item->viesregistered); ?></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><?php echo \JText::_('INVOICING_USER_TAXAUTHORITY'); ?></td>
				<td><input type="text" size="50" maxsize="255" name="taxauthority" value="<?php echo $this->escape($this->item->taxauthority); ?>" /></td>
				<td>&nbsp;</td>
			</tr>-->
			*/?>
			<tr>
				<td><?php echo \JText::_('INVOICING_COMMON_ADDRESS1'); ?></td>
				<td><input type="text" size="50" maxsize="255" name="address1" value="<?php echo $this->escape($this->item->address1); ?>" /></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><?php echo \JText::_('INVOICING_COMMON_ADDRESS2'); ?></td>
				<td><input type="text" size="50" maxsize="255" name="address2" value="<?php echo $this->escape($this->item->address2); ?>" /></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><?php echo \JText::_('INVOICING_COMMON_CITY'); ?></td>
				<td><input type="text" size="50" maxsize="255" name="city" value="<?php echo $this->escape($this->item->city); ?>" /></td>
				<td>&nbsp;</td>
			</tr>
			<?php /*<tr>
				<td><?php echo \JText::_('INVOICING_COMMON_STATE'); ?></td>
				<td><input type="text" size="50" maxsize="255" name="state" value="<?php echo $this->escape($this->item->state); ?>" /></td>
				<td>&nbsp;</td>
			</tr>
			*/?>
			<tr>
				<td><?php echo \JText::_('INVOICING_COMMON_ZIP'); ?></td>
				<td><input type="text" size="50" maxsize="255" name="zip" value="<?php echo $this->escape($this->item->zip); ?>" /></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><?php echo \JText::_('INVOICING_COMMON_COUNTRY'); ?></td>
				<td><?php echo InvoicingHelperSelect::countries($this->item->country,'country') ?></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><?php echo \JText::_('INVOICING_USER_LANDLINE'); ?></td>
				<td><input type="text" size="50" maxsize="255" name="landline" value="<?php echo $this->escape($this->item->landline); ?>" /></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><?php echo \JText::_('INVOICING_USER_MOBILE'); ?></td>
				<td><input type="text" size="50" maxsize="255" name="mobile" value="<?php echo $this->escape($this->item->mobile); ?>" /></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><?php echo \JText::_('INVOICING_COMMON_NOTES'); ?></td>
				<td><textarea rows="10" cols="50" name="notes"><?php echo $this->escape($this->item->notes) ?> </textarea></td>
				<td>&nbsp;</td>				
			</tr>
			<tr>
				<td colspan="3" align="center"><input type="submit" id="submitform" class="button btn" value="<?php echo \JText::_('JSUBMIT') ?>"/></td>
			</tr>
		</table>
	</td></tr>
	</table>
</form>

<?php
	$script = "
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
			if (@$this->item->username == "")  {
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
         },  
         submitHandler: function(form) {
        	 var options = { 
					beforeSubmit: function() {
					},
					success: function(data) { 
						var obj = jQ.parseJSON(data);
						if (obj != null) { 
							if (obj.error == 1) {
								alert(".json_encode(\JText::_('INVOICING_ERROR_CREATE_USER')).");
							} else {
									id = obj.value;
									label = obj.label;
									parentfield = '".$input->get('field', "", "String")."';
									if ( jQ('#'+parentfield+' option[value='+id+']', window.parent.document).size() > 0) {
										jQ('#'+parentfield, window.parent.document).val(id);
										jQ('#'+parentfield+' option[value='+id+']', window.parent.document).html(label);
										window.parent.jQ('#'+parentfield).trigger('change');
									} else { 
										jQ('#'+parentfield, window.parent.document).append('<option value='+id+'>'+label+'</option>');
								   		jQ('#'+parentfield, window.parent.document).val(id);
								   		window.parent.jQ('#'+parentfield).trigger('change');
									}
									try {
										window.parent.document.getElementById('sbox-window').close();
									} catch(err) {
										window.parent.SqueezeBox.close();
									}
							}
						}
					} 
				}; 
			// pass options to ajaxForm 
			jQ('#adminForm').ajaxSubmit(options);
         }            
     });
});";

\JFactory::getApplication()->getDocument()->addScriptDeclaration($script);
