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
?>

<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm form formValidation">
<input type="hidden" name="option" value="com_invoicing" />
<input type="hidden" name="view" value="currencies" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="invoicing_currency_id" value="<?php echo $this->item->invoicing_currency_id ?>" />
<?php echo JHTML::_( 'form.token' ); ?>


		<div class="adminlist">
			<div class="mb-3 row">
				<div class="col col-3"><?php echo \JText::_('INVOICING_CURRENCY_SYMBOL'); ?></div>
				<div class="col"><input class="form-control required" type="text" size="20" maxsize="255" name="symbol" value="<?php echo $this->escape($this->item->symbol); ?>" /></div>
			</div>

			<div class="mb-3 row">
				<div class="col col-3"><?php echo \JText::_('INVOICING_CURRENCY_CODE'); ?></div>
				<div class="col"><input class="form-control required" type="text" size="10" maxsize="255" name="code" value="<?php echo $this->escape($this->item->code); ?>" /></div>
				
			</div>

			<div class="mb-3 row">
				<div class="col col-3"><?php echo \JText::_('INVOICING_CURRENCY_SYMBOL_POSITION'); ?></div>
				<div class="col"><?php echo InvoicingHelperSelect::symbolposition($this->escape($this->item->symbol_position),null,array('class'=>'form-select'))?></div>
				
			</div>

            <div class="mb-3 row">
				<div class="col col-3"><?php echo \JText::_('INVOICING_CURRENCY_NUMBER_DECIMALS'); ?></div>
				<div class="col"><input class="form-control" type="text" size="50" maxsize="255" name="number_decimals" value="<?php echo $this->escape($this->item->number_decimals); ?>" /></div>
				
			</div>	

			<div class="mb-3 row">
				<div class="col col-3"><?php echo \JText::_('INVOICING_CURRENCY_DECIMAL_SEPARATOR'); ?></div>
				<div class="col"><input class="form-control" type="text" size="50" maxsize="255" name="decimal_separator" value="<?php echo $this->escape($this->item->decimal_separator); ?>" /></div>
				
			</div>	

			<div class="mb-3 row">
				<div class="col col-3"><?php echo \JText::_('INVOICING_CURRENCY_THOUSAND_SEPARATOR'); ?></div>
				<div class="col"><input class="form-control" type="text" size="50" maxsize="255" name="thousand_separator" value="<?php echo $this->escape($this->item->thousand_separator); ?>" /></div>
				
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
        	 symbol: { 
                 required: true
               },
			   
			 code: { 
        	     required: true
			   },
			},
         messages: { 
			 symbol: { 
                 required: '".\JText::_('INVOICING_VALIDATION_FIELD_REQUIRED')."'
               },
			   
			 code: { 
        	     required: '".\JText::_('INVOICING_VALIDATION_FIELD_REQUIRED')."'
			   }
         }         
     });
});";

\JFactory::getApplication()->getDocument()->addScriptDeclaration($script);