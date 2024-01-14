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

$input = \JFactory::getApplication()->input;


$content = '';

ob_start();

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
<input type="hidden" name="tmpl" value="component" />
<input type="hidden" name="field" id="field" value="<?php echo $input->get('field', "", "String")?>" />
<input type="hidden" name="layout" value="modal" />
<input type="hidden" name="hidemainmenu" id="hidemainmenu" value="0" />
<input type="hidden" name="boxchecked" id="boxchecked" value="0" />
<input type="hidden" name="filter_order" id="filter_order" value="<?php echo $this->lists->order ?>" />
<input type="hidden" name="filter_order_Dir" id="filter_order_Dir" value="<?php echo $this->lists->order_Dir ?>" />
<?php echo JHTML::_( 'form.token' ); ?>

<table class="adminlist table table-striped" id="itemsList">
	<thead>
		<tr>
			<th>
				<?php echo JHTML::_('grid.sort', 'Num', 'invoicing_vendor_id', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
			</th>
			<th></th>
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
			<td>
				<?php echo JHTML::_('grid.id', $i, $vendor->invoicing_vendor_id, $checkedOut); ?>
			</td>
			<td>
				<a href="javascript:selectItem(<?php echo $vendor->invoicing_vendor_id; ?>)">
					<strong><?php if ($this->escape($vendor->contact_name) === "") 
										echo '&mdash;&mdash;&mdash;';
								  else
										echo $this->escape($vendor->contact_name);?>
					</strong>
				</a>
			</td>
			<td>
			<a href="javascript:selectItem(<?php echo $vendor->invoicing_vendor_id; ?>)">
					<strong><?php echo $this->escape($vendor->filename) ?></strong>
					
					<img class="logo_vendor" alt="logo" src="<?php echo JURI::root()."/media/com_invoicing/images/vendor/".$this->escape($vendor->filename); ?>">
				</a>a>	
			</td>
			<td>
				<a href="javascript:selectItem(<?php echo $vendor->invoicing_vendor_id; ?>)">
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
</table>
</form>
<?php
	$content = ob_get_contents();
	ob_end_clean();

	echo HTMLHelper::_(
		'bootstrap.renderModal',
		'modal-invoice', // selector
		array( // options
			'modal-dialog-scrollable' => true,
			'title'  => \JText::_('INVOICING_FILELIST_TEXT'),
			'footer' => '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">'.\JText::_('INVOICING_CLOSE').'</button>',
		),
			'<div id="modal-body">'.$content.'</div>'
	);

	$script = "
	function selectItem(id) {
		parentfield = '".$input->get('field', "", "String")."';
		jQ('#'+parentfield, window.parent.document).val(id);
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
