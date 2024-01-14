<?php
/**
 * @package		Invoicing
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

use Joomla\CMS\Button\PublishedButton;
//JHtml::_('behavior.tooltip');
if(version_compare(JVERSION, '3.0', 'ge')) {
	JHTML::_('jquery.framework');
} else {
	JHTML::_('behavior.mootools');
}

include_once (JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/select.php');

//$this->loadHelper('Select');
//$this->loadHelper('Cparams');
//$this->loadHelper('Format');

$document = \JFactory::getDocument();
$document->addStyleSheet(JURI::root().'media/com_invoicing/css/fits.css');
?>
<div class="row-fluid">
<div class="span12">

<form action="index.php" method="post" name="adminForm" id="adminForm">
<input type="hidden" name="option" value="com_invoicing" />
<input type="hidden" name="view" value="taxes" />
<input type="hidden" id="task" name="task" value="browse" />
<input type="hidden" name="hidemainmenu" id="hidemainmenu" value="0" />
<input type="hidden" name="boxchecked" id="boxchecked" value="0" />
<input type="hidden" name="filter_order" id="filter_order" value="<?php echo $this->lists->order ?>" />
<input type="hidden" name="filter_order_Dir" id="filter_order_Dir" value="<?php echo $this->lists->order_Dir ?>" />
<?php echo JHTML::_( 'form.token' ); ?>

<button class="hideToAdjust"></button>

<div class="filter-select fltrt">
<?php echo InvoicingHelperSelect::enabled($this->filters['enabled'], 'enabled', array('onchange'=>'this.form.submit();', 'class'=>'form-select')) ?>
</div>

<table class="adminlist table table-striped" id="itemsList">
	<thead>
		<tr>
			<th>
				<?php echo JHTML::_('grid.sort', 'Num', 'invoicing_tax_id', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
			</th>
			<th></th>
			<th width="100%">
				<?php echo JHTML::_('grid.sort', 'INVOICING_TAX_TAXRATE', 'taxrate', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
			</th>
			<th width="8%">
				<?php echo JHTML::_('grid.sort', 'INVOICING_COMMON_STATUS', 'enabled', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
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
		<?php foreach ($this->items as $tax) : ?>
		<?php
			$i++; $m = 1-$m;
			//$checkedOut = ($tax->locked_by != 0);
			$checkedOut = false;
			$ordering = $this->lists->order == 'ordering';
			$tax->published = $tax->enabled;
		?>
		<tr class="<?php echo  'row'.$m; ?>">
			<td>
				<?php echo $tax->invoicing_tax_id; ?>
			</td>
			<td>
				<?php echo JHTML::_('grid.id', $i, $tax->invoicing_tax_id, $checkedOut); ?>
			</td>
			<td>
			<a href="index.php?option=com_invoicing&view=taxes&task=edit&cid=<?php echo $tax->invoicing_tax_id; ?>">
					<strong><?php echo  $this->escape($tax->taxrate) ?></strong>
			</td>
			<td>
				<?php 
					$options = [
						'disabled' => false,
						'id' => $tax->invoicing_tax_id
					];

					echo (new PublishedButton)->render((int) $tax->enabled, $i, $options); 
				?>
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