<?php
/**
 * @package Invoicing
 * @copyright Copyright (c)203 Juloa.com
 * @license GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

class InvoicingModelInvoice extends \JModelLegacy
{
  public function getItem() {
    $dataArray = $this->getItemsArray();
    $item = false;

    if(!empty($dataArray)) {
      $item = array_values($dataArray)[0];
    }

    $item = $this->onAfterGetItem($item);

    return $item;
  }

  public function getRawItem($id) {
    $db = \JFactory::getDbo();

    $sql = "SELECT * FROM #__invoicing_invoices WHERE invoicing_invoice_id = ".(int)$id;
    $db->setQuery($sql);
    $item = $db->loadObject();

    $item = $this->onAfterGetItem($item);

    return $item;
  }

  public function onAfterGetItem($record) {

    $id = $record->invoicing_invoice_id;
    if ($id != null) {
      $db =\JFactory::getDBO();
      $db->setQuery("SELECT * FROM #__invoicing_invoice_items WHERE invoice_id=".$id." ORDER BY ordering ASC");
      $list = $db->loadObjectList();
      $record->items = $list;
    
      $db->setQuery("SELECT * FROM #__invoicing_vendors WHERE invoicing_vendor_id=".(int)$record->vendor_id);
      $vendor = $db->loadObject();
      $record->vendor = $vendor;

      $userModel = InvoicingModelUsers::getInstance('Users', 'InvoicingModel');

      $record->buyer = $userModel->getItem($record->user_id);
      
      $db->setQuery("SELECT code as coupon,title as coupon_title FROM #__invoicing_coupons WHERE invoicing_coupon_id=".(int)$record->coupon_id);
      $c = $db->loadObject();
            if($c != null) {
                $record->coupon = $c->coupon;
                $record->coupon_title = $c->coupon_title;
            }
    } else {
      $record->number= InvoicingHelperCParams::getParam('invoice_number_counter',1);
      $record->created_on = date("Y-m-d");
      $record->coupon = "";
      $record->buyer = new \stdClass();
      $record->vendor = new \stdClass();
      $record->items = null;
    }

    return $record;
  }
}
