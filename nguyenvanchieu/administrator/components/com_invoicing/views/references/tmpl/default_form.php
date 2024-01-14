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
jimport('joomla.language.helper');


$document = \JFactory::getDocument();
$document->addScript(JURI::root().'media/com_invoicing/js/jquery.validate.js');

include_once (JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/select.php');

//$this->loadHelper('Cparams');
//$this->loadHelper('Select');
//$this->loadHelper('Format');
//$this->loadHelper('params');
//$this->loadHelper('Mail');

?>

<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm form formValidation">
<input type="hidden" name="option" value="com_invoicing" />
<input type="hidden" name="view" value="references" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="invoicing_reference_id" value="<?php echo $this->item->invoicing_reference_id ?>" />
<?php echo JHTML::_( 'form.token' ); ?>
	<div id="invoice-container">
			<div id="products">
				<div id="table">
					<div class="mb-3 row">
						<div class="col col-3">
							<?php echo \JText::_('INVOICING_COMMON_REF');?>
                        </div>
                        <div class="col">
							<input id="source_key" type="text" name="source_key" class="source_key form-control" value="<?php echo $this->escape($this->item->source_key);?>" />
						</div>
					</div>
					<div class="mb-3 row">
						<div class="col col-3">
							<?php echo \JText::_('INVOICING_COMMON_NAME');?>
                        </div>
                        <div class="col">
							<input id="name" type="text" name="name" class="name form-control" value="<?php echo $this->escape(@$this->item->name);?>" />
						</div>
					</div>
					<div class="mb-3 row">
						<div class="col col-3">
							<?php echo \JText::_('INVOICING_COMMON_DESCRIPTION');?>
                        </div>
                        <div class="col">
							<textarea class="form-control" id="description" cols="50" rows="3" name="description"><?php echo $this->escape($this->item->description);?></textarea>
						</div>
					</div>
					<div class="mb-3 row">
						<div class="col col-3">
							<?php echo \JText::_('INVOICING_COMMON_QUANTITY');?>
                        </div>
                        <div class="col">
							<input id="quantity" type="text" name="quantity" class="quantity form-control" value="<?php echo $this->escape($this->item->quantity);?>" />
						</div>
					</div>
					<div class="mb-3 row">
						<div class="col col-3">
							<?php echo \JText::_('INVOICING_COMMON_UNIT_PRICE');?>
                        </div>
                        <div class="col">
							<input id="net_unit" type="text" name="net_unit_price" class="net_unit form-control" value="<?php echo $this->escape($this->item->net_unit_price);?>" />
						</div>
					</div>
					<div class="mb-3 row">
						<div class="col col-3">
							<?php echo \JText::_('INVOICING_COMMON_TAX');?>
                        </div>
                        <div class="col">
							<?php echo InvoicingHelperSelect::taxes("tax",$this->escape($this->item->tax),array('class'=>'tax_unit form-select')) ?>
						</div>
					</div>
					<div class="mb-3 row">
						<div class="col col-3">
							<?php echo \JText::_('INVOICING_COMMON_UNIT_PRICE_WITH_TAX');?>
                        </div>
                        <div class="col">
							<input id="gross_unit_price" type="text" name="gross_unit_price" class="form-control gross_unit" value="<?php echo $this->escape($this->item->gross_unit_price);?>" />
						</div>
					</div>
					<div class="mb-3 row">
						<div class="col col-3">
							<?php echo \JText::_('INVOICING_COMMON_TOTAL_PRICE_WITHOUT_TAX');?>
                        </div>
                        <div class="col">
							<input id="item_net_amount" type="text" readonly="readonly" name="net_amount" class="form-control item_net_amount" value="<?php echo $this->escape($this->item->net_amount);?>" />
						</div>
					</div>
					<div class="mb-3 row">
						<div class="col col-3">
							<?php echo \JText::_('INVOICING_COMMON_TOTAL_PRICE_WITH_TAX');?>
                        </div>
                        <div class="col">
							<input id="item_amount" type="text" readonly="readonly" name="gross_amount" class="form-control item_amount" value="<?php echo $this->escape($this->item->gross_amount);?>" />
						</div>
					</div>
					<div class="mb-3 row">
						<div class="col col-3">
							<?php echo \JText::_('INVOICING_COMMON_SOURCE');?>
                        </div>
                        <div class="col">
							<input id="source" type="text" name="source" class="form-control source" value="<?php echo $this->escape(@$this->item->source);?>" />
						</div>
					</div>
				</div>
			</div>
	</div> <!-- end invoice-container -->	
</form>

<?php
	$script = "
	function formatPrice(num) {
		result = Math.round(num*100)/100;
		return result.toFixed(2);
	}
	function formatQuantity(num) {
		result = Math.round(num*100)/100;
		return result;
	}

	function computeItemPrice(element) 
	{
		switch(element) {
			case 'tax':	
			case 'net':
			case 'quantity':
				net = parseFloat(jQ('.net_unit').val());
				quantity = parseFloat(jQ('.quantity').val());
				tax = 100 + parseFloat(jQ('.tax_unit').val());
				gross = parseFloat(jQ('.gross_unit').val());
				gross = net*tax/100;
				// gross = net*100/tax;
				jQ('.gross_unit').val(formatPrice(gross));
				break;
			case 'gross':
				//var selector = '#'+item2+' .quantity';
				quantity = parseFloat(jQ('.quantity').val());
				tax = 100 + parseFloat(jQ('.tax_unit').val());
				gross = parseFloat(jQ('.gross_unit').val());
				net = gross*100/tax;
				jQ('.gross_unit').val(formatPrice(gross));
				jQ('.net_unit').val(formatPrice(net));
				break;
		}
		
		jQ('.item_amount').val(formatPrice(quantity*gross));
		jQ('.item_net_amount').val(formatPrice(quantity*net));
	}

	var fixHelper = function(e, ui) {
		ui.children().each(function() {
			jQ(this).width(jQ(this).width());
		});
		return ui;
	};
	
	jQ(function() {
		jQ( '#table').on('change', '.quantity',function() {
			quantity = parseFloat(jQ(this).val());
			if ((isNaN(quantity))||(quantity == 0))
				quantity = 1;
			jQ(this).val(formatQuantity(quantity));
			computeItemPrice('quantity');
			return true;
		});

		jQ( '#table').on('change', '.net_unit',function() {
			net = parseFloat(jQ(this).val());
			if (isNaN(net))
			net = 0;
			jQ(this).val(formatPrice(net));
			computeItemPrice('net');
			return true;
		});

		jQ( '#table').on('change', '.tax_unit',function() {
			computeItemPrice('tax');
			return true;
		});


		jQ( '#table').on('change', '.gross_unit',function() {
			gross = parseFloat(jQ(this).val());
			if (isNaN(net))
				gross = 0;
			jQ(this).val(formatPrice(gross));
			computeItemPrice('gross');
			return true;
		});

	});
	
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
						 description: { 
									 required: true
								 }
				},
					 messages: { 
				 description: { 
									 required: '".\JText::_('INVOICING_VALIDATION_FIELD_REQUIRED')."'
								 }
					 }         
			 });
	});";

	\JFactory::getApplication()->getDocument()->addScriptDeclaration($script);
	