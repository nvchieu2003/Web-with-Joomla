<?php
/**
 * @package		Invoicing
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license GNU General Public License version 3, or later
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/cparams.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/select.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/format.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/models/users.php');

//HACK
//OnBefore Read doesn't work, this is a workaround that need to be fixed
// Get the list of payment plugins
jimport('joomla.plugin.helper');
\JPluginHelper::importPlugin('invoicingpayment');
$app = \JFactory::getApplication();
$jResponse = $app->triggerEvent('onInvoicingPaymentGetIdentity');
$this->processors = $jResponse;
$app = \JFactory::getApplication();
$key = $app->getUserStateFromRequest("com_invoicing.invoice.key",'key',	0,'string');
$this->key = $key;

$input = \JFactory::getApplication()->input;
$couponstatus = $input->getCmd('couponstatus','');
$this->couponstatus = $couponstatus;

$invoiceId = $input->getInt('id', null);
//That line doesn't work, to finish the work we will do a sql request directly
//$invoice  = $this->getModel()->getItem($invoiceId);
//$this->loadHelper('Format');

//TODO find another way to do that
$db = \JFactory::getDbo();
$db->setQuery("SELECT * 
							 FROM #__invoicing_invoices i
							 WHERE i.invoicing_invoice_id = ".(int)$invoiceId);
$invoice = $db->loadObject();

$db->setQuery("SELECT * 
							 FROM #__invoicing_invoice_items ii
							 WHERE ii.invoice_id = ".(int)$invoiceId);
$invoice->items = $db->loadObjectList();

if ($invoice->invoice_number != null) {
    //$content = InvoicingHelperFormat::formatInvoiceHTML($invoice);
} else {
    //$content = InvoicingHelperFormat::formatOrderHTML($invoice);
}

$enableCoupon = InvoicingHelperCparams::getParam('enable_coupon', 1);

$this->enablecoupon = $enableCoupon;
//$this->content = $content;

$this->invoice = $invoice;

$user = \JFactory::getUser();

$model = InvoicingModelUsers::getInstance('Users', 'InvoicingModel');
$data = (array) $model->getItem($model->getInvoicingUser($user->id));
$this->user = $data;

$mandatoryfields = array('city','country','zip','address1','businessname');
$missing = false;
foreach($mandatoryfields as $field) {
    if (!isset($data[$field]) || $data[$field] == null) {
        $missing = true;
        break;
    }
}
    
// Show login page
$profileItemid = (int)InvoicingHelperCparams::getParam('itemid_profile',0);
//check if Itemid for the profile exist, if yes we add it to the url
if($profileItemid > 0) {
    $itemid = "&Itemid=".$profileItemid;
} else {
    $itemid = "";
}
$juri = \JURI::getInstance();
$myURI = base64_encode($juri->toString());
$editprofileurl = \JRoute::_('index.php?option=com_invoicing&view=user&payment=1&return='.$myURI.$itemid);

//If the profile redirect is enabled and if one mandatory field is missing
$enableRedirection = InvoicingHelperCparams::getParam('enable_mandatory_profile_redirect', 1);

if ($missing == true && $enableRedirection == 1) {
    //That need to be fixed
    //\JFactory::getApplication()->redirect($editprofileurl, 200);
}

$this->editprofileurl = $editprofileurl;

//End of HACK


?>
<div class="juloawrapper adsInvoice">
<div class="row">
    <div class="col col-12">
        <h3><?php echo \JText::_('INVOICING_USER_INFORMATION')?></h3>
	</div>
</div>
<div class="row">
	<div class="col col-6">
        <?php echo @htmlspecialchars($this->user['businessname']) ?><br/>
		<?php echo @htmlspecialchars($this->user['address1']) ?><br/>
		<?php if ($this->user['address2'] != "") { echo htmlspecialchars($this->user['address2']); } ?><br/>
		<?php echo @htmlspecialchars($this->user['zip']) ?> <?php echo @htmlspecialchars($this->user['city']) ?><br/>
		<?php echo InvoicingHelperSelect::formatCountry($this->user['country']) ?>
    </div>
	<div class="col col-6">
		<a class="btn btn-primary float-end" href="<?php echo $this->editprofileurl ?>">
            <span class="icon icon-edit"></span>
            <?php echo \JText::_('INVOICING_EDIT_USER_PROFILE')?>
        </a>
	</div>
</div>
<br/>
<div class="row">
    <div class="col col-12">
        <h3><?php echo \JText::_('INVOICING_COMMON_INVOICE')?></h3>
    </div>
</div>
<div class="row">
    <div class="col col-12">
        <table class="table paymentTable" border="0" cellpadding="5" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th align="left" width="50%" valign="top"><?php echo \JText::_('INVOICING_COMMON_DESCRIPTION');?></th>
                    <th align="left" width="10%" valign="top"><?php echo \JText::_('INVOICING_COMMON_QUANTITY');?></th>
                    <th align="left" width="10%" valign="top"><?php echo \JText::_('INVOICING_COMMON_TAX');?></th>
                    <th align="right" width="10%" valign="top" class="thprice"><?php echo \JText::_('INVOICING_COMMON_UNIT_PRICE');?></th>
                </tr>
            </thead>
            <tbody>
<?php foreach($this->invoice->items as $item) { ?>
                <tr>
                    <td align="left" width="50%" valign="top"><?php echo htmlspecialchars($item->name);if ($item->description != "") { echo "<br/>".htmlspecialchars($item->description); }?></td>
                    <td align="left" width="10%" valign="top"><?php echo $item->quantity ?></td>
                    <td align="left" width="10%" valign="top"><?php echo $item->tax ?></td>
                    <td align="right" width="10%" valign="top"><?php echo InvoicingHelperFormat::formatPrice($item->net_unit_price,$this->invoice->currency_id) ?></td>
                </tr>
<?php } ?> 
            </tbody>
        </table>
    </div>
</div>
<div class="row">
    <div class="col col-3 pull-right">
        <table class="table finalPriceTable" border="0" cellpadding="5" cellspacing="0" width="100%">
            <tbody>
                <tr>
                    <td align="right" width="70%" valign="top"><?php echo \JText::_('INVOICING_DISCOUNT_TTC') ?></td>
                    <td align="right" width="30%" valign="top"><?php echo InvoicingHelperFormat::formatPrice($this->invoice->gross_discount_amount,$this->invoice->currency_id) ?></td>
                </tr>
                <tr class="grey">
                    <td align="right" width="70%" valign="top"><?php echo \JText::_('INVOICING_COMMON_AMOUNT_WITHOUT_TAX') ?></td>
                    <td align="right" width="30%" valign="top"><?php echo InvoicingHelperFormat::formatPrice($this->invoice->net_amount,$this->invoice->currency_id) ?></td>
                </tr>
                <tr class="grey">
                    <td align="right" width="70%" valign="top"><?php echo \JText::_('INVOICING_COMMON_TAX') ?></td>
                    <td align="right" width="30%" valign="top"><?php echo InvoicingHelperFormat::formatPrice($this->invoice->tax_amount,$this->invoice->currency_id) ?></td>
                </tr>
                <tr class="finalPrice">
                    <td align="right" width="70%" valign="top"><?php echo \JText::_('INVOICING_COMMON_AMOUNT_WITH_TAX') ?></td>
                    <td align="right" width="30%" valign="top"><?php echo InvoicingHelperFormat::formatPrice($this->invoice->gross_amount,$this->invoice->currency_id) ?></td>
                </tr>	
            </tbody>
        </table>
    </div>
</div>
<?php if ($this->invoice->notes != "") { ?>
<div class="row">
    <div class="col col-12">
        <h3><?php echo \JText::_('INVOICING_INVOICE_NOTES')?></h3>
        <blockquote>
            <p><?php echo htmlspecialchars($this->invoice->notes) ?></p>
        </blockquote>
    </div>
</div>
<?php } ?>
<?php if($this->enablecoupon){ ?>
<?php if (@$this->couponstatus == "valid") {?>
<div class="alert alert-info"><?php echo sprintf(\JText::_('INVOICING_COUPON_VALID'),"") ?></div>
<br/><br/>
<?php } else if (@$this->couponstatus == "error") {?>
<div class="alert alert-danger"><?php echo sprintf(\JText::_('INVOICING_COUPON_ERROR'),"") ?></div>
<br/><br/>
<?php } ?>


<?php $target = \JRoute::_('index.php?option=com_invoicing&view=payment&task=checkcoupon'); ?>
<form action="<?php echo $target;?>" method="post" name="adminForm" id="adminForm">

<div class="mb-3 row">
    <div class="col d-flex align-items-center">
        <h3><?php echo \JText::_('INVOICING_ENTER_A_COUPON') ?></h3>
    </div>
    <div class="col d-flex align-items-center">
        <input class="form-control" type="text" value="" name="couponcode" />
        <input type="hidden" value="<?php echo $this->invoice->invoicing_invoice_id?>" name="id" />
    </div>
    <div class="col col-auto d-flex align-items-center">
        <input type="submit" class="button btn btn-primary" value="<?php echo \JText::_('INVOICING_VALID_COUPON') ?>"/>
    </div>
</div>

</form>

<br/>
<?php } ?>
<?php if (@$this->invoice->net_amount == 0) {?>
<strong><?php echo \JText::_('INVOICING_VALID_ORDER') ?></strong>
<ul>
<li class="paymentmethod">
<a href="<?php echo \JRoute::_('index.php?option=com_invoicing&view=payment&task=validorder&id='.$this->invoice->invoicing_invoice_id)?>">
<?php echo \JText::_('INVOICING_VALID_ORDER')?>
</a></li>
</ul>
<?php } else { ?>
<strong><?php echo \JText::_('INVOICING_SELECT_A_PAYMENT_METHOD') ?></strong>
<div class="row">
<?php
    \JPluginHelper::importPlugin('invoicingpayment');
?>
<?php 
	$paymentLinks = \JFactory::getApplication()->triggerEvent('onInvoicingPaymentSelection', array ($this->invoice));
    $i = 0;
	foreach($paymentLinks as $paymentLink) {
        if($paymentLink != null) {
            if($i % 3 == 0) {
                echo '</div><div class="row mt-3">';
            }
			echo "<div class='col col-4'>".$paymentLink."</div>";
            $i++;
        }
	}
?>
</div>
<?php } ?>
</div>