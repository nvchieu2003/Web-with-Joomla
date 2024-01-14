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
<input type="hidden" name="view" value="coupons" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="invoicing_coupon_id" value="<?php echo $this->item->invoicing_coupon_id ?>" />
<?php echo JHTML::_( 'form.token' ); ?>

	<div class="adminlist ">
			<div class="mb-3 row">
				<div class="col col-3"><?php echo \JText::_('INVOICING_COUPON_TITLE'); ?></div>
				<div class="col"><input class="form-control required" type="text" size="50" maxsize="255" name="title" value="<?php echo $this->escape($this->item->title); ?>" /></div>

			</div>
			<div class="mb-3 row">
				<div class="col col-3"><?php echo \JText::_('INVOICING_COUPON_CODE'); ?></div>
				<div class="col"><input class="form-control required" type="text" size="50" maxsize="255" name="code" value="<?php echo $this->escape($this->item->code); ?>" /></div>

			</div>

			<div class="mb-3 row">
				<div class="col col-3"><?php echo \JText::_('INVOICING_COUPON_VALUETYPE'); ?></div>
				<div class="col" class="required"><?php echo invoicingHelperSelect::coupontypes('valuetype',$this->escape($this->item->valuetype),array('class'=>'form-select required')) ?></div>

			</div>
			<div class="mb-3 row">
				<div class="col col-3"><?php echo \JText::_('INVOICING_COUPON_VALUE'); ?></div>
				<div class="col"><input  class="form-control required" type="text" size="50" maxsize="255" id="value" name="value" value="<?php echo $this->escape($this->item->value); ?>" /></div>

			</div>
			<div class="mb-3 row">
				<div class="col col-3"><?php echo \JText::_('JPUBLISHED'); ?></div>
				<div class="col"><?php echo JHTML::_('select.booleanlist', 'enabled', null, $this->item->enabled); ?></div>

			</div>
			<div class="mb-3 row">
				<div class="col col-3"><?php echo \JText::_('INVOICING_COUPON_HITS'); ?></div>
				<div class="col"><input class="form-control" type="number" size="50" name="hits" value="<?php echo $this->escape($this->item->hits); ?>" /></div>

			</div>
			<div class="mb-3 row">
				<div class="col col-3"><?php echo \JText::_('INVOICING_COUPON_HITSLIMIT'); ?></div>
				<div class="col"><input class="form-control" type="number" size="50" name="hitslimit" value="<?php if ($this->escape($this->item->hitslimit) != 0){ echo $this->escape($this->item->hitslimit); } ?>" /></div>

			</div>
			<div class="mb-3 row">
				<div class="col col-3"><?php echo \JText::_('INVOICING_COUPON_USERHITSLIMIT'); ?></div>
				<div class="col"><input class="form-control" type="number" size="50" name="userhitslimit" value="<?php if ($this->escape($this->item->userhitslimit) != 0){ echo $this->escape($this->item->userhitslimit); } ?>" /></div>

			</div>
			<div class="mb-3 row">
			<div class="col col-3"><?php echo \JText::_('INVOICING_COUPON_APPLY_ON'); ?></div>
		    <div class="col">
			<?php 
				echo InvoicingHelperSelect::itemtypes("apply_on",$this->item->apply_on,array('multiple'=>true, 'class'=>'form-select'))?></div>
			</div>
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

function valueCheck() {

	percentrules = { required: true, range: [0, 100], number:true};
	valuerules = {required: true, min:0 , number:true};
	jQ('#value').rules('remove');
	if (jQ('#valuetype').val() == 'percent') {
		
		jQ('#value').rules('add',percentrules);
	} else {
		jQ('#value').rules('add',valuerules);
	}
}

jQ().ready(function() { 
    // validate signup form on keyup and submit 
     var validator =jQ('#adminForm').validate({ 
         rules: { 
        	 title: { 
                 required: true,
				 minlength: 3
               },
			   
			 code: { 
        	     required: true,
				 minlength: 3
			   },
			   
			  value: { 
                 required: true,
                 min: 0,
                 number : true
               },
			   
			   valuetype: {
				 required: true
				},
				
				hits: {
				 digits: true
				},
				
				hitslimit: {
				 digits: true
				},
				
				userhitslimit: {
				 digits: true
				}
			},
         messages: { 
			 title: { 
                 required: '".\JText::_('INVOICING_VALIDATION_FIELD_REQUIRED')."',
				 minlength: jQ.format('".\JText::_('Enter at least {0} characters')."')
               },
			   
			 code: { 
        	     required: '".\JText::_('INVOICING_VALIDATION_FIELD_REQUIRED')."',
				 minlength: jQ.format('".\JText::_('Enter at least {0} characters')."')
			   },
			   
			  value: { 
                 required: '".\JText::_('INVOICING_VALIDATION_FIELD_REQUIRED')."'
               },
			   
			   valuetype: {
				 required: '".\JText::_('INVOICING_VALIDATION_FIELD_REQUIRED')."'
				},
				
				hits: {
				 digits: '".\JText::_('INVOICING_VALIDATION_DIGITS_REQUIRED')."'
				},
				
				hitslimit: {
				 digits: '".\JText::_('INVOICING_VALIDATION_DIGITS_REQUIRED')."'
				},
				
				userhitslimit: {
				 digits: '".\JText::_('INVOICING_VALIDATION_DIGITS_REQUIRED')."'
				}
         }
     });
     valueCheck();
     jQ('#valuetype').change(function() {valueCheck();});
});";

\JFactory::getApplication()->getDocument()->addScriptDeclaration($script);