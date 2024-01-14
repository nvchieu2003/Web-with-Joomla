<?php
/**
 * @package		invoicing
 * @copyright	Copyright (c)2010-2014 Thomas PAPIN
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 * @author		Thomas PAPIN - Janich Rasmussen <janich@gmail.com>
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

require_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/assets/paymentplugin.php');
require_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/format.php');

class plgInvoicingpaymentEpaydk extends InvoicingAbstractPaymentPlugin
{
	public function __construct(&$subject, $config = array())
	{
		$config = array_merge($config, array(
			'ppName'		=> 'epaydk',
			'ppKey'			=> 'PLG_INVOICINGPAYMENT_EPAYDK_TITLE',
			'ppImage'		=> 'http://www.epay.dk/images/layout/logo.gif',
		));
		
		parent::__construct($subject, $config);
	}

	/**
	 * Returns the payment form to be submitted by the user's browser. The form must have an ID of
	 * "paymentForm" and a visible submit button.
	 * 
	 * @param string $paymentmethod
	 * @param JUser $user
	 * @param AkeebasubsTableLevel $level
	 * @param AkeebasubsTableSubscription $subscription
	 * @return string
	 */
	//public function onAKPaymentNew($paymentmethod, $user, $level, $subscription)
	function onInvoicingPaymentDisplay($paymentmethod,$data)
	{
		if ($paymentmethod != $this->ppName) return false;
		
		$app = \JFactory::getApplication();
		$db = \JFactory::getDbo();
		$user		= \JFactory::getUser();
		
		$nameParts = explode(' ', $user->name, 2);
		$firstName = $nameParts[0];
		if (count($nameParts) > 1) {
			$lastName = $nameParts[1];
		} else {
			$lastName = '';
		}
		
		$rootURL = rtrim(JURI::base(),'/');
		$subpathURL = JURI::base(true);
		if (!empty($subpathURL) && ($subpathURL != '/')) {
			$rootURL = substr($rootURL, 0, -1 * strlen($subpathURL));
		}
		
		// Separate URL variable as it cannot be a part of the md5 checksum
		$url = $this->getPaymentURL();
		
		$currencyModel = InvoicingModelCurrencies::getInstance('Currencies', 'InvoicingModel');
		$currency = $currencyModel->getItem($data->currency_id);
		
		//var_dump($currency,$data->currency_id);exit();
		$dataform = array(
			'merchant'			=> $this->getMerchantID(),
			'success'			=> $rootURL.str_replace('&amp;','&',\JRoute::_('index.php?option=com_invoicing&view=message&layout=complete')),
			'cancel'			=> $rootURL.str_replace('&amp;','&',\JRoute::_('index.php?option=com_invoicing&view=message&layout=cancel')),
			'postback'			=> JURI::base() . 'index.php?option=com_invoicing&view=payment&task=process&method=epaydk',
			'orderid'			=> $data->invoicing_invoice_id,
			'currency'			=> $currency->code,
			'amount'			=> ($data->gross_amount * 100),		// Epay calculates in minor amounts, and doesn't support tax differentation
			'cardtypes'			=> implode(',', $this->params->get('cardtypes', array())),
			'instantcapture'	=> '1',
			'instantcallback'	=> '1',
			'language'			=> $this->params->get('language', '0'),
			'ordertext'			=> InvoicingHelperFormat::formatOrderNumber($data),
			'windowstate'		=> '3',
			'ownreceipt'		=> '0',
			'md5'				=> $this->params->get('secret','')										// Will be overriden with md5sum checksum
		);
		
		if ($this->params->get('md5', 1)) {
			// Security hash - must be compiled from ALL inputs sent
			$dataform['md5'] = md5(implode('', $dataform));
		}
		else {
			$dataform['md5'] = '';
		}
		
		// Set array as object for compatability
		$dataform = (object) $dataform;
		
		
		
		@ob_start();
		
		$cardtypes = array();
		include dirname(__FILE__).'/epaydk/form.php';
		$html = @ob_get_clean();
		
		return $html;
	}
	
	public function onInvoicingPaymentNotification($paymentmethod, $data)
	{
		JLoader::import('joomla.utilities.date');
		
		// Check if we're supposed to handle this
		if ($paymentmethod != $this->ppName) return false;
		
		// Check return values for md5 security hash for validity (i.e. protect against fraud attempt)
		$isValid = $this->isValidRequest($data);
		if (!$isValid) {
			$data['akeebasubs_failure_reason'] = 'Epay reports transaction as invalid';
		}
		
		if($isValid) {
			$id = array_key_exists('orderid', $data) ? (int) $data['orderid'] : -1;
			$amount = floatval($data['amount'] / 100);	
			$isGrossAmount = true;
			$currencyid = $data['currency'];
			$epay_currency_codes = array('4'=>'AFA','8'=>'ALL','12'=>'DZD','20'=>'ADP','31'=>'AZM','32'=>'ARS','36'=>'AUD','44'=>'BSD','48'=>'BHD','50'=>'BDT','51'=>'AMD','52'=>'BBD','60'=>'BMD','64'=>'BTN','68'=>'BOB','72'=>'BWP','84'=>'BZD','90'=>'SBD','96'=>'BND','100'=>'BGL','104'=>'MMK','108'=>'BIF','116'=>'KHR','124'=>'CAD','132'=>'CVE','136'=>'KYD','144'=>'LKR','152'=>'CLP','156'=>'CNY','170'=>'COP','174'=>'KMF','188'=>'CRC','191'=>'HRK','192'=>'CUP','196'=>'CYP','203'=>'CZK','208'=>'DKK','214'=>'DOP','218'=>'ECS','222'=>'SVC','230'=>'ETB','232'=>'ERN','233'=>'EEK','238'=>'FKP','242'=>'FJD','262'=>'DJF','270'=>'GMD','288'=>'GHC','292'=>'GIP','320'=>'GTQ','324'=>'GNF','328'=>'GYD','332'=>'HTG','340'=>'HNL','344'=>'HKD','348'=>'HUF','352'=>'ISK','356'=>'INR','360'=>'IDR','364'=>'IRR','368'=>'IQD','376'=>'ILS','388'=>'JMD','392'=>'JPY','398'=>'KZT','400'=>'JOD','404'=>'KES','408'=>'KPW','410'=>'KRW','414'=>'KWD','417'=>'KGS','418'=>'LAK','422'=>'LBP','426'=>'LSL','428'=>'LVL','430'=>'LRD','434'=>'LYD','440'=>'LTL','446'=>'MOP','450'=>'MGF','454'=>'MWK','458'=>'MYR','462'=>'MVR','470'=>'MTL','478'=>'MRO','480'=>'MUR','484'=>'MXN','496'=>'MNT','498'=>'MDL','504'=>'MAD','508'=>'MZM','512'=>'OMR','516'=>'NAD','524'=>'NPR','532'=>'ANG','533'=>'AWG','548'=>'VUV','554'=>'NZD','558'=>'NIO','566'=>'NGN','578'=>'NOK','586'=>'PKR','590'=>'PAB','598'=>'PGK','600'=>'PYG','604'=>'PEN','608'=>'PHP','624'=>'GWP','626'=>'TPE','634'=>'QAR','642'=>'ROL','643'=>'RUB','646'=>'RWF','654'=>'SHP','678'=>'STD','682'=>'SAR','690'=>'SCR','694'=>'SLL','702'=>'SGD','703'=>'SKK','704'=>'VND','705'=>'SIT','706'=>'SOS','710'=>'ZAR','716'=>'ZWD','736'=>'SDD','740'=>'SRG','748'=>'SZL','752'=>'SEK','756'=>'CHF','760'=>'SYP','764'=>'THB','776'=>'TOP','780'=>'TTD','784'=>'AED','788'=>'TND','792'=>'TRL','795'=>'TMM','800'=>'UGX','807'=>'MKD','810'=>'RUR','818'=>'EGP','826'=>'GBP','834'=>'TZS','840'=>'USD','858'=>'UYU','860'=>'UZS','862'=>'VEB','886'=>'YER','891'=>'YUM','894'=>'ZMK','901'=>'TWD','949'=>'TRY','950'=>'XAF','951'=>'XCD','952'=>'XOF','953'=>'XPF','972'=>'TJS','973'=>'AOA','974'=>'BYR','975'=>'BGN','976'=>'CDF','977'=>'BAM','978'=>'EUR','979'=>'MXV','980'=>'UAH','981'=>'GEL','983'=>'ECV','984'=>'BOV','985'=>'PLN','986'=>'BRL','990'=>'CLF');
			$currency_code = $epay_currency_codes[$currencyid];
			
			if ($this->checkValidity($id,$amount,$isGrossAmount,$currency_code) == true) {
				$this->validPayment($id,$paymentmethod);
			} else {
				$isValid = false;
			}
		}
		
		// Fraud attempt? Do nothing more!
		if(!$isValid) return false;
	}
	
	
	/**
	 * Gets the form action URL for the payment
	 */
	private function getPaymentURL()
	{
		$sandbox = $this->params->get('sandbox', 0);
		if ($sandbox) {
			// return different url if Epay ever changes
		}
		
		return 'https://ssl.ditonlinebetalingssystem.dk/integration/ewindow/Default.aspx';
	}
	
	
	/**
	 * Gets the Epay Merchant ID (usually digits only)
	 */
	private function getMerchantID()
	{
		$sandbox = $this->params->get('sandbox', 0);
		if ($sandbox) {
			return $this->params->get('sandbox_merchant', '');
		}
		
		return $this->params->get('merchant', '');
	}
	
	
	/**
	 * Validates the incoming data against Epay's security hash to make sure this is not a
	 * fraudelent request.
	 */
	private function isValidRequest($data)
	{
		if ($this->params->get('md5', 1)) {
			// Temp. replace hash with secret
			$hash = $data['hash'];
			$data['hash'] = $this->params->get('secret', '');
			
			// Calculate checksum
			$checksum = md5(implode('', $data));
			
			// Replace hash with original
			$data['hash'] = $hash;
			
			if ($checksum != $hash) {
				return false;
			}
		}
		
		return true;
	}
}