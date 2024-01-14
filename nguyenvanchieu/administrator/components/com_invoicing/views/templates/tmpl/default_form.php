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
JHtml::_('bootstrap.modal');

$document = \JFactory::getDocument();
$document->addScript(JURI::root().'media/com_invoicing/js/jquery.validate.js');

include_once (JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/select.php');

//$this->loadHelper('Cparams');
//$this->loadHelper('Select');
//$this->loadHelper('params');
//$this->loadHelper('Format');

$input = \JFactory::getApplication()->input;
$id = $input->getInt('id', 0);
switch ($id) {
	default:
	case 1: 
		$type  = "order"; 
		break;
	case 2:
		$type  = "invoice"; 
		break;
	case 3:
		$type  = "quote"; 
		break;
}
?>

<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm form formValidation">
<input type="hidden" name="option" value="com_invoicing" />
<input type="hidden" name="view" value="templates" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="invoicing_template_id" value="<?php echo $this->item->invoicing_template_id ?>" />
<?php echo JHTML::_( 'form.token' ); ?>

				<div id="template_tags">
						<?php //InvoicingHelperFormat::displayTags(); ?>
				</div>

				<div id="template_description">
					<?php echo \JText::_('INVOICING_TEMPLATE_DESCRIPTION'); ?>
					<?php echo \JText::_($this->escape($this->item->description)) ?>
				</div>

				<div id="template_htmlcontent"><?php echo \JText::_('INVOICING_TEMPLATE_HTMLCONTENT'); ?></div>

				<div id="template_htmlbutton">
					<button
						class="btn btn-sm btn-info w5rem mb-1" 
						data-bs-toggle="modal" 
						data-bs-target="#modal-box" 
						data-bs-title="<?php echo \JText::_('INVOICING_TEMPLATES_LIST_TEMPLATES_HTML'); ?>" 
						data-bs-id="openhtmlcontent_link" 
						data-bs-action="listtemplateshtml" 
						onclick="return false;">
						<?php echo \JText::_('INVOICING_TEMPLATES_LIST_TEMPLATES_HTML'); ?>
					</button>
				</div>
				
				<div id="template_htmlcontent_textarea">
					<textarea class="form-control" id="htmlcontent" name="htmlcontent">
						<?php echo $this->escape($this->item->htmlcontent) ?>
					</textarea>
				</div>
				
				<div id="template_usehtmlforpdf">
					<input type="hidden" name="usehtmlforpdf" value=""/> 
					<div class="form-check">
						<input class="form-check-input" type="checkbox" id="usehtmlforpdf" name="usehtmlforpdf" value='1' <?php if ($this->item->usehtmlforpdf) echo "checked"; ?> />
						<label for="usehtmlforpdf" class="form-check-label">
							<?php echo \JText::_('INVOICING_TEMPLATE_USEHTMLFORPDF')?>
						</label>
					</div>
				</div>

			<div id="_pdfcontent">
				<div id="template_pdfcontent"><?php echo \JText::_('INVOICING_TEMPLATE_PDFCONTENT'); ?></div>
					<div id="template_pdfbutton">
						<button
							class="btn btn-sm btn-info w5rem mb-1" 
							data-bs-toggle="modal" 
							data-bs-target="#modal-box" 
							data-bs-title="<?php echo \JText::_('INVOICING_TEMPLATES_LIST_TEMPLATES_PDF'); ?>" 
							data-bs-id="openpdfcontent_link" 
							data-bs-action="listtemplatespdf" 
							onclick="return false;">
							<?php echo \JText::_('INVOICING_TEMPLATES_LIST_TEMPLATES_PDF'); ?>
						</button>
					</div>
					<div id="template_pdfcontent_textarea">
						<textarea class="form-control" id="pdfcontent" name="pdfcontent"><?php echo $this->escape($this->item->pdfcontent) ?></textarea>
					</div>
				</div>
			</div>
	
</form>

<?php include_once JPATH_ADMINISTRATOR . '/components/com_invoicing/views/templates/tmpl/modal.php'; ?>

<?php
	$script = "
	// Joomla.submitbutton = function(pressbutton) {
	// 	if (pressbutton == 'cancel') {
	// 			 submitform(pressbutton);	
	// 			 return;
	// 	}
	// 		if(jQ('#adminForm').valid()){
	// 			submitform(pressbutton);	
	// 		}else{
	// 				return false;
	// 		}
	// }
	
	
	
	jQ().ready(function() { 
		let parentfield = 'htmlcontent';
		let templateModal = document.getElementById('modal-box');
		templateModal.addEventListener('show.bs.modal', function (event) {
			// Button that triggered the modal
			let button = event.relatedTarget
			// Extract info from data-bs-* attributes
			let action = button.getAttribute('data-bs-action')
			console.log(action);
			if(action == 'listtemplatespdf') {
				parentfield = 'pdfcontent';
			}
		});

		jQ('.templatelink').click(function() {
			template = jQ(this).attr('template');
			jQ.get('".JUri::root()."administrator/components/com_invoicing/templates/".$type."/'+template+'?time=".time()."', function(data) {
				jQ('#'+parentfield,window.parent.document).val(data);
			});
		});

		jQ('#usehtmlforpdf').click( function(){
			if( jQ(this).is(':checked') ) {
				jQ('#_pdfcontent').hide();
		   } 
		   else {
			   jQ('#_pdfcontent').show();
		   }
	   });

			// validate signup form on keyup and submit 
			 var validator =jQ('#adminForm').validate({ 
					 rules: { 
				 content: { 
								 required: true
					 }
				},
					 messages: { 
				 content: { 
								 required: '".\JText::_('INVOICING_VALIDATION_FIELD_REQUIRED')."'
					 }
					 }         
			 });
		 
		if( jQ('#usehtmlforpdf').is(':checked') ) {
			 jQ('#_pdfcontent').hide();
		} 
	});";
	
	\JFactory::getApplication()->getDocument()->addScriptDeclaration($script);
	