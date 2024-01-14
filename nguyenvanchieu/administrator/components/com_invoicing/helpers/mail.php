<?php
/**
 *  @package Invoicing
 *  @copyright Copyright (c)2012 JoomPROD
 *  @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/format.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/pdf.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/load.php');
jimport( 'joomla.filesystem.file' );

include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/cparams.php');


	define("INVOICE_CANCELLED", "undefined");
	define("INVOICE_PAID", 3);
	define("INVOICE_NEW", 1);
	define("INVOICE_PENDING", 4);
	define("INVOICE_PAID_ADMIN", 6);
	define("INVOICE_NEW_ADMIN", 5);
	define("INVOICE_PENDING_ADMIN", 7);
	define("QUOTE_USER", 8);
	define("QUOTE_ADMIN", 9);
	define("INVOICE_PAYMENT_DISPLAY_OFFLINE", 10);
	define("INVOICE_PAYMENT_DISPLAY_OFFLINE_ADMIN", 11);
	define("INVOICE_PAYMENT_DISPLAY_OFFLINE2", 12);
	define("INVOICE_PAYMENT_DISPLAY_OFFLINE2_ADMIN", 13);
	
class InvoicingHelperMail {
   
    /**
     * Mail function (uses phpMailer)
     *
     * @param   string   $from         From email address
     * @param   string   $fromname     From name
     * @param   mixed    $recipient    Recipient email address(es)
     * @param   string   $subject      Email subject
     * @param   string   $body         Message body
     * @param   boolean  $mode         False = plain text, true = HTML
     * @param   mixed    $cc           CC email address(es)
     * @param   mixed    $bcc          BCC email address(es)
     * @param   mixed    $attachment   Attachment file name(s)
     * @param   mixed    $replyto      Reply to email address(es)
     * @param   mixed    $replytoname  Reply to name(s)
     *
     * @return  boolean  True on success
     *
     * @see     JMail::sendMail()
     * @since   11.1
     */
    protected static function sendMail($from, $fromname, $recipient, $subject, $body, $mode = 0, $cc = null, $bcc = null, $attachment = null,
                $replyto = null, $replytoname = null)
    {	
			$input = \JFactory::getApplication()->input;
			$id = $input->getInt('id',0);
			
			$db = \JFactory::getDbo();
			$db->setQuery("SELECT * FROM #__invoicing_invoices WHERE invoicing_invoice_id = ".(int)$id);
			$invoice = $db->loadObject();
			
					$lang = \JFactory::getLanguage();
			$tag = $lang->getTag();
			if ($tag != $invoice->language) {
				InvoicingHelperLoad::loadLanguage($invoice->language);
			}
					
			$body = InvoicingHelperFormat::replaceTags($body,$invoice);
			$subject = InvoicingHelperFormat::replaceTags($subject,$invoice);
		
					if ($tag != $invoice->language) {
				InvoicingHelperLoad::loadLanguage($tag);
			}
					
					if (version_compare(JVERSION,'2.5.0','>=')) {
							// Get a JMail instance
				$mail = \JFactory::getMailer();
							$send = $mail->sendMail($from, $fromname, $recipient, $subject, $body, $mode, $cc, $bcc, $attachment,$replyto,$replytoname);

					} else {
							$send = JUtility::sendMail($from, $fromname, $recipient, $subject, $body, $mode, $cc, $bcc, $attachment,$replyto,$replytoname);
					}
			$type = gettype($send);
			if(is_object($send)) {
				$send = $send->getMessage() ;
			}
			return $send;
    }
    
    public static function getPDFInvoice($invoice) {
		$filenameText = \JText::_('INVOICING_INVOICE_FILENAME');
		$invoiceNumber = InvoicingHelperFormat::formatInvoiceNumber($invoice);
		$filename = sprintf($filenameText,$invoiceNumber);
		$filename = str_replace(array("/","."," "),"_",$filename);
		$filename =  JPATH_SITE.'/tmp/'.$filename.'.pdf';
		$data = InvoicingHelperFormat::formatInvoiceHTML($invoice);
		InvoicingHelperPDF::createPDF($data,$filename);
		return $filename;
	}

	public static function getPDFOrder($invoice) {
		$filenameText = \JText::_('INVOICING_ORDER_FILENAME');
		$invoiceNumber = InvoicingHelperFormat::formatOrderNumber($invoice);
		$filename = sprintf($filenameText,$invoiceNumber);
		$filename = str_replace(array("/","."," "),"_",$filename);
		$filename =  JPATH_SITE.'/tmp/'.$filename.'.pdf';
		$data = InvoicingHelperFormat::formatOrderHTML($invoice);
		InvoicingHelperPDF::createPDF($data,$filename);
		return $filename;
	}
	
	public static function getPDFQuote($quote) {
		$filenameText = \JText::_('INVOICING_QUOTE_FILENAME');
		$invoiceNumber = InvoicingHelperFormat::formatOrderNumber($invoice);
		$filename = sprintf($filenameText,$invoiceNumber);
		$filename = str_replace(array("/","."," "),"_",$filename);
		$filename =  JPATH_SITE.'/tmp/'.$filename.'.pdf';
		$data = InvoicingHelperFormat::formatQuoteHTML($quote);
		InvoicingHelperPDF::createPDF($data,$filename);
		return $filename;
	}
	
	public static function sendQuoteMail($quote) {
		$emailid = QUOTE_USER;
		$db = \JFactory::getDbo();
		$db->setQuery("SELECT * FROM #__invoicing_emails WHERE invoicing_email_id = ".(int)$emailid);
		$email = $db->loadObject();
		
		$from = InvoicingHelperCparams::getParam('sender_mail','');
		$fromname = InvoicingHelperCparams::getParam('sender_name','');
		
		if(!isset($from) || $from == '' ){
			if (version_compare(JVERSION,'3.0.0','>=')) {
				$versionJoomla = 1;
			} else {
				$versionJoomla = 0;
			}
			$config	= \JFactory::getConfig();
			$from = $versionJoomla ? $config->get('mailfrom') : $config->getValue('config.mailfrom');
			$fromname = $versionJoomla ? $config->get('fromname') : $config->getValue('config.fromname');
		}
			
		if ($from == "") {
			echo "You need to set email in invoicing configuration";
			return;
		}
		
        $lang = \JFactory::getLanguage();
		$tag = $lang->getTag();
		if ($tag != $quote->language) {
			InvoicingHelperLoad::loadLanguage($quote->language);
		}
        
		$email->body = InvoicingHelperFormat::replaceTags($email->body,$quote);
		$email->subject = InvoicingHelperFormat::replaceTags($email->subject,$quote);
		
		if ($tag != $quote->language) {
			InvoicingHelperLoad::loadLanguage($tag);
		}
        
		//TODO c'est quoi cette ligne à la con ?? maillinglist ca existe pas ??
		$sendToAdmin = InvoicingHelperCparams::getParam('maillinglist',1);
		
		$attachment = $email->pdf;
		
		if ($sendToAdmin) {
			//$bcc = array($from);

			$emailidAdmin = QUOTE_ADMIN;
				
			$db = \JFactory::getDbo();
			$db->setQuery("SELECT * FROM #__invoicing_emails WHERE invoicing_email_id = ".(int)$emailidAdmin);
			$emailAdmin = $db->loadObject();
				
			$attachmentAdmin = $emailAdmin->pdf;
				
			$emailAdmin->body = InvoicingHelperFormat::replaceTags($emailAdmin->body,$quote);
			$emailAdmin->subject = InvoicingHelperFormat::replaceTags($emailAdmin->subject,$quote);
		}
		
		$result = "";
		
		if ($attachment) {
			$attachment = self::getPDFInvoice($quote);
        } else {
            $attachment = null;
        }
		if ($attachmentAdmin) {
			$attachmentAdmin = $attachment;
        } else {
            $attachmentAdmin = null;
        }
		
		//var_dump($email);
		//var_dump($emailAdmin);
		//exit();
		if (($email->published)&&(isset($quote->buyer->email))&&($quote->buyer->email != null)) {
			$resultUser = self::sendMail($from, $fromname,$quote->buyer->email, $email->subject, $email->body, 1, null, null, $attachment);
		}
		
		if ($emailAdmin->published && $sendToAdmin) {
			$resultAdmin = self::sendMail($from, $fromname, $from, $emailAdmin->subject, $emailAdmin->body, 1, null,null, $attachmentAdmin);
		}
		
			
		return true;
	}

	public static function sendMailByStatus($invoice) {
		$body = "undefined";
		$status = $invoice->status;
		
        if($status == 'PENDING' && $invoice->processor == 'offline') {
            $status = 'PAYMENT_DISPLAY_OFFLINE';
        }
        if($status == 'PENDING' && $invoice->processor == 'offline2') {
            $status = 'PAYMENT_DISPLAY_OFFLINE2';
        }
        
		switch($status) {
			case 'PAID': $emailid = INVOICE_PAID;break;
		    case 'NEW': $emailid = INVOICE_NEW ;break;
			case 'PENDING': $emailid = INVOICE_PENDING;break;
			case 'PAYMENT_DISPLAY_OFFLINE': $emailid = INVOICE_PAYMENT_DISPLAY_OFFLINE;break;
			case 'PAYMENT_DISPLAY_OFFLINE2': $emailid = INVOICE_PAYMENT_DISPLAY_OFFLINE2;break;
			default: $emailid = null;
		}	
		
		if ($emailid == null)
			return;
		
		$db = \JFactory::getDbo();
		$db->setQuery("SELECT * FROM #__invoicing_emails WHERE invoicing_email_id = ".(int)$emailid);
		$email = $db->loadObject();

		jimport('joomla.application.component.helper');
		$params = \JComponentHelper::getParams('com_invoicing');
		
		$from = $params->get('sender_mail', '');//InvoicingHelperCparams::getParam('sender_mail','');
		$fromname = $params->get('sender_name', '');//InvoicingHelperCparams::getParam('sender_name','');
		
		if(!isset($from) || $from == '' ){
			if (version_compare(JVERSION,'3.0.0','>=')) {
				$versionJoomla = 1;
			} else {
				$versionJoomla = 0;
			}
			$config	= \JFactory::getConfig();
			$from = $versionJoomla ? $config->get('mailfrom') : $config->getValue('config.mailfrom');
			$fromname = $versionJoomla ? $config->get('fromname') : $config->getValue('config.fromname');
		}
					
		if ($from == "") {
			echo "You need to set email in invoicing configuration";
			return;
		}
		
		$email->body = InvoicingHelperFormat::replaceTags($email->body,$invoice);
		$email->subject = InvoicingHelperFormat::replaceTags($email->subject,$invoice);
		
		$sendToAdmin = $params->get('maillinglist', 1);//InvoicingHelperCparams::getParam('maillinglist',1);
		
		$attachment = $email->pdf;
		
		if ($sendToAdmin) {
			//$bcc = array($from);
			
			switch($status) {
				case 'PAID': $emailidAdmin = INVOICE_PAID_ADMIN;break;
				case 'NEW': $emailidAdmin = INVOICE_NEW_ADMIN ;break;
				case 'PENDING': $emailidAdmin = INVOICE_PENDING_ADMIN;break;
                case 'PAYMENT_DISPLAY_OFFLINE': $emailidAdmin = INVOICE_PAYMENT_DISPLAY_OFFLINE_ADMIN;break;
                case 'PAYMENT_DISPLAY_OFFLINE2': $emailidAdmin = INVOICE_PAYMENT_DISPLAY_OFFLINE2_ADMIN;break;
				default: $emailidAdmin = null;
			}	
			
			$db = \JFactory::getDbo();
			$db->setQuery("SELECT * FROM #__invoicing_emails WHERE invoicing_email_id = ".(int)$emailidAdmin);
			$emailAdmin = $db->loadObject();
			
			$attachmentAdmin = $emailAdmin->pdf;
			
			$emailAdmin->body = InvoicingHelperFormat::replaceTags($emailAdmin->body,$invoice);
			$emailAdmin->subject = InvoicingHelperFormat::replaceTags($emailAdmin->subject,$invoice);
		}
		
		$result = "";
		
		if ($attachment) {
			switch($status) {
                case 'PAYMENT_DISPLAY_OFFLINE': 
                case 'PAYMENT_DISPLAY_OFFLINE2': 
				case 'PENDING': $attachment = self::getPDFOrder($invoice);
								break;
				default: $attachment = self::getPDFInvoice($invoice);
			}
        } else {
            $attachment = null;
        }
		if ($attachmentAdmin) {
			$attachmentAdmin = $attachment;
        } else {
            $attachmentAdmin = null;
        }

		if (($email->published)&&(isset($invoice->buyer->email))&&($invoice->buyer->email != null)) {
			 $resultUser = self::sendMail($from, $fromname,$invoice->buyer->email, $email->subject, $email->body, 1, null, null, $attachment);
		}		
		
		if ($emailAdmin->published && $sendToAdmin) {
		 	$resultAdmin = self::sendMail($from, $fromname, $from, $emailAdmin->subject, $emailAdmin->body, 1, null,null, $attachmentAdmin); 
		}
		
			
		return true;
	}
	
	/**
	 * Send different types of mails depends on the status of the invoice
	 *
	 */
	 
	public static function sendPaymentConfirmationEmail() {
		self::sendMailByStatus(\JText::_('INVOICING_INVOICE_PAID'));
	}
	
	public static function sendInvoiceConfirmationEmail() {
		self::sendMailByStatus(\JText::_('INVOICING_INVOICE_NEW'));
	}
	
	public static function sendPaymentReminder() {
		self::sendMailByStatus(\JText::_('INVOICING_INVOICE_PENDING'));
	}
}
