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
?>
<div class="row-fluid">
<div class="span12">

<form action="index.php" method="post" name="adminForm" id="adminForm">
<input type="hidden" name="option" value="com_invoicing" />
<input type="hidden" name="view" value="coupons" />
<input type="hidden" id="task" name="task" value="browse" />
<input type="hidden" name="hidemainmenu" id="hidemainmenu" value="0" />
<input type="hidden" name="boxchecked" id="boxchecked" value="0" />
<input type="hidden" name="filter_order" id="filter_order" value="<?php echo $this->lists->order ?>" />
<input type="hidden" name="filter_order_Dir" id="filter_order_Dir" value="<?php echo $this->lists->order_Dir ?>" />
<?php echo JHTML::_( 'form.token' ); ?>

<div class="row adminForm-search-bar">
	<div class="col">
		<input type="text" name="search" id="search"
		value="<?php echo $this->escape($this->filters['search']);?>"
		class="form-control" onchange="document.adminForm.submit();"
		placeholder="<?php echo \JText::_('COM_INVOICING_COMMON_SEARCH')?>"
		/>
	</div>

	<div class="col col-auto">
		<button class="btn btn-mini btn-outline-primary" onclick="this.form.submit();">
			<span class="icon-search"></span>
			<?php echo \JText::_('JSEARCH_FILTER'); ?>
		</button>
	</div>
	<div class="col col-auto">
		<button class="btn btn-mini btn-outline-success" onclick="document.adminForm.search.value='';this.form.submit();">
		<span class="icon-times"></span>
		<?php echo \JText::_('JSEARCH_RESET'); ?>
		</button>
	</div>
</div>

<div class="filter-select fltrt">
<?php echo InvoicingHelperSelect::published($this->filters['enabled'], 'enabled', array('onchange'=>'this.form.submit();', 'class'=>'form-select')) ?>
</div>

<table class="adminlist table table-striped" id="itemsList">
	<thead>
		<tr>
			<th>
				<?php echo JHTML::_('grid.sort', 'Num', 'invoicing_coupon_id', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
			</th>
			<th></th>
			<th width="20%">
				<?php echo JHTML::_('grid.sort', 'INVOICING_COUPON_TITLE', 'invoicing_coupon_id', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
			</th>
			<th width="20%">
				<?php echo JHTML::_('grid.sort', 'INVOICING_COUPON_CODE', 'code', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
			</th>
			<th>
				<?php echo \JText::_('INVOICING_COUPON_VALUETYPE') ?>
			</th>
			<th>
				<?php echo \JText::_('INVOICING_COUPON_VALUE') ?>
			</th>
			
			<!-- <th>
				<?php echo \JText::_('INVOICING_COUPON_HITSLIMIT') ?>
			</th>
			<th>
				<?php echo \JText::_('INVOICING_COUPON_HITS') ?>
			</th>
			<th>
				<?php echo \JText::_('INVOICING_COUPON_USERHITSLIMIT') ?>
			</th>-->
			<th width="8%">
				<?php echo JHTML::_('grid.sort', 'JPUBLISHED', 'enabled', $this->lists->order_Dir, $this->lists->order, 'browse'); ?>
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
		<?php foreach ($this->items as $coupon) : ?>
		<?php
			$i++; $m = 1-$m;
			//$checkedOut = ($coupon->locked_by != 0);
			$checkedOut = false;
			$ordering = $this->lists->order == 'ordering';
			$coupon->published = $coupon->enabled;
		?>
		<tr class="<?php echo  'row'.$m; ?>">
			<td>
				<?php echo $coupon->invoicing_coupon_id; ?>
			</td>
			<td>
				<?php echo JHTML::_('grid.id', $i, $coupon->invoicing_coupon_id, $checkedOut); ?>
			</td>
			<td>
				<a href="index.php?option=com_invoicing&view=coupons&task=edit&cid=<?php echo $coupon->invoicing_coupon_id; ?>">
					<strong><?php echo  $this->escape($coupon->title) ?></strong>
				</a>
			</td>
			<td>
					<strong><?php echo  $this->escape($coupon->code) ?></strong>
			</td>
			<td>
				<?php echo  $this->escape($coupon->valuetype) ?>
			</td>
			<td>
				<?php echo  $this->escape($coupon->value) ?>
			</td>
			<!-- <td>
				<?php echo  $this->escape($coupon->hitslimit) ?>
			</td>
			<td>
				<?php echo  $this->escape($coupon->hits) ?>
			</td>
			<td>
				<?php echo  $this->escape($coupon->userhitslimit) ?> 
			</td>-->
			<td>
				<?php 
					$options = [
						'disabled' => false,
						'id' => $coupon->invoicing_coupon_id
					];

					echo (new PublishedButton)->render((int) $coupon->enabled, $i, $options); 
				?>
			</td>	
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