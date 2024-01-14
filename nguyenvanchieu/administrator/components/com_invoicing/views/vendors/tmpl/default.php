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

include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/select.php');

//$this->loadHelper('Select');
//$this->loadHelper('Cparams');
//$this->loadHelper('Format');

?>
<div class="row-fluid">
<div class="span12">

<form action="index.php" method="post" name="adminForm" id="adminForm">
<input type="hidden" name="option" value="com_invoicing" />
<input type="hidden" name="view" value="vendors" />
<input type="hidden" id="task" name="task" value="browse" />
<input type="hidden" name="hidemainmenu" id="hidemainmenu" value="0" />
<input type="hidden" name="boxchecked" id="boxchecked" value="0" />
<input type="hidden" name="filter_order" id="filter_order" value="<?php echo $this->lists->order ?>" />
<input type="hidden" name="filter_order_Dir" id="filter_order_Dir" value="<?php echo $this->lists->order_Dir ?>" />
<?php echo JHTML::_( 'form.token' ); ?>


<div class="row adminForm-search-bar">
	<div class="col">
	<input type="text" name="search" id="contact_name"
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
	<button class="btn btn-mini btn-outline-success" onclick="document.adminForm.contact_name.value='';this.form.submit();">
		<span class="icon-times"></span>
		<?php echo \JText::_('JSEARCH_RESET'); ?>
	</button>
	</div>
</div>

			
<table class="adminlist table table-striped table-long" id="itemsList">
	<thead>
		<tr>
			<th>
				<?php echo JHTML::_('grid.sort', 'Num', 'invoicing_vendor_id', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
			</th>
			<th class="small-th"></th>
			<th width="20%">
				<?php echo JHTML::_('grid.sort', 'INVOICING_VENDOR_CONTACT_NAME', 'contact_name', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
			</th>
			<th>
				<?php echo JHTML::_('grid.sort', 'INVOICING_VENDOR_LOGO', 'logo', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
			</th>
			<th width="100%">
				<?php echo JHTML::_('grid.sort', 'INVOICING_VENDOR_COMPANY_NAME', 'company_name', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
			</th>
			<th width="8%">
				<?php echo JHTML::_('grid.sort', 'INVOICING_COMMON_URL', 'company_url', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
			</th>	
			<th width="8%">
				<?php echo \JText::_('INVOICING_COMMON_PHONE');?>
			</th>
			<th width="8%">
				<?php echo \JText::_('INVOICING_COMMON_MAIL');?>
			</th>
			<th width="8%">
				<?php echo \JText::_('INVOICING_COMMON_CITY');?>
			</th>
			<th width="8%">
				<?php echo \JText::_('INVOICING_COMMON_STATE');?>
			</th>
			<th width="8%">
				<?php echo \JText::_('INVOICING_COMMON_ZIP');?>
			</th>
			<th width="8%">
				<?php echo \JText::_('INVOICING_COMMON_COUNTRY');?>
			</th>
		</tr>
	</thead>
	<tbody>
		<?php if($count = count($this->items)): ?>
		<?php $i = -1; $m = 0; ?>
		<?php foreach ($this->items as $vendor) : ?>
		<?php
			$i++; $m = 1-$m;
			//$checkedOut = ($vendor->locked_by != 0);
			$checkedOut = false;
			$ordering = $this->lists->order == 'ordering';
		?>
		<tr class="<?php echo  'row'.$m; ?>">
			<td>
				<?php echo $vendor->invoicing_vendor_id; ?>
			</td>
			<td class="small-td">
				<?php echo JHTML::_('grid.id', $i, $vendor->invoicing_vendor_id, $checkedOut); ?>
			</td>
			<td>
				<a href="index.php?option=com_invoicing&view=vendors&task=edit&cid=<?php echo $vendor->invoicing_vendor_id; ?>">
					<strong><?php if ($this->escape($vendor->contact_name) === "") 
										echo '&mdash;&mdash;&mdash;';
								  else
										echo $this->escape($vendor->contact_name);?>
					</strong>
				</a>
			</td>
			<td>
					<strong><?php echo $this->escape($vendor->filename) ?></strong>
					<img class="logo_vendor" alt="logo" src="<?php echo JURI::root()."/media/com_invoicing/images/vendor/".$this->escape($vendor->filename); ?>">
					
			</td>
			<td>
				<a href="index.php?option=com_invoicing&view=vendor&cid=<?php echo $vendor->invoicing_vendor_id; ?>">
					<strong><?php echo  $this->escape($vendor->company_name) ?></strong>
				</a>
			</td>
			<td>
				<a href="<?php echo $this->escape($vendor->company_url)?>">
				<?php echo  $this->escape($vendor->company_url) ?></a>
			</td>
			<td>
				<?php echo  $this->escape($vendor->company_phone) ?>
			</td>
			<td>
					<a href="mailto:<?php echo $this->escape($vendor->company_email)?>">
				<?php echo  $this->escape($vendor->company_email) ?></a>
			</td>
			<td>
				<?php echo  $this->escape($vendor->city) ?>
			</td>
			<td>
				<?php echo  $this->escape($vendor->state) ?>
			</td>
			<td>
				<?php echo  $this->escape($vendor->zip) ?>
			</td>
			<td>
				<?php echo InvoicingHelperSelect::formatCountry($vendor->country) ?>
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
	<tfoot>
		<tr>
			<td colspan="20">
				<?php if($this->pagination->total > 0) echo $this->pagination->getListFooter() ?>
			</td>
		</tr>
	</tfoot>
</table>
</form>
	
</div>
</div>