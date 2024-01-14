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

$content = '';

ob_start();

//$this->loadHelper('Select');
?>
<form action="index.php" method="post" name="modalForm" id="modalForm" class="modalForm form ">
	<input type="hidden" name="option" value="com_invoicing" />
	<input type="hidden" name="view" value="vendor" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="ajaxcall" value="1" />
	<input type="hidden" name="invoicing_vendor_id" value="<?php echo $this->item->invoicing_vendor_id ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
	<table cellspacing="0" cellpadding="0" width="100%">
	<tr valign="top"><td width="60%">	
		<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
			<tr>
				<td><?php echo \JText::_('INVOICING_VENDOR_CONTACT_NAME'); ?></td>
				<td><input type="text" size="50" maxsize="255" name="contact_name" value="<?php echo $this->escape($this->item->contact_name); ?>" /></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><?php echo \JText::_('INVOICING_VENDOR_COMPANY_NAME'); ?></td>
				<td><input type="text" size="50" maxsize="255" name="company_name" value="<?php echo $this->escape($this->item->company_name); ?>" /></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><?php echo \JText::_('INVOICING_COMMON_URL'); ?></td>
				<td><input type="text" size="50" maxsize="255" name="company_url" value="<?php echo $this->escape($this->item->company_url); ?>" /></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><?php echo \JText::_('INVOICING_COMMON_PHONE'); ?></td>
				<td><input type="text" size="50" maxsize="255" name="company_phone" value="<?php echo $this->escape($this->item->company_phone); ?>" /></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><?php echo \JText::_('INVOICING_COMMON_EMAIL'); ?></td>
				<td><input type="text" size="50" maxsize="255" name="company_email" value="<?php echo $this->escape($this->item->company_email); ?>" /></td>
				<td>&nbsp;</td>
			</tr>
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
			<tr>
				<td><?php echo \JText::_('INVOICING_COMMON_STATE'); ?></td>
				<td><input type="text" size="50" maxsize="255" name="state" value="<?php echo $this->escape($this->item->state); ?>" /></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><?php echo \JText::_('INVOICING_COMMON_ZIP'); ?></td>
				<td><input type="text" size="50" maxsize="255" name="zip" value="<?php echo $this->escape($this->item->zip); ?>" /></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><?php echo \JText::_('INVOICING_COMMON_COUNTRY'); ?></td>
				<td><?php echo InvoicingHelperSelect::countries($this->item->country,' country') ?></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><?php echo \JText::_('INVOICING_COMMON_NOTES'); ?></td>
				<td><input type="text" size="50" maxsize="255" name="notes" value="<?php echo $this->escape($this->item->notes); ?>" /></td>
			</tr>
			<tr>
				<td colspan="2" align="center"><input type="submit" id="submitform" class="button btn" value="<?php echo \JText::_('JSUBMIT')?>"/></td>
			</tr>
		</table>
	</td></tr>
	</table>
</form>

<?php

	$content = ob_get_contents();
	ob_end_clean();

	echo HTMLHelper::_(
		'bootstrap.renderModal',
		'modal-invoice', // selector
		array( // options
			'modal-dialog-scrollable' => true,
			'title'  => \JText::_('INVOICING_FILELIST_TEXT'),
			'footer' => '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">'.\JText::_('INVOICING_CLOSE').'</button>',
		),
			'<div id="modal-body">'.$content.'</div>'
	);

	$script = "
	jQ().ready(function() { 
		var validator =jQ('#modalForm').validate({ 
			rules: { 			   
				company_name: { 
								required: true
					},
					
				company_email: {
				 required: true,
				 email: true
					}
					
			 },
					messages: { 			   
				company_name: { 
								required: '".\JText::_('INVOICING_VALIDATION_FIELD_REQUIRED')."', 
					},
					
				company_email: {
				 required: '".\JText::_('INVOICING_VALIDATION_FIELD_REQUIRED')."', 
				 email: '".\JText::_('INVOICING_VALIDATION_EMAIL_REQUIRED')."'
					}
							} ,  
				submitHandler: function(form) {
		 var options = { 
				 beforeSubmit: function() {
			 },
				 success: function(data) { 
					 var obj = jQ.parseJSON(data);
					 if (obj != null) {
						 id = obj.vendor_id;
						 label = obj.vendor_label;
						 parentfield = '".$input->get('field', "", "String")."';
							 jQ('#'+parentfield, window.parent.document).append('<option value='+id+'>'+label+'</option>');
							 jQ('#'+parentfield, window.parent.document).val(id);
							 window.parent.jQ('#'+parentfield, window.parent.document).trigger('change');
							 try {
								 window.parent.document.getElementById('sbox-window').close();
						 } catch(err) {
							 window.parent.SqueezeBox.close();
						 }
					 }
				 } 
		 }; 
		 // pass options to ajaxForm 
		 jQ('#modalForm').ajaxSubmit(options);
				}
		});
});";

	\JFactory::getApplication()->getDocument()->addScriptDeclaration($script);

	