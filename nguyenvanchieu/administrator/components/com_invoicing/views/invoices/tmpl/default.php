<?php
/**
 *  @package Invoicing
 *  @copyright Copyright (c)203 Juloa.com
 *  @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/select.php');

/*//JHtml::_('behavior.tooltip');
//JHtml::_('behavior.modal');

if(version_compare(JVERSION, '3.0', 'ge')) {
	JHTML::_('jquery.framework');
} else {
	JHTML::_('behavior.mootools');
}*/

JHtml::_('formbehavior.chosen', 'select', null, array('disable_search_threshold'=>1));


?>
<div class="row-fluid">
<div class="span12">

<form action="index.php" method="post" name="adminForm" id="adminForm">
<input type="hidden" name="option" value="com_invoicing" />
<input type="hidden" name="view" value="invoices" />
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

	<div class="col col-auto">
		<a class="btn btn-success" href="<?php echo \JRoute::_('index.php?option=com_invoicing&view=invoices&format=csv')?>">
			<span class="icon-download"></span>
			<?php echo \JText::_('INVOICING_DOWNLOAD_CSV') ?>
		</a>
	</div>
</div>

<div class="filter-select fltrt">
<?php echo JHTML::_('calendar',$this->escape($this->filters['dateFilterFrom']), "dateFilterFrom", "dateFilterFrom", "%Y-%m-%d");?>
	<?php echo JHTML::_('calendar',$this->escape($this->filters['dateFilterTo']), "dateFilterTo", "dateFilterTo", "%Y-%m-%d");?>
	<button class="btn btn-mini btn-primary" onclick="document.adminForm.submit();">
		<span class="icon-filter"></span>
		<?php echo \JText::_('INVOICING_FILTER'); ?>
	</button>
<?php echo InvoicingHelperSelect::coupons('coupon_id',$this->filters['coupon_id'], array('include_all'=>true,'onchange'=>'this.form.submit();', 'class'=>'form-select')) ?>
<?php echo InvoicingHelperSelect::processors($this->filters['processor'],'processor', array('onchange'=>'this.form.submit();', 'class'=>'form-select')) ?>
<?php echo InvoicingHelperSelect::invoicestatus($this->filters['status'], 'status', array('include_all'=>true,'onchange'=>'this.form.submit();', 'class'=>'form-select')) ?>
<?php echo InvoicingHelperSelect::vendors('vendor_id',$this->filters['vendor_id'], array('onchange'=>'this.form.submit();', 'class'=>'form-select')) ?>
<?php echo InvoicingHelperSelect::users('user_id',$this->filters['user_id'], array('onchange'=>'this.form.submit();', 'class'=>'form-select')) ?>
</div>

<table class="adminlist table table-striped table-long" id="itemsList">
	<thead>
		<tr>
			<th>
				<?php echo JHTML::_('grid.sort', 'Num', 'invoicing_invoice_id', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
			</th>
			<th class="small-th"></th>
			<th>
				<?php echo JHTML::_('grid.sort', 'COM_INVOICING_TITLE_INVOICES', 'invoice_number', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
			</th>
			<th>
				<?php echo JHTML::_('grid.sort', 'COM_INVOICING_TITLE_ORDERS', 'order_number', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
			</th>
			<th>
				<?php echo \JText::_('INVOICING_COMMON_INVOICE_SUBJECT');?>
			</th>	
			<th>
				<?php echo JHTML::_('grid.sort', 'COM_INVOICING_TITLE_USERS', 'user_id', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
			</th>
			<th>
				<?php echo JHTML::_('grid.sort', 'COM_INVOICING_TITLE_VENDORS', 'vendor_id', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
			</th>
			<th>
				<?php echo \JText::_('INVOICING_COMMON_COUPON');?>
			</th>	
			<th>
				<?php echo \JText::_('INVOICING_INVOICE_DISCOUNT_AMOUNT');?>
			</th>	
			<th>
				<?php echo \JText::_('INVOICING_INVOICE_NET_AMOUNT');?>
			</th>	
			<th>
				<?php echo \JText::_('INVOICING_INVOICE_TAX_AMOUNT');?>
			</th>	
			<th>
				<?php echo JHTML::_('grid.sort', 'INVOICING_INVOICE_GROSS_AMOUNT', 'gross_amount', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
			</th>	
			<th width="8%">
				<?php echo JHTML::_('grid.sort', 'INVOICING_COMMON_STATUS', 'status', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
			</th>
			<th width="8%">
				<?php echo JHTML::_('grid.sort', 'INVOICING_COMMON_INVOICE_PROCESSOR', 'processor', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
			</th>
			<th width="10%">
				<?php echo \JText::_('INVOICING_INVOICE_CREATED_ON');?>
			</th>		
			<th width ="10%">
				<?php echo \JText::_('INVOICING_INVOICE_ACTIONS');?>
			</th>	
		</tr>
	</thead>

	<tbody>
		<?php if($count = count($this->items)): ?>
		<?php $i = -1; $m = 0; ?>
		<?php foreach ($this->items as $invoice) : ?>
		<?php
			$i++; $m = 1-$m;
			//$checkedOut = ($invoice->locked_by != 0);
			$checkedOut = false;
			$ordering = $this->lists->order == 'ordering';
		?>
		<tr class="<?php echo  'row'.$m; ?>">
			<td>
				<?php echo $invoice->invoicing_invoice_id; ?>
			</td>
			<td class="small-td">
				<?php echo JHTML::_('grid.id', $i, $invoice->invoicing_invoice_id, $checkedOut); ?>
			</td>
			<td>
				<a href="index.php?option=com_invoicing&view=invoices&task=edit&cid=<?php echo $invoice->invoicing_invoice_id; ?>">
				<strong><?php echo $this->escape(InvoicingHelperFormat::formatInvoiceNumber($invoice)); ?></strong>
				</a>
			</td>
			<td>
				<a href="index.php?option=com_invoicing&view=invoices&task=edit&cid=<?php echo $invoice->invoicing_invoice_id; ?>">
				<strong><?php echo $this->escape(InvoicingHelperFormat::formatOrderNumber($invoice)); ?></strong>
				</a>
			</td>
			<td>
				<a href="index.php?option=com_invoicing&view=invoices&task=edit&cid=<?php echo $invoice->invoicing_invoice_id; ?>">
				<strong><?php echo $this->escape($invoice->subject); ?></strong>
				</a>
			</td>
			<td>
				<a href="index.php?option=com_invoicing&view=users&task=edit&cid=<?php echo $invoice->user_id; ?>">
					<?php echo InvoicingHelperFormat::formatUser($invoice->user_id,"businessname") ?>
				</a>
			</td>
			<td>
				<a href="index.php?option=com_invoicing&view=vendors&task=edit&cid=<?php echo $invoice->vendor_id; ?>">
					<?php echo InvoicingHelperFormat::formatVendor($invoice->vendor_id,"contact_name") ?>
				</a>
			</td>
			<td>
				<?php echo InvoicingHelperFormat::formatCoupon($invoice->coupon_id); ?>
			</td>
			<td>
				<?php echo InvoicingHelperFormat::formatPrice($invoice->net_discount_amount,$invoice->currency_id); ?>
			</td>
			<td>
				<?php echo InvoicingHelperFormat::formatPrice($invoice->net_amount,$invoice->currency_id); ?>
			</td>			
			<td>
				<?php echo InvoicingHelperFormat::formatPrice($invoice->tax_amount,$invoice->currency_id); ?>
			</td>
			<td>
					<strong><?php echo  InvoicingHelperFormat::formatPrice($invoice->gross_amount,$invoice->currency_id) ?></strong>
			</td>
			<td><?php echo InvoicingHelperFormat::formatInvoiceStatus($invoice->status)?></td>
			<td><?php echo InvoicingHelperFormat::formatProcessor($invoice->processor)?></td>
			<td><?php 
				echo InvoicingHelperFormat::formattedDate($invoice->created_on);
			?></td>
			<td>
				<div id="pic_actions">
				
				<a id="see_pic_actions" class="modal" rel="{handler: 'iframe', size: {x: 900, y: 600}}" href="index.php?option=com_invoicing&view=invoices&task=read&cid=<?php echo $this->escape($invoice->invoicing_invoice_id) ?>&tmpl=component&print=1">
			<span class="icon icon-eye"></span>
			</a>
				<a id="pdf_pic_actions" href="index.php?option=com_invoicing&view=invoices&cid=<?php echo $this->escape($invoice->invoicing_invoice_id) ?>&task=read&tmpl=component&format=pdf">
			<span class="icon icon-file"></span>
			</a>
				<a id="send_pic_actions" href="index.php?option=com_invoicing&view=invoices&cid=<?php echo $this->escape($invoice->invoicing_invoice_id)?>&task=mail">
				<span class="icon icon-envelope"></span>
			</a>
				<!-- <a id="pdf_pic_actions" href="#"></a>-->
				</div>
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
