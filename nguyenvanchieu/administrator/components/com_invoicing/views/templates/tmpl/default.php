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

//$this->loadHelper('Select');
//$this->loadHelper('Cparams');
//$this->loadHelper('Format');

$hasAjaxOrderingSupport = $this->hasAjaxOrderingSupport();
?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
<input type="hidden" name="option" value="com_invoicing" />
<input type="hidden" name="view" value="templates" />
<input type="hidden" id="task" name="task" value="browse" />
<input type="hidden" name="hidemainmenu" id="hidemainmenu" value="0" />
<input type="hidden" name="boxchecked" id="boxchecked" value="0" />
<input type="hidden" name="filter_order" id="filter_order" value="<?php echo $this->lists->order ?>" />
<input type="hidden" name="filter_order_Dir" id="filter_order_Dir" value="<?php echo $this->lists->order_Dir ?>" />
<?php echo JHTML::_( 'form.token' ); ?>

<table class="adminlist table table-striped" id="itemsList">
	<thead>
		<tr>
			<th>
				<?php echo JHTML::_('grid.sort', 'Num', 'invoicing_template_id', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
			</th>
			<th></th>
			<th width="80%">
				<?php echo JHTML::_('grid.sort', 'INVOICING_TEMPLATE_DESCRIPTION', 'description', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
			</th>
			<th width="80%">
				<?php echo JHTML::_('grid.sort', 'INVOICING_TEMPLATE_USEHTMLFORPDF', 'usehtmlforpdf', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
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
		<?php foreach ($this->items as $template) : ?>
		<?php
			$i++; $m = 1-$m;
			//$checkedOut = ($coupon->locked_by != 0);
			$checkedOut = false;
			$ordering = $this->lists->order == 'ordering';
		?>
		<tr class="<?php echo  'row'.$m; ?>">
			<td>
				<?php echo $template->invoicing_template_id; ?>
			</td>
			<td>
				<?php echo JHTML::_('grid.id', $i, $template->invoicing_template_id, $checkedOut); ?>
			</td>
			<td>
				<a href="index.php?option=com_invoicing&view=templates&task=edit&cid=<?php echo $template->invoicing_template_id; ?>">
					<strong><?php echo  $this->escape(\JText::_($template->description)) ?></strong>
				</a>
			</td>	
			<td>
				<?php if($template->usehtmlforpdf)
						echo \JText::_('JYES');
					  else
						echo \JText::_('JNO');
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