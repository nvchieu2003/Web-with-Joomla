<?php
/**
 * @package		Invoicing
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
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
jimport('joomla.language.helper');

$document = \JFactory::getDocument();
$document->addScript(JURI::root().'media/com_invoicing/js/jquery.validate.js');

//$this->loadHelper('Cparams');
//$this->loadHelper('Select');
//$this->loadHelper('Format');
//$this->loadHelper('params');
//$this->loadHelper('Mail');


?>

<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm form formValidation">
<input type="hidden" name="option" value="com_invoicing" />
<input type="hidden" name="view" value="quotes" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="invoicing_quote_id" value="<?php echo $this->item->invoicing_quote_id ?>" />
<?php echo JHTML::_( 'form.token' ); ?>
	<div id="invoice-container">
			<div class="row">
				<div class="col col-8">
					<fieldset id="details"> 
						<legend><?php echo \JText::_('INVOICING_COMMON_VENDOR')?></legend>
						
					
						<div class="mb-3 row">
						<label class="col"><?php	echo \JText::_('INVOICING_COMMON_VENDOR')?></label>
							<div class="col"><?php echo InvoicingHelperSelect::vendors('vendor_id',$this->item->vendor_id,array('include_find'=>true,'include_add'=>true,'class'=>'form-select'))?></div>
							<div class="col"></div>
						</div>
			
						<div class="mb-3 row">
							<label class="col" >
								<?php if(isset($this->item->vendor->filename)){ ?>
								<img class="logo_vendor" alt="logo" src="<?php echo JURI::root()."/media/com_invoicing/images/vendor/".$this->item->vendor->filename; ?>">
								<?php } ?>
							</label>
							<div class="col" id="vendor_details"></div>
							<div class="col"><a id="link_update_vendor" class="modal" rel="{handler: 'iframe', size: {x: 800, y: 500}}" href=""></a></div>
						</div>	
					
						<div class="mb-3 row">
							<label class="col"><?php echo \JText::_('INVOICING_QUOTE_NUMBER')?></label>
							<div class="col"><?php echo $this->item->quote_number; ?></div> 
						</div>

						<div class="mb-3 row">
							<label class="col"><?php echo \JText::_('INVOICING_COMMON_DATE')?></label>
							<div class="col"><?php 
							echo JHTML::_('calendar', $this->item->created_on, "created_on", "created_on", "%Y-%m-%d");
							?></div>
						</div>
						
						<div class="mb-3 row">
							<label class="col"><?php echo \JText::_('INVOICING_INVOICE_DUE_DATE')?></label>
							<div class="col"><?php echo JHTML::_('calendar',$this->item->due_date, "due_date", "due_date", "%Y-%m-%d");?></div>
						</div>	
						
						<div class="mb-3 row">
							<div class="col" class="sendMailToClient">
							<input type="hidden" name="sendMailToClient" value=""/> 
							<input type="checkbox" name="sendMailToClient" value="1" checked="checked"/>
							<?php echo \JText::_('INVOICING_INVOICE_SENDMAIL')?>
							</div>
						</div>

						<div class="mb-3 row">
							<label class="col"><?php	echo \JText::_('INVOICING_COMMON_INVOICE_PROCESSOR')?></label>
							<div class="col"><?php echo InvoicingHelperSelect::processors($this->item->processor,'processor',array('class'=>'form-select'))?></div>					
						</div>

						<div class="mb-3 row">
							<label class="col"><?php	echo \JText::_('INVOICING_COMMON_INVOICE_PROCESSOR_KEY')?></label>
							<div class="col"><input class="form-control" type="text" id="processor_key" name="processor_key" value="<?php echo $this->escape($this->item->processor_key); ?>" size="40"/></div>					
						</div>

						<?php /*<tr>
							<th><?php	echo \JText::_('INVOICING_COMMON_INVOICE_INTEGRATOR')?></th>
							<td><input type="text" id="generator" name="generator" value="<?php echo $this->escape($this->item->generator); ?>" size="40"/></td>					
						</tr>
						<tr>
							<th><?php	echo \JText::_('INVOICING_COMMON_INVOICE_INTEGRATOR_KEY')?></th>
							<td><input type="text" id="generator_key" name="generator_key" value="<?php echo $this->escape($this->item->generator_key); ?>" size="40"/></td>					
						</tr>
						*/?>		
					
						<div class="mb-3 row">
							<label class="col"><?php echo \JText::_('INVOICING_COMMON_LANGUAGE')?></label>
							<div class="col"><?php echo InvoicingHelperSelect::languages($this->item->language,'language',array('class'=>'form-select'))?></div>			
						</div>
						
						<div class="mb-3 row">
							<label class="col"><?php	echo \JText::_('INVOICING_COMMON_CURRENCY')?></label>
							<div class="col"><?php echo InvoicingHelperSelect::currencies('currency_id',$this->item->currency_id,array('include_none'=>false,'class'=>'form-select'))?></div>
						</div>
						
						<div class="mb-3 row">
							<label class="col"><?php	echo \JText::_('INVOICING_COMMON_INVOICE_SUBJECT')?></label>
							<div class="col"><input class="form-control" type="text" id="subject" name="subject" value="<?php echo $this->escape($this->item->subject); ?>" size="80"/></div>					
						</div>
						
					
					</fieldset>
				</div>
				<div class="col col-4">
					<fieldset id="contacts">
						<legend><?php echo \JText::_('INVOICING_COMMON_CONTACTS')?></legend>
						<div class="mb-3 row">
							<label class="col col-4"><?php echo \JText::_('INVOICING_COMMON_CONTACTS')?></label>
							<div class="col"><?php echo InvoicingHelperSelect::users('user_id',$this->item->user_id,array('include_find'=>true,'include_add'=>true,'class'=>'form-select'))?></div>
						</div>
						
						<div class="mb-3">
							<label></label>
							<div id="user_details">
							</div>
							<a id="link_update_user" class="modal" rel="{handler: 'iframe', size: {x: 800, y: 500}}" href=""></a>
						</div>	
					</fieldset>
				</div>
			</div>
		
			
			<div id="products">
			<fieldset>
				<legend><?php echo \JText::_('INVOICING_COMMON_PRODUCTS')?></legend>
					<input type="button" id="addfvalue" class="button btn btn-primary" value="<?php echo \JText::_('INVOICING_COMMON_ADD')?>" />
					
				<a id="openreferences_link" class="modal" rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="index.php?option=com_invoicing&view=references&task=browse&layout=modal&tmpl=component">
				<input type="button" id="openreferences" class="button btn btn-primary" value="<?php echo \JText::_('INVOICING_COMMON_ACCESS_CATALOG')?>" /></a>
										
					<table id="sort" class="adminlist table table-striped table-long mt-3">
							<thead>
								<tr>
									<th class="th-small"></th>
									<th><?php echo htmlspecialchars(\JText::_('INVOICING_COMMON_REF')) ?></th>
									<th><?php echo \JText::_('INVOICING_COMMON_DESCRIPTION')?></th>
									<th><?php echo \JText::_('INVOICING_COMMON_QUANTITY')?></th>
									<th><?php echo \JText::_('INVOICING_COMMON_UNIT_PRICE')?></th>
									<th><?php echo \JText::_('INVOICING_COMMON_TAX')?></th> 
									<th><?php echo \JText::_('INVOICING_COMMON_UNIT_PRICE_WITH_TAX')?></th>
									<th><?php echo \JText::_('INVOICING_COMMON_TOTAL_PRICE_WITH_TAX')?></th>
									<th></th>
								</tr>
							</thead>
							
							<tbody>
							</tbody>
					</table>
					<div id="subtotal">

					<div>
						<div class="mb-3 row">
						<label class="col"><?php	echo \JText::_('INVOICING_COMMON_AMOUNT_WITHOUT_TAX')?></label>
							<div class="col"><input class="form-control" type="text" id="net_subamount" readonly="readonly" name="net_subamount" value="<?php echo $this->escape($this->item->net_subamount); ?>" size="40"/></div>
						</div>
						<div class="mb-3 row">
						<label class="col"><?php	echo \JText::_('INVOICING_COMMON_AMOUNT_TAX')?></label>	
							<div class="col"><input class="form-control" type="text" id="tax_subamount" readonly="readonly" name="tax_subamount" value="<?php echo $this->escape($this->item->tax_subamount); ?>" size="40"/></div>
						</div>
						<div class="mb-3 row">
						<label class="col"><?php	echo \JText::_('INVOICING_COMMON_AMOUNT_WITH_TAX')?></label>
							<div class="col"><input class="form-control" type="text" id="gross_subamount" readonly="readonly" name="gross_subamount" value="<?php echo $this->escape($this->item->gross_subamount); ?>" size="40"/></div>
						</div>
					</div>
				
					</div>
			</fieldset>
			</div>
			
			<div id="discount">
			<fieldset>
				<legend><?php echo \JText::_('INVOICING_COMMON_DISCOUNT')?></legend>
				<div>
					<div class="mb-3 row">
					<label class="col"><?php	echo \JText::_('INVOICING_COMMON_COUPON')?></label>
						<div class="col"><?php echo InvoicingHelperSelect::coupons('coupon_id',$this->item->coupon_id,array('add_data_attribs'=>true,'class'=>'form-select')); ?></div>
						<div class="col"></div>
					</div>
					<div class="mb-3 row">
					<label class="col"><?php	echo \JText::_('INVOICING_COMMON_DISCOUNT_TYPE')?></label>
						<div class="col"><?php echo InvoicingHelperSelect::coupontypes('discount_type',$this->item->discount_type,array('class'=>'form-select')); ?></div>
						<div class="col">
						<input class="form-control" type="text" id="discount_value" name="discount_value" value="<?php echo $this->escape($this->item->discount_value); ?>" size="40"/>
						<input type="hidden" id="gross_discount_amount" name="gross_discount_amount" value="<?php echo $this->escape($this->item->gross_discount_amount); ?>"/>
						<input type="hidden" id="net_discount_amount" name="net_discount_amount" value="<?php echo $this->escape($this->item->net_discount_amount); ?>"/>
						<input type="hidden" id="tax_discount_amount" name="tax_discount_amount" value="<?php echo $this->escape($this->item->tax_discount_amount); ?>"/>
						</div>
					</div>
				</div>
					
			</fieldset>
			</div>
			
			<div class="row mt-3">
				<div class="col col-6">
					<fieldset id="notes">
						<legend><?php echo \JText::_('INVOICING_COMMON_NOTES')?></legend>
						<textarea class="form-control" cols="60" rows="10" name="notes"><?php echo $this->escape($this->item->notes); ?></textarea>
					</fieldset>
				</div>

				<div class="col col-6">
					<fieldset id="total">
						<legend><?php echo \JText::_('INVOICING_COMMON_TOTAL')?></legend>
						<div>
						<div class="mb-3 row">
							<label class="col"><?php echo \JText::_('INVOICING_COMMON_AMOUNT_WITHOUT_TAX')?></label>
							<div class="col"><input class="form-control" type="text" id="net_amount" readonly="readonly" name="net_amount" value="<?php echo $this->escape($this->item->net_amount); ?>" size="40"/></div>
						</div>
						<div class="mb-3 row">
						<label class="col"><?php	echo \JText::_('INVOICING_COMMON_AMOUNT_TAX')?></label>	
							<div class="col"><input class="form-control" type="text" id="tax_amount" readonly="readonly" name="tax_amount" value="<?php echo $this->escape($this->item->tax_amount); ?>" size="40"/></div>
						</div>
						<div class="mb-3 row">
						<label class="col"><?php	echo \JText::_('INVOICING_COMMON_AMOUNT_WITH_TAX')?></label>
							<div class="col"><input class="form-control" type="text" id="gross_amount" readonly="readonly" name="gross_amount" value="<?php echo $this->escape($this->item->gross_amount); ?>" size="40"/></div>
						</div>
						</div>
					</fieldset>
				</div>
			</div>
			
	
			</div>

			</div> <!-- end invoice-container -->
			
</form>
<?php
	$script = "
	function loadUser() {
		id = jQ('#user_id').val();
		if (id > 0) {
			jQ.getJSON('index.php?option=com_invoicing&format=json&task=read&view=users&id='+id,
							function(json) {				
						 user_details = json.businessname+'<br>'+json.address1+'<br>'+json.address2+'<br>'+json.city+'<br>'+json.zip+'<br>'+json.country; 
						jQ('#user_details').html(user_details);
						link = 'index.php?option=com_invoicing&view=user&task=edit&id='+json.invoicing_user_id+'&layout=modal&tmpl=component&field=user_id';
						jQ('#link_update_user').attr('href',link);
						//alert(link);
						jQ('#link_update_user').html('<img style=\"vertical-align:middle;float:none\" border=\"0\" src=\"".JUri::root()."media/com_invoicing/images/menu/edit.png\" alt=\"".htmlspecialchars(\JText::_('INVOICING_COMMON_EDIT'))."\"/>&nbsp;".htmlspecialchars(\JText::_('INVOICING_COMMON_EDIT'))."');
			});
		}

	}
	
	function loadVendor() {
		id = jQ('#vendor_id').val();
		if (id > 0) {
			jQ.getJSON('index.php?option=com_invoicing&format=json&task=read&view=vendors&id='+id,
							function(json) {				
						vendor_details = '<strong>'+json.company_name+'</strong>';
						if (json.contact_name != '')
							vendor_details +='<br>'+json.contact_name;
						if (json.company_url != '')
							vendor_details +='<br>'+json.company_url;
						if (json.address1 != '')
							vendor_details +='<br>'+json.address1;
						if (json.address2 != '')
							vendor_details +='<br>'+json.address2;
						if (json.zip != '')
							vendor_details +='<br>'+json.zip;
						if (json.city != '')
							vendor_details +='<br>'+json.city;	
						if (json.country != '')
							vendor_details +='<br>'+json.country;
						jQ('#vendor_details').html(vendor_details);	
						link = 'index.php?option=com_invoicing&view=vendor&task=edit&id='+id+'&layout=modal&tmpl=component&field=vendor_id';
						jQ('#link_update_vendor').attr('href',link);
						jQ('#link_update_vendor').html('<img style=\"vertical-align:middle;float:none\" border=\"0\" src=\"".JUri::root()."media/com_invoicing/images/menu/edit.png\" alt=\"".htmlspecialchars(\JText::_('INVOICING_COMMON_EDIT'))."\"/>&nbsp;".htmlspecialchars(\JText::_('INVOICING_COMMON_EDIT'))."');
			});
		}

	}
	
	function formatPrice(num) {
		result = Math.round(num*100)/100;
		return result.toFixed(2);
	}
	function formatQuantity(num) {
		result = Math.round(num*100)/100;
		return result;
	}

	function computeItemPrice(element,product_item) 
	{
		switch(element) {
			case 'tax':	
			case 'gross':
			case 'quantity':
				//var selector = '#'+item2+' .quantity';
				quantity = parseFloat(jQ('#'+product_item+' .quantity').val());
				tax = 100 + parseFloat(jQ('#'+product_item+' .tax_unit').val());
				gross = parseFloat(jQ('#'+product_item+' .net_unit').val());
				net = gross*tax/100;
				jQ('#'+product_item+' .gross_unit').val(formatPrice(net));
				break;
			case 'net':
				quantity = parseFloat(jQ('#'+product_item+' .quantity').val());
				tax = 100 + parseFloat(jQ('#'+product_item+' .tax_unit').val());
				net = parseFloat(jQ('#'+product_item+' .gross_unit').val());
				gross = net*100/tax;
				jQ('#'+product_item+' .net_unit').val(formatPrice(gross));
		}
		
		jQ('#'+product_item+' .item_amount').val(formatPrice(quantity*net));

		computeTotalPrice();
	}

	function changeDiscount() {
		coupon = jQ('#coupon_id');
		if (coupon.val() != -1) {
			couponvalue = jQ('#coupon_id option:selected').data('couponvalue');
			valuetype = jQ('#coupon_id option:selected').data('valuetype');
			jQ('#discount_value').attr('readonly','true');
			jQ('#discount_type').attr('disabled','true');
			jQ('#discount_type_hidden').remove();
			jQ('#discount_type').after(\"<input type='hidden' name='discount_type' value='\"+valuetype+\"' id='discount_type_hidden'/>\");
		} else {
			couponvalue = 0;
			valuetype = 'percent';
			jQ('#discount_value').removeAttr('readonly');
			jQ('#discount_type').removeAttr('disabled');
			jQ('#discount_type_hidden').remove();
		}
		
		jQ('#discount_value').val(couponvalue);
		jQ('#discount_type').val(valuetype);
		computeTotalPrice();
	}

	function computeTotalPrice() {
		net_amount = 0;
		tax_amount = 0;
		gross_amount = 0;
		jQ('#sort tbody tr').each(function() {
			quantity = parseFloat(jQ('.quantity',this).val());
			gross = parseFloat(jQ('.net_unit',this).val());
			taxpercent = parseFloat(jQ('.tax_unit',this).val());
			tax = quantity * (gross*taxpercent/100)
			net_amount += quantity * gross;
			tax_amount   += tax;
			gross_amount   += (quantity * gross) + tax;
		});
		
		jQ('#gross_subamount').val(formatPrice(gross_amount));
		jQ('#tax_subamount').val(formatPrice(tax_amount));
		jQ('#net_subamount').val(formatPrice(net_amount));

		coupontype = jQ('#discount_type').val();
		//alert(coupontype);
		if (coupontype == 'percent') {
			discount = jQ('#discount_value').val();
			jQ('#gross_amount').val(formatPrice(gross_amount*(100-discount)/100));
			jQ('#tax_amount').val(formatPrice(tax_amount*(100-discount)/100));
			jQ('#net_amount').val(formatPrice(net_amount*(100-discount)/100));
			jQ('#gross_discount_amount').val(gross_amount*discount/100);
			jQ('#net_discount_amount').val(net_amount*discount/100);
			jQ('#tax_discount_amount').val(tax_amount*discount/100);
		} else if (coupontype == 'value') {
			gross_discount = jQ('#discount_value').val();
			if (gross_discount > gross_amount) {
				gross_discount = gross_amount;
			}
			tax = parseFloat(jQ('.tax_unit').first().val());
			net_discount = gross_discount * 100 / (100+tax);
			tax_discount = gross_discount-net_discount;
			jQ('#gross_amount').val(formatPrice(gross_amount-gross_discount));
			jQ('#tax_amount').val(formatPrice(tax_amount-tax_discount));
			jQ('#net_amount').val(formatPrice(net_amount-net_discount));
			jQ('#gross_discount_amount').val(gross_discount);
			jQ('#net_discount_amount').val(net_discount);
			jQ('#tax_discount_amount').val(tax_discount);
		} else {
			jQ('#gross_amount').val(formatPrice(gross_amount));
			jQ('#tax_amount').val(formatPrice(tax_amount));
			jQ('#net_amount').val(formatPrice(net_amount));
			jQ('#gross_discount_amount').val(0);
			jQ('#net_discount_amount').val(0);
			jQ('#tax_discount_amount').val(0);
		}
	}
								
	var item_i = 0;
	function addFValue(id,name,source_key,description,quantity,gross,tax,net) {
		item_i++;
		var data = '<tr id=\"item_'+item_i+'\" class=\"ui-state-default values\">\
						<td class=\'td-small\'>\
							<span id=\'empty\' class=\'btn btn-success\'>...</span>\
							<input type=\"hidden\" name=\"itemid[]\" value=\"'+id+'\">\
						</td>\
						<td>\
							<input class=\"form-control\" type=\"text\" placeholder=\"".htmlspecialchars(\JText::_('INVOICING_COMMON_REF'))."..\" name=\"source_key[]\" style=\"width: 100px;\" value=\"'+source_key+'\" /> <br/>\
						</td>\
						<td>\
							<input class=\"form-control\" type=\"text\" placeholder=\"".htmlspecialchars(\JText::_('INVOICING_COMMON_TITLE'))."...\" name=\"name[]\" style=\"width: 340px;\" value=\"'+name+'\" /> <br/>\
							<textarea class=\"form-control\" cols=\"50\" rows=\"3\" style=\"width: 340px;\" name=\"description[]\" placeholder=\"".htmlspecialchars(\JText::_('INVOICING_COMMON_DESCRIPTION'))."...\">'+description+'</textarea>\
						</td>\
						<td><input class=\"form-control\" type=\"text\" style=\"width: 37px;\" name=\"quantity[]\" class=\"quantity\" value=\"'+quantity+'\" /></td>\
						<td><input class=\"form-control\" type=\"text\" name=\"net_unit[]\" style=\"width: 105px;\" class=\"net_unit\" value=\"'+gross+'\" /></td>\
						<td>".str_replace("\n",'',InvoicingHelperSelect::taxes("tax[]",'',array('class'=>'tax_unit form-select',"style"=>"width:inherit")))."</td>\
						<td><input class=\"form-control\" type=\"text\" name=\"gross_unit[]\" style=\"width: 105px;\" class=\"gross_unit\" value=\"'+net+'\" /></td>\
						<td><input class=\"form-control\" type=\"text\" readonly=\"readonly\" name=\"item_amount[]\" class=\"item_amount\" style=\"width: 105px;\" value=\"dsfsdfds\" /></td>\
						<td style=\"white-space: nowrap;\"><span class=\"ui-icon-cancel\"><img style=\"vertical-align:middle;float:none\" src=\"".JUri::root()."media/com_invoicing/images/menu/remove.png\" alt=\"".htmlspecialchars(\JText::_('INVOICING_COMMON_DELETE'))."\"/>&nbsp;".htmlspecialchars(\JText::_('INVOICING_COMMON_DELETE'))."</span></td>\
					</tr>';
		jQ('#sort tbody').append(data);
		jQ('#item_'+item_i+' .tax_unit').val(tax);
		jQ('#item_'+item_i+' .item_amount').val(formatPrice(quantity*net));
		return 'item_'+item_i;
	}
	";

	
		$script .= "
	var fixHelper = function(e, ui) {
		ui.children().each(function() {
			jQ(this).width(jQ(this).width());
		});
		return ui;
	};
	
	jQ(function() {
		jQ(document).ready(function(){
			";

			if (count($this->item->items) > 0) {
				foreach($this->item->items as $item) {
					$script .= "addFValue(".$item->invoicing_quote_item_id.",";
					$script .= json_encode(htmlspecialchars($item->name)).",";
					$script .= json_encode(htmlspecialchars($item->source_key)).",";
					$script .= json_encode(htmlspecialchars($item->description)).",";
					$script .= $item->quantity.",";
					$script .= $item->net_unit_price.",";
					$script .= $item->tax.",";
					$script .= $item->gross_unit_price.");";
				}
			} else {
				$script .= "addFValue('','','','',1,0,0,0);
				addFValue('','','','',1,0,0,0);";
			}

			$script .= "
			computeTotalPrice();
			jQ('#sort tbody').sortable({helper: fixHelper,placeholder: 'ui-state-highlight',forcePlaceholderSize:true,
				'start': function (event, ui) {
							ui.placeholder.html('<td colspan=\"6\"></td>');
					},
					axis:'y'});
		});
		
		jQ( '#products #addfvalue' ).click(function() {
			addFValue('','',1,0,0,0);
		});
		
		jQ( '#sort tbody').on('click', '.ui-icon-cancel',function() {
			jQ(this).parent().parent().remove();
			computeTotalPrice();
		});

		jQ( '#coupon_id').change(function() {
			changeDiscount();
		});

		jQ( '#sort tbody').on('change', '.quantity',function() {
			product_item = jQ(this).parent().parent().attr('id');
			quantity = parseFloat(jQ(this).val());
			if ((isNaN(quantity))||(quantity == 0))
				quantity = 1;
			jQ(this).val(formatQuantity(quantity));
			computeItemPrice('quantity',product_item);
			return true;
		});

		jQ( '#sort tbody').on('change', '.net_unit',function() {
			product_item = jQ(this).parent().parent().attr('id');
			gross = parseFloat(jQ(this).val());
			if (isNaN(gross))
				gross = 0;
			jQ(this).val(formatPrice(gross));
			computeItemPrice('gross',product_item);
			return true;
		});

		jQ( '#sort tbody').on('change', '.tax_unit',function() {
			product_item = jQ(this).parent().parent().attr('id');
			computeItemPrice('tax',product_item);
			return true;
		});

		jQ( '#discount_value').change(function() {
			discount = parseFloat(jQ(this).val());
			if (isNaN(discount))
				discount = 0;
			jQ(this).val(formatPrice(discount));
			computeTotalPrice();
		});

		jQ( '#discount_type').change(function() {
			computeTotalPrice();
		});

		jQ('#user_id').change(function() {
			loadUser();
		});
		
		jQ('#vendor_id').change(function() {
			loadVendor();
		});
		
		//Add a checkbox when a invoice get the status pending
		jQ('#status').change(function() {
			options = jQ(this).val();
			if ((options == 'PENDING')||(options == 'PAID')){
				jQ('.sendMailToClient').css('display','block');
			}
			else {
				jQ('.sendMailToClient').css('display','none');
			}
		});

		jQ( '#sort tbody').on('change', '.gross_unit',function() {
			product_item = jQ(this).parent().parent().attr('id');
			net = parseFloat(jQ(this).val());
			if (isNaN(net))
				net = 0;
			jQ(this).val(formatPrice(net));
			computeItemPrice('net',product_item);
			return true;
		});

		loadUser();
		loadVendor();
	});
	
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

function jSelectVendor_vendorid(id, contactname)
{
	document.getElementById('vendorid').value = id;
	document.getElementById('vendorid_visible').value = contactname;
	try {
		document.getElementById('sbox-window').close();	
	} catch(err) {
		SqueezeBox.close();
	}
}

function valueCheck() {

	percentrules = { range: [0, 100], number:true};
	valuerules = {min:0 , number:true};
	jQ('#discount_value').rules('remove');
	if (jQ('#discount_type').val() == 'percent') {
		
		jQ('#discount_value').rules('add',percentrules);
	} else {
		jQ('#discount_value').rules('add',valuerules);
	}
}

jQ().ready(function() { 
    // validate signup form on keyup and submit 
     var validator =jQ('#adminForm').validate({ 
         rules: { 
        	 user_id: { 
                 required: true
               },
			   
			 vendor_id: { 
        	     required: true
			   },
			   
			  status: { 
                 required: true
               },
			   
			   gross_amount: {
				 number: true,
				 min: 0
				},
				
				currency_id: {
				 required : true
				}
			},
         messages: { 
			 user_id: { 
                 required: '".\JText::_('INVOICING_VALIDATION_FIELD_REQUIRED')."'
               },
			   
			 vendor_id: { 
        	     required: '".\JText::_('INVOICING_VALIDATION_FIELD_REQUIRED')."'
			   },
			   
			  status: { 
                   required: '".\JText::_('INVOICING_VALIDATION_FIELD_REQUIRED')."'
                 },
			   
			   gross_amount: {
			     number: '".\JText::_('INVOICING_VALIDATION_FLOAT_REQUIRED')."',
				 required: '".\JText::_('INVOICING_VALIDATION_FLOAT_REQUIRED')."'
				},
				
				currency_id: {
				 required : '".\JText::_('INVOICING_VALIDATION_CURRENCY_NEEDED')."'
				}
         }         
     });
     valueCheck();
     jQ('#discount_type').change(function() {valueCheck();});
});";

\JFactory::getApplication()->getDocument()->addScriptDeclaration($script);