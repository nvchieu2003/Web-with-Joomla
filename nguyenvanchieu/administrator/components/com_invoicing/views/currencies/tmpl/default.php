<?php
/**
 *  @package Invoicing
 *  @copyright Copyright (c)203 Juloa.com
 *  @license GNU General Public License version 3, or later
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

$hasAjaxOrderingSupport = $this->hasAjaxOrderingSupport();

//FOFTemplateUtils::addCSS('media://com_invoicing/css/fits.css');
?>
<div class="row-fluid">
<div class="span12">

<form action="index.php" method="post" name="adminForm" id="adminForm">
<input type="hidden" name="option" value="com_invoicing" />
<input type="hidden" name="view" value="currencies" />
<input type="hidden" id="task" name="task" value="browse" />
<input type="hidden" name="hidemainmenu" id="hidemainmenu" value="0" />
<input type="hidden" name="boxchecked" id="boxchecked" value="0" />
<input type="hidden" name="filter_order" id="filter_order" value="<?php echo $this->lists->order ?>" />
<input type="hidden" name="filter_order_Dir" id="filter_order_Dir" value="<?php echo $this->lists->order_Dir ?>" />
<?php echo JHTML::_( 'form.token' ); ?>

<button class="hideToAdjust"></button>

<div class="filter-select fltrt">
<?php
 echo InvoicingHelperSelect::enabled($this->filters['enabled'], 'enabled', array('onchange'=>'this.form.submit();', 'class'=>'form-select'));?>
</div>

<table class="adminlist table table-striped" id="itemsList">
	<thead>
		<tr>
			<th width="3%">
				<?php echo JHTML::_('grid.sort', 'Num', 'invoicing_currency_id', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
			</th>
			<th width="3%"></th>
			<th>
				<?php echo JHTML::_('grid.sort', 'INVOICING_CURRENCY_SYMBOL', 'symbol', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
			</th>
			<th width="8%">
				<?php echo JHTML::_('grid.sort', 'INVOICING_CURRENCY_CODE', 'code', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
			</th>
			<th>
			<?php echo \JText::_('INVOICING_CURRENCY_SYMBOL_POSITION');?>
			</th>
            <th>
			<?php echo \JText::_('INVOICING_CURRENCY_NUMBER_DECIMALS');?>
			</th>
			<th>
			<?php echo \JText::_('INVOICING_CURRENCY_DECIMAL_SEPARATOR');?>
			</th>
			<th>
			<?php echo \JText::_('INVOICING_CURRENCY_THOUSAND_SEPARATOR');?>
			</th>
			<th width="8%">
			<?php echo \JText::_('JENABLED');?>
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
		<?php foreach ($this->items as $currency) : ?>
		<?php
			$i++; $m = 1-$m;
			//$checkedOut = ($currency->locked_by != 0);
			$checkedOut = false;
			$ordering = $this->lists->order == 'ordering';
			$currency->published = $currency->enabled;
		?>
		<tr class="<?php echo  'row'.$m; ?>">
			<td>
				<?php echo $currency->invoicing_currency_id; ?>
			</td>
			<td>
				<?php echo JHTML::_('grid.id', $i, $currency->invoicing_currency_id, $checkedOut); ?>
			</td>
			<td>
				<a href="index.php?option=com_invoicing&view=currencies&task=edit&cid=<?php echo $currency->invoicing_currency_id; ?>">
					<strong><?php echo  $this->escape($currency->symbol) ?></strong>
				</a>
			</td>
			<td>
					<strong><?php echo  $this->escape($currency->code) ?></strong>
			</td>
			</td>
			<td>
			<?php echo $currency->symbol_position; ?>
			</td>
            <td>
			<?php echo $currency->number_decimals; ?>
			</td>
			<td>
			<?php echo $currency->decimal_separator; ?>
			</td>
			<td>
			<?php echo $currency->thousand_separator; ?>
			</td>
			<td>
				<?php 
					$options = [
						'disabled' => false,
						'id' => $currency->invoicing_currency_id
					];

					echo (new PublishedButton)->render((int) $currency->enabled, $i, $options); 
				?>
			</td>	
		</tr>
		<?php endforeach; ?>	
		<?php else: ?>
		<tr>
			<td colspan="20">
				<?php echo  \JText::_('COM_INVOICING_COMMON_NORECORDS') ?>
		<?php endif; ?>
	</tbody>
</table>

</form>
	
</div>
</div>