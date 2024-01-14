<?php
/**
 * @package		Invoicing
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

//JHtml::_('behavior.tooltip');
//JHtml::_('behavior.modal');

if(version_compare(JVERSION, '3.0', 'ge')) {
	JHTML::_('jquery.framework');
} else {
	JHTML::_('behavior.mootools');
}

//$this->loadHelper('Select');
//$this->loadHelper('Cparams');
//$this->loadHelper('Format');
?>
<div class="row-fluid">
<div class="span12">

<form action="index.php" method="post" name="adminForm" id="adminForm">
<input type="hidden" name="option" value="com_invoicing" />
<input type="hidden" name="view" value="references" />
<input type="hidden" id="task" name="task" value="browse" />
<input type="hidden" name="hidemainmenu" id="hidemainmenu" value="0" />
<input type="hidden" name="tmpl" value="component" />
<input type="hidden" name="layout" value="modal" />
<input type="hidden" name="boxchecked" id="boxchecked" value="0" />
<input type="hidden" name="filter_order" id="filter_order" value="<?php echo $this->lists->order ?>" />
<input type="hidden" name="filter_order_Dir" id="filter_order_Dir" value="<?php echo $this->lists->order_Dir ?>" />
<?php echo JHTML::_( 'form.token' ); ?>

<div class="filter-select fltrt">
<input type="text" placeholder="<?php echo htmlspecialchars(\JText::_('INVOICING_COMMON_REF')).".." ?>" value="<?php echo htmlspecialchars($this->getModel()->getState('source_key','')) ?>" name="source_key" onchange="this.form.submit();" />
<input type="text" placeholder="<?php echo htmlspecialchars(\JText::_('INVOICING_COMMON_NAME')).".." ?>" value="<?php echo htmlspecialchars($this->getModel()->getState('name','')) ?>" name="name" onchange="this.form.submit();" />
<input type="text" placeholder="<?php echo htmlspecialchars(\JText::_('INVOICING_COMMON_DESCRIPTION')).".." ?>" value="<?php echo htmlspecialchars($this->getModel()->getState('description','')) ?>" name="description" onchange="this.form.submit();" />
</div>

<table class="adminlist table table-striped" id="itemsList">
	<thead>
		<tr>
			<th>
				<?php echo \JText::_('INVOICING_COMMON_REF');?>
			</th>
			<th>
				<?php echo \JText::_('INVOICING_COMMON_NAME');?>
			</th>		
			<th>
				<?php echo \JText::_('INVOICING_COMMON_DESCRIPTION');?>
			</th>	
			<th>
				<?php echo \JText::_('INVOICING_COMMON_UNIT_PRICE');?>
			</th>	
			<th>
				<?php echo \JText::_('INVOICING_COMMON_TAX');?>
			</th>	
			<th>
				<?php echo \JText::_('INVOICING_COMMON_UNIT_PRICE_WITH_TAX');?>
			</th>
		</tr>
	</thead>
	<tfoot>
	</tfoot>
	<tbody>
		<?php if($count = count($this->items)): ?>
		<?php $i = -1; $m = 0; ?>
		<?php foreach ($this->items as $reference) : ?>
		<?php
			$i++; $m = 1-$m;
			//$checkedOut = ($invoice->locked_by != 0);
			$checkedOut = false;
			$ordering = $this->lists->order == 'ordering';
		?>
		<tr class="<?php echo  'row'.$m; ?>">
			<td>
				<a href='javascript:selectItem(<?php echo json_encode($reference->invoicing_reference_id);?>)' >
					<?php echo $reference->source_key; ?>
				</a>
			</td>
			<td>
					<a href='javascript:selectItem(<?php echo json_encode($reference->invoicing_reference_id);?>)'>
					<?php echo htmlspecialchars($reference->name); ?>
					</a>
			</td>
			<td>
					<a href='javascript:selectItem(<?php echo json_encode($reference->invoicing_reference_id);?>)'>
					<?php echo htmlspecialchars($reference->description); ?>
					</a>
			</td>
			<td>
					<?php echo $reference->net_unit_price; ?>
			</td>
			<td>
					<?php echo $reference->tax; ?>
			</td>
			<td>
					<?php echo $reference->gross_unit_price; ?>
			</td>
		</tr>
		<?php endforeach; ?>	
		<?php else: ?>
		<tr>
			<td colspan="20">
				<?php echo  \JText::_('COM_INVOICING_COMMON_NORECORDS') ?>
			</td>
		</tr>
		<?php endif; ?>
	</tbody>
</table>

</form>

<?php 
	$orderlist = array();
	foreach($this->items as $item) {
		$orderlist[$item->invoicing_reference_id] = $item;
	}

	$script = "
	references = ".json_encode($orderlist).";
	function selectItem(id) {
		reference = references[id];
		product_item = parent.addFValue('',reference.name,reference.source_key,reference.description,1,parent.formatPrice(reference.gross_unit_price),reference.tax,parent.formatPrice(reference.net_unit_price));
		parent.computeItemPrice('quantity',product_item);
		parent.computeTotalPrice();
		try {
			window.parent.document.getElementById('sbox-window').close();
		} catch(err) {
			window.parent.SqueezeBox.close();
		}
	}";

	\JFactory::getApplication()->getDocument()->addScriptDeclaration($script);
?>
	
</div>
</div>
