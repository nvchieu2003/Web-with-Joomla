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

include_once (JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/select.php');

//$this->loadHelper('Select');
//$this->loadHelper('Cparams');
//$this->loadHelper('Format');
?>
<div class="row-fluid">
<div class="span12">

<form action="index.php" method="post" name="adminForm" id="adminForm">
<input type="hidden" name="option" value="com_invoicing" />
<input type="hidden" name="view" value="users" />
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
	class="form-select " onchange="document.adminForm.submit();"
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
<input class="form-control" type="text" placeholder="<?php echo htmlspecialchars(\JText::_('INVOICING_COMMON_CITY')) ?>" value="<?php echo htmlspecialchars($this->filters['city']) ?>" name="city" onchange="this.form.submit();" />
<input class="form-control" type="text" placeholder="<?php echo htmlspecialchars(\JText::_('INVOICING_COMMON_ZIP')) ?>" value="<?php echo htmlspecialchars($this->filters['zip']) ?>" name="zip" onchange="this.form.submit();" />
<?php echo InvoicingHelperSelect::countries($this->filters['country'],'country', array('onchange'=>'this.form.submit();', 'class'=>'form-select')) ?>
</div>
<div style="clear:both"></div>

<table class="adminlist table table-striped" id="itemsList">
	<thead>
		<tr>
			<th width="8%">
				<?php echo JHTML::_('grid.sort', 'Num', 'invoicing_user_id', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
			</th>
			<th></th>
			<th width="40%">
				<?php echo JHTML::_('grid.sort', 'INVOICING_USER_BUSINESSNAME', 'businessname', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
			</th>
			<th width="40%">
				<?php echo JHTML::_('grid.sort', 'INVOICING_USER_NAME', 'name', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
			</th>
			<?php /*<th>
				<?php echo JHTML::_('grid.sort', 'INVOICING_USER_ISBUSINESS', 'isbusiness', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
			</th>
			<th width="20%">
				<?php echo JHTML::_('grid.sort', 'INVOICING_USER_OCCUPATION', 'occupation', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
			</th>
			
			<!--<th width="8%">
				<?php echo JHTML::_('grid.sort', 'INVOICING_USER_VATNUMBER', 'vatnumber', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
			</th>
			<th>
				<?php echo JHTML::_('grid.sort', 'INVOICING_USER_VIESREGISTERED', 'enabled', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
			</th>-->
			*/?>
			<th width="10%">
				<?php echo JHTML::_('grid.sort', 'INVOICING_COMMON_CITY', 'city', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
			</th>
			<?php /*<th width="10%">
				<?php echo JHTML::_('grid.sort', 'INVOICING_COMMON_STATE', 'state', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
			</th>
			*/?>
			<th width="5%">
				<?php echo JHTML::_('grid.sort', 'INVOICING_COMMON_ZIP', 'zip', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
			</th>	
			<th width="10%">
				<?php echo JHTML::_('grid.sort', 'INVOICING_COMMON_COUNTRY', 'country', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
			</th>		
			<th width="10%">
				<?php echo JHTML::_('grid.sort', 'INVOICING_COMMON_ACTIONS', '', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
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
		<?php foreach ($this->items as $user) : ?>
		<?php
			//$user->invoicing_user_id = $user->id;
			$i++; $m = 1-$m;
			//$checkedOut = ($user->locked_by != 0);
			$checkedOut = false;
			$ordering = $this->lists->order == 'ordering';
		?>
		<tr class="<?php echo  'row'.$m; ?>">
			<td>
				<?php echo $user->invoicing_user_id; ?>
			</td>
			<td>
				<?php echo JHTML::_('grid.id', $i, $user->invoicing_user_id, $checkedOut); ?>
			</td>
			<td>
				<a href="index.php?option=com_invoicing&view=users&task=edit&cid=<?php echo $user->invoicing_user_id; ?>">
					<strong>
					<?php 
					$name = $user->businessname;
					echo $this->escape($user->businessname);
					?>
					</strong>
				</a>
			</td>
			<td>
				<a href="index.php?option=com_invoicing&view=user&cid=<?php echo $user->invoicing_user_id; ?>">
					<strong>
					<?php 
					echo $this->escape($user->firstname." ".$user->lastname);
					?>
					</strong>
				</a>
			</td>
			<?php 
			/*<td>
				<?php echo InvoicingHelperFormat::formatYesNo($user->isbusiness) ?>
			</td>
			<td>
					<strong><?php echo  $this->escape($user->occupation) ?></strong>
			</td>
			<!--<td align="left">
					<strong><?php echo  $this->escape($user->vatnumber) ?></strong>
			</td>
			<td align="left">
					<strong><?php echo  $this->escape($user->viesregistered) ?></strong>
			</td>-->
			*/?>
			<td><strong><?php echo  $this->escape($user->city) ?></strong></td>
			<?php /*<td><strong><?php echo  $this->escape($user->state) ?></strong></td>*/?>
			<td><strong><?php echo  $this->escape($user->zip) ?></strong></td>
			<td>
					<?php echo InvoicingHelperSelect::formatCountry($user->country) ?>
			</td>
			<td>
					<a href="index.php?option=com_invoicing&view=invoices&user_id=<?php echo $this->escape($user->invoicing_user_id);?>">
					<?php echo \JText::_('INVOICING_INVOICES_LIST'); ?>
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
