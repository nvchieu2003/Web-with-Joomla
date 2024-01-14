<?php
/**
 * @package		Invoicing
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license GNU General Public License version 3, or later
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/format.php');

//Ugly hack
//TODO fix that sh*t t avoid doing a request in the view

$user = \JFactory::getUser();
$db = \JFactory::getDbo();
$db->setQuery("SELECT * 
							 FROM #__invoicing_invoices i
							 INNER JOIN #__invoicing_users u
							 ON u.invoicing_user_id = i.user_id
							 WHERE u.user_id = ".(int)$user->id." 
							 ORDER BY created_on DESC");
$items = $db->loadObjectList();

?>
<div class="row-fluid">
    <div class="span12">
    
            <h2><?php echo \JText::_('INVOICING_INVOICES')?></h2>
<table class="adminlist table table-striped mt-4">
<tr>
	<th><?php echo \JText::_('INVOICING_INVOICE_ID')?></th>
	<th><?php echo \JText::_('INVOICING_INVOICE_DATE')?></th>
	<th><?php echo \JText::_('INVOICING_INVOICE_STATUS')?></th>
	<th><?php echo \JText::_('INVOICING_INVOICE_NET_AMOUNT')?></th>
	<th><?php echo \JText::_('INVOICING_INVOICE_ACTIONS')?></th>
</tr>
<?php
foreach($items as $order) { ?>
<tr>
	<td><?php echo InvoicingHelperFormat::formatInvoiceNumber($order) ?></td>
	<td><?php echo date("d-m-Y",strtotime($order->created_on)) ?></td>
	<td>
	<?php echo InvoicingHelperFormat::formatInvoiceStatus($order->status) ?>
	</td>
	<td><?php echo InvoicingHelperFormat::formatPrice($order->net_amount,$order->currency_id); ?></td>
	<td>
		<?php if ($order->status != "CANCELLED") { /* ?>
		<a target='_blank' href="<?php echo \JRoute::_('index.php?option=com_invoicing&view=invoice&id='.$order->invoicing_invoice_id.'&tmpl=component')?>"></a>
		<?php } else { ?>
		<a href="<?php echo \JRoute::_('index.php?option=com_invoicing&view=invoice&id='.$order->invoicing_invoice_id.'&layout=payment')?>"><?php echo \JText::_('INVOICING_INVOICE_PAY')?></a>
		<?php } ?> */
		?>
		<div id="pic_actions">			
			<a id="see_pic_actions" class="modal me-2" rel="{handler: 'iframe', size: {x: 900, y: 600}}" href='<?php echo \JRoute::_("index.php?option=com_invoicing&view=invoice&id=".$order->invoicing_invoice_id."&tmpl=component") ?>'>
				<span class="icon icon-eye"></span>
			</a>
			
			<a id="pdf_pic_actions" class="me-2" href='<?php echo \JRoute::_("index.php?option=com_invoicing&view=invoice&id=".$order->invoicing_invoice_id."&tmpl=component&format=pdf") ?>'>
				<span class="icon icon-file"></span>
			</a>
			
			<?php if ($order->status == "PENDING" || $order->status == "NEW") { ?>
				<a id="pay_pic_actions" href='<?php echo \JRoute::_("index.php?option=com_invoicing&view=invoice&id=".$order->invoicing_invoice_id."&layout=payment&task=read") ?>'>
					<span class="icon icon-angle-right"></span>
				</a>
			<?php } ?>

		</div>
		<?php } ?>
	</td>

</tr>
<?php } ?>
            </table>

    </div>
</div>