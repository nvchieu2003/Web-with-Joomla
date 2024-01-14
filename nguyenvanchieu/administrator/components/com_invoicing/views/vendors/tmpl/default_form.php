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
//JHtml::_('behavior.modal');

$document = \JFactory::getDocument();
$document->addScript(JURI::root().'media/com_invoicing/js/jquery.validate.js');

include_once (JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/select.php');
 
//$this->loadHelper('Select');
//$this->loadHelper('Format');

?>
<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm form form formValidation" enctype="multipart/form-data">
	<input type="hidden" name="option" value="com_invoicing" />
	<input type="hidden" name="view" value="vendors" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="invoicing_vendor_id" value="<?php echo $this->item->invoicing_vendor_id ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>

		<div class="adminlist">
		
			<div class="mb-3 row">
				<div class="col col-3">
					<?php echo \JText::_('INVOICING_VENDOR_IMAGE');?>
				</div>
				<div class="col">
					<input type="file" name="filename" id="filename" /><br>
					<?php if ($this->escape($this->item->filename)) { ?>
						<img class="logo_vendor" alt="logo" src="<?php echo JURI::root()."/media/com_invoicing/images/vendor/".$this->escape($this->item->filename); ?>"/>
					<?php } ?>
				</div>
			</div>
				
			<div class="mb-3 row">
				<div class="col col-3"><?php echo \JText::_('INVOICING_VENDOR_CONTACT_NAME'); ?></div>
				<div class="col"><input class="form-control" id="contact_name" type="text" size="50" maxsize="255" name="contact_name" value="<?php echo $this->escape($this->item->contact_name); ?>" /></div>
			</div>

			<div class="mb-3 row">
				<div class="col col-3"><?php echo \JText::_('INVOICING_VENDOR_COMPANY_NAME'); ?></div>
				<div class="col"><input class="form-control required" type="text" size="50" maxsize="255" name="company_name" value="<?php echo $this->escape($this->item->company_name); ?>" /></div>
				
			</div>
			<div class="mb-3 row">
				<div class="col col-3"><?php echo \JText::_('INVOICING_COMMON_URL'); ?></div>
				<div class="col"><input class="form-control required" type="text" size="50" maxsize="255" name="company_url" value="<?php echo $this->escape($this->item->company_url); ?>" /></div>
				
			</div>
			<div class="mb-3 row">
				<div class="col col-3"><?php echo \JText::_('INVOICING_COMMON_PHONE'); ?></div>
				<div class="col"><input class="form-control" type="text" size="50" maxsize="255" name="company_phone" value="<?php echo $this->escape($this->item->company_phone); ?>" /></div>
				
			</div>
			<div class="mb-3 row">
				<div class="col col-3"><?php echo \JText::_('INVOICING_COMMON_MAIL'); ?></div>
				<div class="col"><input class="form-control required" type="text" size="50" maxsize="255" name="company_email" value="<?php echo $this->escape($this->item->company_email); ?>" /></div>
				
			</div>
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
			<div class="mb-3 row">
				<div class="col col-3"><?php echo \JText::_('INVOICING_COMMON_STATE'); ?></div>
				<div class="col"><input class="form-control" type="text" size="50" maxsize="255" name="state" value="<?php echo $this->escape($this->item->state); ?>" /></div>
				
			</div>
			<div class="mb-3 row">
				<div class="col col-3"><?php echo \JText::_('INVOICING_COMMON_ZIP'); ?></div>
				<div class="col"><input class="form-control" type="text" size="50" maxsize="255" name="zip" value="<?php echo $this->escape($this->item->zip); ?>" /></div>
				
			</div>
			<div class="mb-3 row">
				<div class="col col-3"><?php echo \JText::_('INVOICING_COMMON_COUNTRY'); ?></div>
				<div class="col"><?php echo InvoicingHelperSelect::countries($this->item->country,'country',array('class'=>'form-select')) ?></div>
				
			</div>
			<div class="mb-3 row">
				<div class="col col-3"><?php echo \JText::_('INVOICING_COMMON_NOTES'); ?></div>
				<div class="col"><textarea class="form-control" rows="10" cols="50" name="notes"><?php echo $this->escape($this->item->notes);?> </textarea></div>			
			</div>
	</div>

</form>
<?php

	$script = "
	Joomla.submitbutton = function(pressbutton) {
		if (pressbutton == 'cancel') {
				 Joomla.submitform(pressbutton);	
				 return;
		}
			if(jQ('#adminForm').valid()){
				Joomla.submitform(pressbutton);
			}
		else{
					return false;
			}
	}
	
	jQ(document).ready(function() { 
	
			// validate signup form on keyup and submit 
			 var validator =jQ('#adminForm').validate({ 
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
							 }   
			 });
		 
		 //Display the picture selected in file
		 //First onlick on explorer button then if the class is valid add html to display the picture
	});";

	\JFactory::getApplication()->getDocument()->addScriptDeclaration($script);
	