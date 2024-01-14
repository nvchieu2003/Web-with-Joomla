<?php
/**
 *  @package invoicing
 *  @copyright Copyright (c)203 Juloa.com
 *  @license GNU General Public License version 3, or later
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

//$this->loadHelper('Cparams');
//$this->loadHelper('Select');
//$this->loadHelper('params');
//$this->loadHelper('Format');
?>

<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm form formValidation">
<input type="hidden" name="option" value="com_invoicing" />
<input type="hidden" name="view" value="emails" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="invoicing_email_id" value="<?php echo $this->item->invoicing_email_id ?>" />
<?php echo JHTML::_( 'form.token' ); ?>

	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist ">
			<div class="mb-3">
				<?php echo \JText::_($this->escape($this->item->description)) ?>
				<?php //InvoicingHelperFormat::displayTags(); ?>
			</div>
			<div class="mb-3 row">
				<div class="col col-3"><?php echo \JText::_('INVOICING_EMAIL_SUBJECT'); ?></div>
				<div class="col"><input class="form-control required" type="text" maxsize="255" name="subject" value="<?php echo $this->escape($this->item->subject); ?>" /></div>
			</div>
			<div class="mb-3 row">
				<div class="col col-3"><?php echo \JText::_('INVOICING_EMAIL_CONTENT'); ?></div>
				<div class="col"><textarea class="form-control" rows="30" name="content"><?php echo $this->escape($this->item->body) ?> </textarea></div>
				
			</div>

			<div class="mb-3">
				<input type="hidden" name="pdf" value=""/> 
				<div class="form-check">
					<input class="form-check-input" type="checkbox" id="pdf" name="pdf" value='1' <?php if ($this->item->pdf) echo "checked"; ?> />
					<lalel class="form-check-label" for="pdf"><?php echo \JText::_('INVOICING_INVOICE_PDF_ATTACHMENT')?></lalel>
				</div>
			</div>	
	</table>

</form>
<?php
	$script = "
	window.addEvent('domready', function() {
		$$('button.modal').each(function(el) {
			el.addEvent('click', function(e) {
				try {
					new Event(e).stop();
				} catch(anotherMTUpgradeIssue) {
					try {
						e.stop();
					} catch(WhateverIsWrongWithYouIDontCare) {
						try {
							DOMEvent(e).stop();
						} catch(NoBleepinWay) {
							alert('If you see this message, your copy of Joomla! is FUBAR');
						}
					}
				}
				SqueezeBox.fromElement($('userselect'), {
					parse: 'rel'
				});
			});
		});
	});
	Joomla.submitbutton = function(pressbutton) {
		if (pressbutton == 'cancel') {
				 submitform(pressbutton);	
				 return;
		}
			if(jQ('#adminForm').valid()){
				submitform(pressbutton);	
			}else{
					return false;
			}
	}
	
	jQ().ready(function() { 
			// validate signup form on keyup and submit 
			 var validator =jQ('#adminForm').validate({ 
					 rules: { 
						 subject: { 
									 required: true,
					 minlength: 3
								 },
					 
				 content: { 
								 required: true,
					 minlength: 3
					 }
				},
					 messages: { 
				 subject: { 
									 required: '".\JText::_('INVOICING_VALIDATION_FIELD_REQUIRED')."',
					 minlength: jQ.format('".\JText::_('Enter at least {0} characters')."')
								 },
					 
				 content: { 
								 required: '".\JText::_('INVOICING_VALIDATION_FIELD_REQUIRED')."',
					 minlength: jQ.format('".\JText::_('Enter at least {0} characters')."')
					 }
					 }         
			 });
	});";

	\JFactory::getApplication()->getDocument()->addScriptDeclaration($script);
	