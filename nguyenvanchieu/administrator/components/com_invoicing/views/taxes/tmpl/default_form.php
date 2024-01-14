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

//$this->loadHelper('Cparams');
//$this->loadHelper('Select');
//$this->loadHelper('params');
?>

<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm form formValidation">
<input type="hidden" name="option" value="com_invoicing" />
<input type="hidden" name="view" value="taxes" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="invoicing_tax_id" value="<?php echo $this->item->invoicing_tax_id ?>" />
<?php echo JHTML::_( 'form.token' ); ?>

		<div class="adminlist">
			<div class="mb-3 row">
				<div class="col col-3"><?php echo \JText::_('INVOICING_TAX_TAXRATE'); ?></div>
				<div class="col"><input class="form-control required" type="text" size="50" maxsize="255" name="taxrate" value="<?php echo $this->escape($this->item->taxrate); ?>" /></div>
			</div>
			<div class="mb-3 row">
				<div class="col col-3"><?php echo \JText::_('JENABLED'); ?></div>
				<div class="col"><?php echo JHTML::_('select.booleanlist', 'enabled', null, $this->item->enabled); ?></div>
			</div>
		</div>

</form>

<?php
	$script = "
	function jSelectUser_userid(id, username)
{
	document.getElementById('userid').value = id;
	document.getElementById('userid_visible').value = username;
	try {
		document.getElementById('sbox-window').close();	
	} catch(err) {
		SqueezeBox.close();
	}
}
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
        	 taxrate: { 
				   required : true,
				   number : true,
                   min: 0,
				   max :100
               }
         }, 
         messages: { 
        	 taxrate: { 
			     required: '".\JText::_('INVOICING_VALIDATION_FIELD_REQUIRED')."', 
				 number: '".\JText::_('INVOICING_VALIDATION_PERCENT_REQUIRED')."', 
        	     min: '".\JText::_('INVOICING_VALIDATION_PERCENT_REQUIRED')."', 
                 min: '".\JText::_('INVOICING_VALIDATION_PERCENT_REQUIRED')."'
             }
         }         
     });
});";


\JFactory::getApplication()->getDocument()->addScriptDeclaration($script);
	