<?php
/**
 * @package		Invoicing
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license GNU General Public License version 3, or later
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

?>
<?php 
if (@$this->config->sandbox == 1)
{
	?>
	<form id="paymentForm" name="paymentForm" action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post">
	<input type="hidden" name="business" value="<?php echo $this->config->sandbox_merchant; ?>" />
	<?php
} 
else
{
	?>
	<form id="paymentForm" name="paymentForm" action="https://www.paypal.com/cgi-bin/webscr" method="post">
	<input type="hidden" name="business" value="<?php echo @$this->config->merchant; ?>" />
	<?php
}
?>
<input type="hidden" name="amount" value="<?php echo round($this->amount,2);?>" />
<input type="hidden" name="item_number" value="<?php echo $this->item_number;?>" />
<input type="hidden" name="item_name" value="<?php echo $this->item_name;?>" />
<input type="hidden" name="cmd" value="_xclick" />
<input type="hidden" name="currency_code" value="<?php echo $this->currency_code; ?>" />
<input type="hidden" name="notify_url" value="<?php echo $this->baseurl; ?>index.php?option=com_invoicing&view=payment&task=process&method=paypal" />
<input type="hidden" name="return" value="<?php echo $this->baseurl; ?>index.php?option=com_invoicing&view=message&layout=complete" />
<input type="hidden" name="cancel_return" value="<?php echo $this->baseurl; ?>index.php?option=com_invoicing&view=message&layout=cancel" />
<input type="hidden" name="tax" value="<?php echo 0;/*$this->tax*/ ?>" />
<input type="hidden" name="no_note" value="1" />
<input type="hidden" name="no_shipping" value="1" />
<input type="hidden" name="charset" value="utf-8">
</form>

<?php echo \JText::_('PLG_INVOICINGPAYMENT_PAYPAL_PLEASE_WAIT_UNTIL_REDIRECTION'); ?>