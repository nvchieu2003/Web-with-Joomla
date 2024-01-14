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
<input type="hidden" name="boxchecked" id="boxchecked" value="0" />
<input type="hidden" name="filter_order" id="filter_order" value="<?php echo $this->lists->order ?>" />
<input type="hidden" name="filter_order_Dir" id="filter_order_Dir" value="<?php echo $this->lists->order_Dir ?>" />
<?php echo JHTML::_( 'form.token' ); ?>

<div class="filter-select fltrt">
<input class="form-control" type="text" placeholder="<?php echo htmlspecialchars(\JText::_('INVOICING_COMMON_REF')) ?>" value="<?php echo htmlspecialchars($this->filters['source_key']) ?>" name="source_key" onchange="this.form.submit();" />
<input class="form-control" type="text" placeholder="<?php echo htmlspecialchars(\JText::_('INVOICING_COMMON_NAME'))?>" value="<?php echo htmlspecialchars($this->filters['name']) ?>" name="name" onchange="this.form.submit();" />
<input class="form-control" type="text" placeholder="<?php echo htmlspecialchars(\JText::_('INVOICING_COMMON_DESCRIPTION')) ?>" value="<?php echo htmlspecialchars($this->filters['description']) ?>" name="description" onchange="this.form.submit();" />
</div>
<div style="clear:both"></div>

<table class="adminlist table table-striped" id="itemsList">
	<thead>
		<tr>
			<th>
				<?php echo JHTML::_('grid.sort', 'Num', 'invoicing_reference_id', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
			</th>
			<th></th>
			<th width="20%">
				<?php echo JHTML::_('grid.sort', 'INVOICING_REFERENCE_ID', 'invoicing_references_id', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
			</th>
			<th>
				<?php echo \JText::_('INVOICING_COMMON_REF');?>
			</th>
			<th>
				<?php echo \JText::_('INVOICING_REFERENCE_NAME');?>
			</th>	
			<th>
				<?php echo \JText::_('INVOICING_REFERENCE_DESCRIPTION');?>
			</th>	
			<th>
				<?php echo \JText::_('INVOICING_REFERENCE_NET_UNIT_PRICE');?>
			</th>	
			<th>
				<?php echo \JText::_('INVOICING_REFERENCE_TAX');?>
			</th>	
			<th>
				<?php echo \JText::_('INVOICING_REFERENCE_GROSS_UNIT_PRICE');?>
			</th>	
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="20">
				<?php if($this->pagination->total > 0) echo $this->pagination->getListFooter() ?>
			</td>
		</tr>
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
				<?php echo $reference->invoicing_reference_id; ?>
			</td>
			<td>
				<?php echo JHTML::_('grid.id', $i, $reference->invoicing_reference_id, $checkedOut); ?>
			</td>
			<td>
				<a href="index.php?option=com_invoicing&view=references&task=edit&cid=<?php echo $reference->invoicing_reference_id; ?>">
				<?php echo $reference->invoicing_reference_id; ?>
				</a>
			</td>
			<td>
					<a href="index.php?option=com_invoicing&view=references&task=edit&cid=<?php echo $reference->source_key; ?>">
					<?php echo htmlspecialchars($reference->source_key); ?>
					</a>
			</td>
			<td>
					<a href="index.php?option=com_invoicing&view=references&task=edit&cid=<?php echo $reference->invoicing_reference_id; ?>">
					<?php echo htmlspecialchars($reference->name); ?>
					</a>
			</td>
			<td>
					<a href="index.php?option=com_invoicing&view=references&task=edit&cid=<?php echo $reference->invoicing_reference_id; ?>">
					<?php echo htmlspecialchars($reference->description); ?>
					</a>
			</td>
			<td>
					<?php echo $reference->net_unit_price; ?>
				</a>
			</td>
			<td>
					<?php echo $reference->tax; ?>
				</a>
			</td>
			<td>
					<?php echo $reference->gross_unit_price; ?>
				</a>
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
	
</div>
</div>
