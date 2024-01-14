<?php 
defined('_JEXEC') or die();
/**
 * @package		Invoicing
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license GNU General Public License version 3, or later
 */
?>
<form id="paymentForm" method='post' action='<?php echo $url; ?>' >
<!--  Additional fields can be added here as outlined in the SIM integration
 guide at: http://developer.authorize.net -->
	<input type='hidden' name='x_login' value='<?php echo $loginID; ?>' />
	<input type='hidden' name='x_amount' value='<?php echo $data->net_amount; ?>' />
	<input type='hidden' name='x_description' value='<?php echo $this->item_name;?>' />
	<input type='hidden' name='x_invoice_num' value='<?php echo $invoice; ?>' />
	<input type='hidden' name='x_fp_sequence' value='<?php echo $sequence; ?>' />
	<input type='hidden' name='x_fp_timestamp' value='<?php echo $timeStamp; ?>' />
	<input type='hidden' name='x_po_num' value='<?php echo $data->invoicing_invoice_id;?>' />
	<input type='hidden' name='x_relay_response' value='TRUE' />
	<input type='hidden' name='x_relay_url' value='<?php echo $this->baseurl; ?>/index.php?option=com_invoicing&view=payment&task=process&method=authorizenet' />
	<input type='hidden' name='x_fp_hash' value='<?php echo $fingerprint; ?>' />
	<input type='hidden' name='x_test_request' value='<?php echo $testMode; ?>' />
	<input type='hidden' name='x_tax' value='<?php echo $data->tax_amount; ?>' />
	<input type='hidden' name='x_show_form' value='PAYMENT_FORM' />
</form>

<?php echo \JText::_('Please Wait, you are going to be redirected to Authorize.net ...'); ?>