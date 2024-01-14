<?php defined('_JEXEC') or die(); ?>
<?php
$t1 = \JText::_('COM_AKEEBASUBS_LEVEL_REDIRECTING_HEADER');
$t2 = \JText::_('COM_AKEEBASUBS_LEVEL_REDIRECTING_BODY');
?>
<form action="<?php echo $url ?>"  method="post" id="paymentForm" name="paymentForm">
	
	<input type="hidden" name="merchantnumber" value="<?php echo $dataform->merchant ?>" />
	<input type="hidden" name="accepturl" value="<?php echo $dataform->success ?>" />
	<input type="hidden" name="cancelurl" value="<?php echo $dataform->cancel ?>" />
	<input type="hidden" name="callbackurl" value="<?php echo $dataform->postback ?>" />
	<input type="hidden" name="orderid" value="<?php echo $dataform->orderid ?>" />
	
	<?php /** @see http://tech.epay.dk/Currency-codes_60.html */ ?>
	<input type="hidden" name="currency" value="<?php echo $dataform->currency ?>" />
	<input type="hidden" name="amount" value="<?php echo $dataform->amount ?>" />
	
	
	<?php /** @see http://tech.epay.dk/Specification_85.html#paymenttype */ ?>
	<input type="hidden" name="paymenttype" value="<?php echo $dataform->cardtypes ?>" />
	<input type="hidden" name="instantcapture" value="<?php echo $dataform->instantcapture ?>" />
	<input type="hidden" name="instantcallback" value="<?php echo $dataform->instantcallback ?>" />
	
	<input type="hidden" name="language" value="<?php echo $dataform->language ?>" />
	<input type="hidden" name="ordertext" value="<?php echo $dataform->ordertext ?>" />
	
	<input type="hidden" name="windowstate" value="<?php echo $dataform->windowstate ?>" />
	<input type="hidden" name="ownreceipt" value="<?php echo $dataform->ownreceipt ?>" />
	<input type="hidden" name="hash" value="<?php echo $dataform->md5 ?>" />
	<!--  -->
	<input type="image" src="http://tech.epay.dk/kb_upload/image/epay_logos/uk.gif" border="0" name="submit" alt="Epay Payments" id="epaydksubmit" />
</form>

<?php echo \JText::_('PLG_INVOICINGPAYMENT_EPAYDK_PLEASE_WAIT_UNTIL_REDIRECTION'); ?>