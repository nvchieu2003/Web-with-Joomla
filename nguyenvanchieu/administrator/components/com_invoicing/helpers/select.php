<?php
/**
 *  @package Invoicing
 *  @copyright Copyright (c)203 Juloa.com
 *  @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();


include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/models/coupons.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/models/invoices.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/models/logs.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/models/quotes.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/models/templates.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/models/users.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/models/vendors.php');
include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/models/payment.php');
use Joomla\CMS\Language\LanguageHelper as JLanguageHelper;


class InvoicingHelperSelect
{
	public static $countries = array(
		'' => '----',
		'AD' => 'INVOICING_COUNTRY_AD',
		'AE' => 'INVOICING_COUNTRY_AE',
		'AF' => 'INVOICING_COUNTRY_AF',
		'AG' => 'INVOICING_COUNTRY_AG',
		'AI' => 'INVOICING_COUNTRY_AI',
		'AL' => 'INVOICING_COUNTRY_AL',
		'AM' => 'INVOICING_COUNTRY_AM',
		'AN' => 'INVOICING_COUNTRY_AN',
		'AO' => 'INVOICING_COUNTRY_AO',
		'AQ' => 'INVOICING_COUNTRY_AQ',
		'AR' => 'INVOICING_COUNTRY_AR',
		'AS' => 'INVOICING_COUNTRY_AS',
		'AT' => 'INVOICING_COUNTRY_AT',
		'AU' => 'INVOICING_COUNTRY_AU',
		'AW' => 'INVOICING_COUNTRY_AW',
		'AX' => 'INVOICING_COUNTRY_AX',
		'AZ' => 'INVOICING_COUNTRY_AZ',
		'BA' => 'INVOICING_COUNTRY_BA',
		'BB' => 'INVOICING_COUNTRY_BB',
		'BD' => 'INVOICING_COUNTRY_BD',
		'BE' => 'INVOICING_COUNTRY_BE',
		'BF' => 'INVOICING_COUNTRY_BF',
		'BG' => 'INVOICING_COUNTRY_BG',
		'BH' => 'INVOICING_COUNTRY_BH',
		'BI' => 'INVOICING_COUNTRY_BI',
		'BJ' => 'INVOICING_COUNTRY_BJ',
		'BL' => 'INVOICING_COUNTRY_BL',
		'BM' => 'INVOICING_COUNTRY_BM',
		'BN' => 'INVOICING_COUNTRY_BN',
		'BO' => 'INVOICING_COUNTRY_BO',
		'BR' => 'INVOICING_COUNTRY_BR',
		'BS' => 'INVOICING_COUNTRY_BS',
		'BT' => 'INVOICING_COUNTRY_BT',
		'BV' => 'INVOICING_COUNTRY_BV',
		'BW' => 'INVOICING_COUNTRY_BW',
		'BY' => 'INVOICING_COUNTRY_BY',
		'BZ' => 'INVOICING_COUNTRY_BZ',
		'CA' => 'INVOICING_COUNTRY_CA',
		'CC' => 'INVOICING_COUNTRY_CC',
		'CD' => 'INVOICING_COUNTRY_CD',
		'CF' => 'INVOICING_COUNTRY_CF',
		'CG' => 'INVOICING_COUNTRY_CG',
		'CH' => 'INVOICING_COUNTRY_CH',
		'CI' => 'INVOICING_COUNTRY_CI',
		'CK' => 'INVOICING_COUNTRY_CK',
		'CL' => 'INVOICING_COUNTRY_CL',
		'CM' => 'INVOICING_COUNTRY_CM',
		'CN' => 'INVOICING_COUNTRY_CN',
		'CO' => 'INVOICING_COUNTRY_CO',
		'CR' => 'INVOICING_COUNTRY_CR',
		'CU' => 'INVOICING_COUNTRY_CU',
		'CV' => 'INVOICING_COUNTRY_CV',
		'CX' => 'INVOICING_COUNTRY_CX',
		'CY' => 'INVOICING_COUNTRY_CY',
		'CZ' => 'INVOICING_COUNTRY_CZ',
		'DE' => 'INVOICING_COUNTRY_DE',
		'DJ' => 'INVOICING_COUNTRY_DJ',
		'DK' => 'INVOICING_COUNTRY_DK',
		'DM' => 'INVOICING_COUNTRY_DM',
		'DO' => 'INVOICING_COUNTRY_DO',
		'DZ' => 'INVOICING_COUNTRY_DZ',
		'EC' => 'INVOICING_COUNTRY_EC',
		'EE' => 'INVOICING_COUNTRY_EE',
		'EG' => 'INVOICING_COUNTRY_EG',
		'EH' => 'INVOICING_COUNTRY_EH',
		'ER' => 'INVOICING_COUNTRY_ER',
		'ES' => 'INVOICING_COUNTRY_ES',
		'ET' => 'INVOICING_COUNTRY_ET',
		'FI' => 'INVOICING_COUNTRY_FI',
		'FJ' => 'INVOICING_COUNTRY_FJ',
		'FK' => 'INVOICING_COUNTRY_FK',
		'FM' => 'INVOICING_COUNTRY_FM',
		'FO' => 'INVOICING_COUNTRY_FO',
		'FR' => 'INVOICING_COUNTRY_FR',
		'GA' => 'INVOICING_COUNTRY_GA',
		'GB' => 'INVOICING_COUNTRY_GB',
		'GD' => 'INVOICING_COUNTRY_GD',
		'GE' => 'INVOICING_COUNTRY_GE',
		'GF' => 'INVOICING_COUNTRY_GF',
		'GG' => 'INVOICING_COUNTRY_GG',
		'GH' => 'INVOICING_COUNTRY_GH',
		'GI' => 'INVOICING_COUNTRY_GI',
		'GL' => 'INVOICING_COUNTRY_GL',
		'GM' => 'INVOICING_COUNTRY_GM',
		'GN' => 'INVOICING_COUNTRY_GN',
		'GP' => 'INVOICING_COUNTRY_GP',
		'GQ' => 'INVOICING_COUNTRY_GQ',
		'GR' => 'INVOICING_COUNTRY_GR',
		'GS' => 'INVOICING_COUNTRY_GS',
		'GT' => 'INVOICING_COUNTRY_GT',
		'GU' => 'INVOICING_COUNTRY_GU',
		'GW' => 'INVOICING_COUNTRY_GW',
		'GY' => 'INVOICING_COUNTRY_GY',
		'HK' => 'INVOICING_COUNTRY_HK',
		'HM' => 'INVOICING_COUNTRY_HM',
		'HN' => 'INVOICING_COUNTRY_HN',
		'HR' => 'INVOICING_COUNTRY_HR',
		'HT' => 'INVOICING_COUNTRY_HT',
		'HU' => 'INVOICING_COUNTRY_HU',
		'ID' => 'INVOICING_COUNTRY_ID',
		'IE' => 'INVOICING_COUNTRY_IE',
		'IL' => 'INVOICING_COUNTRY_IL',
		'IM' => 'INVOICING_COUNTRY_IM',
		'IN' => 'INVOICING_COUNTRY_IN',
		'IO' => 'INVOICING_COUNTRY_IO',
		'IQ' => 'INVOICING_COUNTRY_IQ',
		'IR' => 'INVOICING_COUNTRY_IR',
		'IS' => 'INVOICING_COUNTRY_IS',
		'IT' => 'INVOICING_COUNTRY_IT',
		'JE' => 'INVOICING_COUNTRY_JE',
		'JM' => 'INVOICING_COUNTRY_JM',
		'JO' => 'INVOICING_COUNTRY_JO',
		'JP' => 'INVOICING_COUNTRY_JP',
		'KE' => 'INVOICING_COUNTRY_KE',
		'KG' => 'INVOICING_COUNTRY_KG',
		'KH' => 'INVOICING_COUNTRY_KH',
		'KI' => 'INVOICING_COUNTRY_KI',
		'KM' => 'INVOICING_COUNTRY_KM',
		'KN' => 'INVOICING_COUNTRY_KN',
		'KP' => 'INVOICING_COUNTRY_KP',
		'KR' => 'INVOICING_COUNTRY_KR',
		'KW' => 'INVOICING_COUNTRY_KW',
		'KY' => 'INVOICING_COUNTRY_KY',
		'KZ' => 'INVOICING_COUNTRY_KZ',
		'LA' => 'INVOICING_COUNTRY_LA',
		'LB' => 'INVOICING_COUNTRY_LB',
		'LC' => 'INVOICING_COUNTRY_LC',
		'LI' => 'INVOICING_COUNTRY_LI',
		'LK' => 'INVOICING_COUNTRY_LK',
		'LR' => 'INVOICING_COUNTRY_LR',
		'LS' => 'INVOICING_COUNTRY_LS',
		'LT' => 'INVOICING_COUNTRY_LT',
		'LU' => 'INVOICING_COUNTRY_LU',
		'LV' => 'INVOICING_COUNTRY_LV',
		'LY' => 'INVOICING_COUNTRY_LY',
		'MA' => 'INVOICING_COUNTRY_MA',
		'MC' => 'INVOICING_COUNTRY_MC',
		'MD' => 'INVOICING_COUNTRY_MD',
		'ME' => 'INVOICING_COUNTRY_ME',
		'MF' => 'INVOICING_COUNTRY_MF',
		'MG' => 'INVOICING_COUNTRY_MG',
		'MH' => 'INVOICING_COUNTRY_MH',
		'MK' => 'INVOICING_COUNTRY_MK',
		'ML' => 'INVOICING_COUNTRY_ML',
		'MM' => 'INVOICING_COUNTRY_MM',
		'MN' => 'INVOICING_COUNTRY_MN',
		'MO' => 'INVOICING_COUNTRY_MO',
		'MP' => 'INVOICING_COUNTRY_MP',
		'MQ' => 'INVOICING_COUNTRY_MQ',
		'MR' => 'INVOICING_COUNTRY_MR',
		'MS' => 'INVOICING_COUNTRY_MS',
		'MT' => 'INVOICING_COUNTRY_MT',
		'MU' => 'INVOICING_COUNTRY_MU',
		'MV' => 'INVOICING_COUNTRY_MV',
		'MW' => 'INVOICING_COUNTRY_MW',
		'MX' => 'INVOICING_COUNTRY_MX',
		'MY' => 'INVOICING_COUNTRY_MY',
		'MZ' => 'INVOICING_COUNTRY_MZ',
		'NA' => 'INVOICING_COUNTRY_NA',
		'NC' => 'INVOICING_COUNTRY_NC',
		'NE' => 'INVOICING_COUNTRY_NE',
		'NF' => 'INVOICING_COUNTRY_NF',
		'NG' => 'INVOICING_COUNTRY_NG',
		'NI' => 'INVOICING_COUNTRY_NI',
		'NL' => 'INVOICING_COUNTRY_NL',
		'NO' => 'INVOICING_COUNTRY_NO',
		'NP' => 'INVOICING_COUNTRY_NP',
		'NR' => 'INVOICING_COUNTRY_NR',
		'NU' => 'INVOICING_COUNTRY_NU',
		'NZ' => 'INVOICING_COUNTRY_NZ',
		'OM' => 'INVOICING_COUNTRY_OM',
		'PA' => 'INVOICING_COUNTRY_PA',
		'PE' => 'INVOICING_COUNTRY_PE',
		'PF' => 'INVOICING_COUNTRY_PF',
		'PG' => 'INVOICING_COUNTRY_PG',
		'PH' => 'INVOICING_COUNTRY_PH',
		'PK' => 'INVOICING_COUNTRY_PK',
		'PL' => 'INVOICING_COUNTRY_PL',
		'PM' => 'INVOICING_COUNTRY_PM',
		'PN' => 'INVOICING_COUNTRY_PN',
		'PR' => 'INVOICING_COUNTRY_PR',
		'PS' => 'INVOICING_COUNTRY_PS',
		'PT' => 'INVOICING_COUNTRY_PT',
		'PW' => 'INVOICING_COUNTRY_PW',
		'PY' => 'INVOICING_COUNTRY_PY',
		'QA' => 'INVOICING_COUNTRY_QA',
		'RE' => 'INVOICING_COUNTRY_RE',
		'RO' => 'INVOICING_COUNTRY_RO',
		'RS' => 'INVOICING_COUNTRY_RS',
		'RU' => 'INVOICING_COUNTRY_RU',
		'RW' => 'INVOICING_COUNTRY_RW',
		'SA' => 'INVOICING_COUNTRY_SA',
		'SB' => 'INVOICING_COUNTRY_SB',
		'SC' => 'INVOICING_COUNTRY_SC',
		'SD' => 'INVOICING_COUNTRY_SD',
		'SE' => 'INVOICING_COUNTRY_SE',
		'SG' => 'INVOICING_COUNTRY_SG',
		'SH' => 'INVOICING_COUNTRY_SH',
		'SI' => 'INVOICING_COUNTRY_SI',
		'SJ' => 'INVOICING_COUNTRY_SJ',
		'SK' => 'INVOICING_COUNTRY_SK',
		'SL' => 'INVOICING_COUNTRY_SL',
		'SM' => 'INVOICING_COUNTRY_SM',
		'SN' => 'INVOICING_COUNTRY_SN',
		'SO' => 'INVOICING_COUNTRY_SO',
		'SR' => 'INVOICING_COUNTRY_SR',
		'ST' => 'INVOICING_COUNTRY_ST',
		'SV' => 'INVOICING_COUNTRY_SV',
		'SY' => 'INVOICING_COUNTRY_SY',
		'SZ' => 'INVOICING_COUNTRY_SZ',
		'TC' => 'INVOICING_COUNTRY_TC',
		'TD' => 'INVOICING_COUNTRY_TD',
		'TF' => 'INVOICING_COUNTRY_TF',
		'TG' => 'INVOICING_COUNTRY_TG',
		'TH' => 'INVOICING_COUNTRY_TH',
		'TJ' => 'INVOICING_COUNTRY_TJ',
		'TK' => 'INVOICING_COUNTRY_TK',
		'TL' => 'INVOICING_COUNTRY_TL',
		'TM' => 'INVOICING_COUNTRY_TM',
		'TN' => 'INVOICING_COUNTRY_TN',
		'TO' => 'INVOICING_COUNTRY_TO',
		'TR' => 'INVOICING_COUNTRY_TR',
		'TT' => 'INVOICING_COUNTRY_TT',
		'TV' => 'INVOICING_COUNTRY_TV',
		'TW' => 'INVOICING_COUNTRY_TW',
		'TZ' => 'INVOICING_COUNTRY_TZ',
		'UA' => 'INVOICING_COUNTRY_UA',
		'UG' => 'INVOICING_COUNTRY_UG',
		'UM' => 'INVOICING_COUNTRY_UM',
		'US' => 'INVOICING_COUNTRY_US',
		'UY' => 'INVOICING_COUNTRY_UY',
		'UZ' => 'INVOICING_COUNTRY_UZ',
		'VA' => 'INVOICING_COUNTRY_VA',
		'VC' => 'INVOICING_COUNTRY_VC',
		'VE' => 'INVOICING_COUNTRY_VE',
		'VG' => 'INVOICING_COUNTRY_VG',
		'VI' => 'INVOICING_COUNTRY_VI',
		'VN' => 'INVOICING_COUNTRY_VN',
		'VU' => 'INVOICING_COUNTRY_VU',
		'WF' => 'INVOICING_COUNTRY_WF',
		'WS' => 'INVOICING_COUNTRY_WS',
		'YE' => 'INVOICING_COUNTRY_YE',
		'YT' => 'INVOICING_COUNTRY_YT',
		'ZA' => 'INVOICING_COUNTRY_ZA',
		'ZM' => 'INVOICING_COUNTRY_ZM',
		'ZW' => 'INVOICING_COUNTRY_ZW'
	);
	
	protected static function populateCountries($countries) {
		foreach ($countries as $key => $value) {
			$countries[$key] = \JText::_($value);
		}
		return $countries;
	}
	
	protected static function genericlist($list, $name, $attribs, $selected, $idTag)
	{
		if(empty($attribs))
		{
			$attribs = null;		
		}
		else
		{
			$temp = '';
			foreach($attribs as $key=>$value)
			{
				$temp .= $key.' = "'.$value.'"';
			}
			$attribs = $temp;
		}

		return \JHTML::_('select.genericlist', $list, $name, $attribs, 'value', 'text', $selected, $idTag);
	}

	protected static function genericradiolist($list, $name, $attribs, $selected, $idTag)
	{
		if(empty($attribs))
		{
			$attribs = null;
		}
		else
		{
			$temp = '';
			foreach($attribs as $key=>$value)
			{
				$temp .= $key.' = "'.$value.'"';
			}
			$attribs = $temp;
		}

		return \JHTML::_('select.radiolist', $list, $name, $attribs, 'value', 'text', $selected, $idTag);
	}

	public static function booleanlist( $name, $attribs = null, $selected = null )
	{
		$options = array(
			\JHTML::_('select.option','','---'),
			\JHTML::_('select.option',  '0', \JText::_( 'JNo' ) ),
			\JHTML::_('select.option',  '1', \JText::_( 'JYes' ) )
		);
		return self::genericlist($options, $name, $attribs, $selected, $name);
	}

	public static function countries($selected = null, $id = 'country', $attribs = array())
	{
		$options = array();
		$countries = self::populateCountries(self::$countries);		
		asort($countries);
		foreach($countries as $code => $name)
		{
			$options[] = \JHTML::_('select.option', $code, $name );
		}
		
		if (isset($attribs['id'])) {
			$rid = $attribs['id'];
		} else {
			$rid = $id;
		}
		
		
		return self::genericlist($options, $id, $attribs, $selected, $rid);
	}
	
	public static function processors($selected = null, $id = 'processor', $attribs = array())
	{
		$model = InvoicingModelPayment::getInstance('Payment', 'InvoicingModel');
		$processors = $model->getPaymentPlugins();
		$options = array();
		$options[] = \JHTML::_('select.option', "", "" );
		foreach($processors as $processor)
		{
			$options[] = \JHTML::_('select.option', $processor->name, $processor->title );
		}
		return self::genericlist($options, $id, $attribs, $selected, $id);
	}
	
	public static function usergroups($name = 'usergroups', $selected = '', $attribs = array())
	{
		// Get a database object.
		$db = \JFactory::getDBO();

		// Get the user groups from the database.
		$query = $db->getQuery(true)
			->select(array(
			$db->qn('a').'.'.$db->qn('id'),
			$db->qn('a').'.'.$db->qn('title'),
			$db->qn('a').'.'.$db->qn('parent_id').' AS '.$db->qn('parent'),
			'COUNT(DISTINCT '.$db->qn('b').'.'.$db->qn('id').') AS '.$db->qn('level')
		))->from($db->qn('#__usergroups').' AS '.$db->qn('a'))
		->join('left', $db->qn('#__usergroups').' AS '.$db->qn('b').' ON '.
			$db->qn('a').'.'.$db->qn('lft').' > '.$db->qn('b').'.'.$db->qn('lft').
			' AND '.$db->qn('a').'.'.$db->qn('rgt').' < '.$db->qn('b').'.'.$db->qn('rgt')
		)->group(array(
			$db->qn('a').'.'.$db->qn('id')
		))->order(array(
			$db->qn('a').'.'.$db->qn('lft').' ASC'
		))
		;
		$db->setQuery($query);
		$groups = $db->loadObjectList();

		$options = array();
		$options[] = \JHTML::_('select.option', '', '- '.\JText::_('INVOICING_COMMON_SELECT').' -');

		foreach ($groups as $group) {
			$options[] = \JHTML::_('select.option', $group->id, \JText::_($group->title));
		}

		return self::genericlist($options, $name, $attribs, $selected, $name);
	}

	public static function published($selected = null, $id = 'enabled', $attribs = array())
	{
		$options = array();
		$options[] = \JHTML::_('select.option',null,'- '.\JText::_('INVOICING_COMMON_SELECTSTATE').' -');
		$options[] = \JHTML::_('select.option',0,\JText::_('JUNPUBLISHED'));
		$options[] = \JHTML::_('select.option',1,\JText::_('JPUBLISHED'));

		return self::genericlist($options, $id, $attribs, $selected, $id);
	}
	public static function enabled($selected = null, $id = 'enabled', $attribs = array())
	{
		$options = array();
		$options[] = \JHTML::_('select.option',null,'- '.\JText::_('INVOICING_COMMON_SELECTSTATE').' -');
		$options[] = \JHTML::_('select.option',0,\JText::_('JDISABLED'));
		$options[] = \JHTML::_('select.option',1,\JText::_('JENABLED'));

		return self::genericlist($options, $id, $attribs, $selected, $id);
	}
	
	public static function symbolposition($selected = null, $id = 'symbol_position', $attribs = array())
	{
		$options = array();
		$options[] = \JHTML::_('select.option','','- '.\JText::_('INVOICING_COMMON_SELECTSTATE').' -');
	
		$options[] = \JHTML::_('select.option',"before",\JText::_('INVOICING_COMMON_BEFORE'));
		$options[] = \JHTML::_('select.option',"after",\JText::_('INVOICING_COMMON_AFTER'));
		
		return self::genericlist($options, $id, $attribs, $selected, $id);
	}
	
	public static function languages($selected = null, $id = 'language', $attribs = array() )
	{
		$languages = JLanguageHelper::getLanguages('lang_code');
		$options = array();

		if(!empty($languages)) foreach($languages as $key => $lang)
		{
				$options[] = \JHTML::_('select.option',$key,$lang->title);
		}
		
		
		return self::genericlist($options, $id, $attribs, $selected, $id);
	}
		/**
	 * Returns a list of status in array types
	 */
	public static function invoicestatus($selected = null,$id ='status', $attribs = array())
	{
		$options = array();
		
		$include_all = false;
		if(array_key_exists('include_all', $attribs)) {
			$include_all = $attribs['include_all'];
			unset($attribs['include_all']);
		}
		if ($include_all) {
			$options[] = \JHTML::_('select.option','','- '.\JText::_('INVOICING_INVOICE_STATE').' -');
		}
		
		$types = array('NEW','CANCELLED','PAID','PENDING');
		foreach($types as $type) $options[] = \JHTML::_('select.option',$type,\JText::_('INVOICING_INVOICE_'.$type));

		return self::genericlist($options, $id, $attribs, $selected, $id);
	}

	/**
	 * Drop down list of payment states
	 */
	public static function coupontypes($name = 'valuetype', $selected = null , $attribs = array())
	{
		$options = array();
	
		$options[] = \JHTML::_('select.option','','- '.\JText::_('INVOICING_COMMON_SELECT').' -');
		$options[] = \JHTML::_('select.option','value',\JText::_('INVOICING_COUPON_TYPE_VALUE'));
		$options[] = \JHTML::_('select.option','percent',\JText::_('INVOICING_COUPON_TYPE_PERCENT'));
		
		return self::genericlist($options, $name, $attribs, $selected, $name);
	}
	
	/**
	 * Shows a listbox with defined subscription levels
	 */
	public static function vendors($name = 'vendor_id', $selected = null, $attribs = array())
	{
		$model = InvoicingModelVendors::getInstance('Vendors', 'InvoicingModel');
		$db = \JFactory::getDbo();
		$query = $db->getQuery(true)
		->select('*')
		->from($db->qn('#__invoicing_vendors'))
		->order($db->qn('company_name') . 'ASC');

		$list = $db->setQuery($query)->loadObjectList();
	
		$options   = array();
	
		$include_none = false;
		$include_all = false;
		$include_add = false;
		$include_find = false;
		if(array_key_exists('include_none', $attribs)) {
			$include_none = $attribs['include_none'];
			unset($attribs['include_none']);
		}
		if(array_key_exists('include_all', $attribs)) {
			$include_all = $attribs['include_all'];
			unset($attribs['include_all']);
		}
		if(array_key_exists('include_add', $attribs)) {
			$include_add = $attribs['include_add'];
			unset($attribs['include_add']);
		}
		if(array_key_exists('include_find', $attribs)) {
			$include_find = $attribs['include_find'];
			unset($attribs['include_find']);
		}
	
		if($include_none) {
			$options[] = \JHTML::_('select.option','-1',\JText::_('INVOICING_COMMON_SELECTLEVEL_NONE'));
		}
		if($include_all) {
			$options[] = \JHTML::_('select.option','0',\JText::_('INVOICING_COMMON_SELECTLEVEL_ALL'));
		}
		if(!$include_none && !$include_all) {
			$options[] = \JHTML::_('select.option','','- '.\JText::_('INVOICING_COMMON_SELECT_VENDOR').' -');
		}
	
		foreach($list as $item) {
			$options[] = \JHTML::_('select.option',$item->invoicing_vendor_id,$item->contact_name);
		}
		
		$html = self::genericlist($options, $name, $attribs, $selected, $name);
		
		if($include_find) {
			$html .= '<a class="modal" id="add'.$name.'" href="index.php?option=com_invoicing&view=vendors&layout=modal&tmpl=component&amp;field='.$name.'" rel="{handler: \'iframe\', size: {x: 800, y: 500}}"><img src="'.\JUri::root().'media/com_invoicing/images/menu/search.gif" border="0" /></a>';
		}
		if($include_add) {
			$html .= '<a class="modal" id="add'.$name.'" href="index.php?option=com_invoicing&view=vendor&task=add&layout=modal&tmpl=component&amp;field='.$name.'" rel="{handler: \'iframe\', size: {x: 800, y: 500}}"><img src="'.\JUri::root().'media/com_invoicing/images/menu/add.png" border="0" /></a>';
		}
		return $html;
	}
	
	//Helper for Coupons with title and codecoupon
	public static function coupons($name = 'coupon_id', $selected = '', $attribs = array())
	{
		$model = InvoicingModelCoupons::getInstance('Coupons', 'InvoicingModel');
		$db = \JFactory::getDbo();
		$query = $db->getQuery(true)
		->select('*')
		->from($db->qn('#__invoicing_coupons'))
		->order($db->qn('ordering') . 'ASC');

		$list = $db->setQuery($query)->loadObjectList();

		$options   = array();
	
		$include_none = false;
		$include_all = false;
		$include_add = false;
		$include_find = false;
		$add_data_attribs = false;
		if(array_key_exists('include_none', $attribs)) {
			$include_none = $attribs['include_none'];
			unset($attribs['include_none']);
		}
		if(array_key_exists('include_all', $attribs)) {
			$include_all = $attribs['include_all'];
			unset($attribs['include_all']);
		}
		if(array_key_exists('include_add', $attribs)) {
			$include_add = $attribs['include_add'];
			unset($attribs['include_add']);
		}
		if(array_key_exists('include_find', $attribs)) {
			$include_find = $attribs['include_find'];
			unset($attribs['include_find']);
		}
		if(array_key_exists('add_data_attribs', $attribs)) {
			$add_data_attribs = $attribs['add_data_attribs'];
			unset($attribs['add_data_attribs']);
		}

		if($include_all) {
			$selectoption = ("" == $selected) ? 'selected="selected"': "";
			$options[] = '<option value="" '.$selectoption.'></option>';
		}
		$selectoption = ("-1" == $selected) ? 'selected="selected"': "";
		$options[] = '<option value="-1" '.$selectoption.'>'.htmlspecialchars(\JText::_('INVOICING_CUSTOM_DISCOUNT'))."</option>";
	
		foreach($list as $item) {		
			$selectoption = ($item->invoicing_coupon_id == $selected) ? 'selected="selected"': "";
			if ($add_data_attribs == true) {
				$attrib = ' data-valuetype="'.$item->valuetype.'" data-couponvalue="'.$item->value.'" ';
				$options[] = '<option '.$selectoption.' '.$attrib.' value="'.$item->invoicing_coupon_id.'">'.htmlspecialchars($item->title." (".$item->code.")")."</option>";
			} else {
				$options[] = '<option '.$selectoption.' value="'.$item->invoicing_coupon_id.'">'.htmlspecialchars($item->title." (".$item->code.")")."</option>";
			}
		}

		$attrib = '';
		foreach($attribs as $key => $val) {
			$attrib .= " ".$key."=\"".$val."\" ";
		}
		
		$html = '<select '.$attrib.' name="'.$name.'" id="'.$name.'">';
		$html .= implode("\n",$options);
		$html .= '</select>';

		if($include_find) {
			$html .= '<a class="modal" id="add'.$name.'" href="index.php?option=com_invoicing&view=coupons&layout=modal&tmpl=component&amp;field='.$name.'" rel="{handler: \'iframe\', size: {x: 800, y: 500}}"><img src="'.\JUri::root().'media/com_invoicing/images/menu/search.gif" border="0" /></a>';
		}
		if($include_add) {
			$html .= '<a class="modal" id="add'.$name.'" href="index.php?option=com_invoicing&view=coupon&task=add&layout=modal&tmpl=component&amp;field='.$name.'" rel="{handler: \'iframe\', size: {x: 800, y: 500}}"><img src="'.\JUri::root().'media/com_invoicing/images/menu/add.png" border="0" /></a>';
		}
		return $html;
	}
	
	//Helper for currencies with code and symbol
	public static function currencies($name = 'currency_id', $selected = '', $attribs = array())
	{
		$db = \JFactory::getDbo();
		$query = $db->getQuery(true)
		->select('*')
		->from($db->qn('#__invoicing_currencies'))
		->where($db->qn('enabled') . ' = ' . $db->q(1))
		->order($db->qn('ordering') . 'ASC');

		$list = $db->setQuery($query)->loadObjectList();
	
		$options   = array();
	
		$include_none = false;
		$include_all = false;
		$include_add = false;
		$include_find = false;
		if(array_key_exists('include_none', $attribs)) {
			$include_none = $attribs['include_none'];
			unset($attribs['include_none']);
		}
		if(array_key_exists('include_all', $attribs)) {
			$include_all = $attribs['include_all'];
			unset($attribs['include_all']);
		}
		if(array_key_exists('include_add', $attribs)) {
			$include_add = $attribs['include_add'];
			unset($attribs['include_add']);
		}
		if(array_key_exists('include_find', $attribs)) {
			$include_find = $attribs['include_find'];
			unset($attribs['include_find']);
		}
	
		if($include_none) {
			$options[] = \JHTML::_('select.option','-1',\JText::_('INVOICING_COMMON_SELECTLEVEL_NONE'));
		}
		if($include_all) {
			$options[] = \JHTML::_('select.option','0',\JText::_('INVOICING_COMMON_SELECTLEVEL_ALL'));
		}
		/*if(!$include_none && !$include_all) {
			$options[] = \JHTML::_('select.option','','- '.\JText::_('INVOICING_COMMON_SELECT').' -');
		}*/
		foreach($list as $item) {
			$options[] = \JHTML::_('select.option',$item->invoicing_currency_id,$item->code." (".$item->symbol.")");
		}
		
		

		$html = self::genericlist($options, $name, $attribs, $selected, $name);

		if($include_find) {
			$html .= '<a class="modal" id="add'.$name.'" href="index.php?option=com_invoicing&view=currencies&layout=modal&tmpl=component&amp;field='.$name.'" rel="{handler: \'iframe\', size: {x: 800, y: 500}}"><img src="'.\JUri::root().'media/com_invoicing/images/menu/search.gif" border="0" /></a>';
		}
		if($include_add) {
			$html .= '<a class="modal" id="add'.$name.'" href="index.php?option=com_invoicing&view=currency&task=add&layout=modal&tmpl=component&amp;field='.$name.'" rel="{handler: \'iframe\', size: {x: 800, y: 500}}"><img src="'.\JUri::root().'media/com_invoicing/images/menu/add.png" border="0" /></a>';
		}
		return $html;
	}
	//Helper for invoices ID and amount
	public static function invoices($name = 'invoice_id', $selected = '', $attribs = array())
	{
		$model = InvoicingModelInvoices::getInstance('Invoices', 'InvoicingModel');
		$list = $model->savestate(0)
		->filter_order('ordering')
		->filter_order_Dir('ASC')
		->limit(0)
		->offset(0)
		->getList();
	
		$options   = array();
	
		$include_none = false;
		$include_all = false;
		$include_add = false;
		$include_find = false;
		if(array_key_exists('include_none', $attribs)) {
			$include_none = $attribs['include_none'];
			unset($attribs['include_none']);
		}
		if(array_key_exists('include_all', $attribs)) {
			$include_all = $attribs['include_all'];
			unset($attribs['include_all']);
		}
		if(array_key_exists('include_add', $attribs)) {
			$include_add = $attribs['include_add'];
			unset($attribs['include_add']);
		}
		if(array_key_exists('include_find', $attribs)) {
			$include_find = $attribs['include_find'];
			unset($attribs['include_find']);
		}
	
		if($include_none) {
			$options[] = \JHTML::_('select.option','-1',\JText::_('INVOICING_COMMON_SELECTLEVEL_NONE'));
		}
		if($include_all) {
			$options[] = \JHTML::_('select.option','0',\JText::_('INVOICING_COMMON_SELECTLEVEL_ALL'));
		}
		if(!$include_none && !$include_all) {
			$options[] = \JHTML::_('select.option','','- '.\JText::_('INVOICING_COMMON_SELECT').' -');
		}
	
		foreach($list as $item) {
			$options[] = \JHTML::_('select.option',$item->invoicing_invoice_id,$item->user_id." (".$item->net_amount.")");
		}
		
		$html = self::genericlist($options, $name, $attribs, $selected, $name);
		
		if($include_find) {
			$html .= '<a class="modal" id="add'.$name.'" href="index.php?option=com_invoicing&view=invoices&layout=modal&tmpl=component&amp;field='.$name.'" rel="{handler: \'iframe\', size: {x: 800, y: 500}}"><img src="'.\JUri::root().'media/com_invoicing/images/menu/search.gif" border="0" /></a>';
		}
		if($include_add) {
			$html .= '<a class="modal" id="add'.$name.'" href="index.php?option=com_invoicing&view=invoice&task=add&layout=modal&tmpl=component&amp;field='.$name.'" rel="{handler: \'iframe\', size: {x: 800, y: 500}}"><img src="'.\JUri::root().'media/com_invoicing/images/menu/add.png" border="0" /></a>';
		}
		return $html;
	}

	public static function taxes($name = 'tax_id', $selected = '', $attribs = array())
	{
		$db = \JFactory::getDbo();
		$query = $db->getQuery(true)
		->select('*')
		->from($db->qn('#__invoicing_taxes'))
		->where($db->qn('enabled') . ' = ' . $db->q(1))
		->order($db->qn('ordering') . 'ASC');

		$list = $db->setQuery($query)->loadObjectList();
	
		$options   = array();
	
		$include_none = false;
		$include_all = false;
		$include_add = false;
		$include_find = false;
		if(array_key_exists('include_none', $attribs)) {
			$include_none = $attribs['include_none'];
			unset($attribs['include_none']);
		}
		if(array_key_exists('include_all', $attribs)) {
			$include_all = $attribs['include_all'];
			unset($attribs['include_all']);
		}
		if(array_key_exists('include_add', $attribs)) {
			$include_add = $attribs['include_add'];
			unset($attribs['include_add']);
		}
		if(array_key_exists('include_find', $attribs)) {
			$include_find = $attribs['include_find'];
			unset($attribs['include_find']);
		}
		if (array_key_exists('id', $attribs)) {
			$id = $attribs['id'];
			unset($attribs['id']);
		} else {
			$id = $name;
		}
			
		$options[] = \JHTML::_('select.option','0',\JText::_('INVOICING_NO_TAX'));
	
		foreach($list as $item) {
			$options[] = \JHTML::_('select.option',$item->taxrate,$item->taxrate);
		}
		
		$html = self::genericlist($options, $name, $attribs, $selected, $id);

		if($include_find) {
			$html .= '<a class="modal" id="add'.$name.'" href="index.php?option=com_invoicing&view=taxes&layout=modal&tmpl=component&amp;field='.$name.'" rel="{handler: \'iframe\', size: {x: 800, y: 500}}"><img src="'.\JUri::root().'media/com_invoicing/images/menu/search.gif" border="0" /></a>';
		}
		if($include_add) {
			$html .= '<a class="modal" id="add'.$name.'" href="index.php?option=com_invoicing&view=tax&task=add&layout=modal&tmpl=component&amp;field='.$name.'" rel="{handler: \'iframe\', size: {x: 800, y: 500}}"><img src="'.\JUri::root().'media/com_invoicing/images/menu/add.png" border="0" /></a>';
		}
		return $html;
	}
	
	public static function joomlausers($name = 'user_id', $selected = '', $attribs = array())
	{
		$db = \JFactory::getDBO();
		
		$query = $db->getQuery(true)
			->select('u.name,u.username,u.id')
			->from("#__users as u")
			->where("u.id NOT IN (SELECT DISTINCT user_id FROM #__invoicing_users)")
			->order("u.username ASC");
		$db->setQuery($query);
		$users = $db->loadObjectList();
		$options = array();
		$options[] = \JHTML::_('select.option','','- '.\JText::_('INVOICING_COMMON_SELECT_USER').' -');
		foreach($users as $user) {
			$options[] = \JHTML::_('select.option',$user->id,$user->username." (".$user->name.")");
		}
		
		$html = self::genericlist($options, $name, $attribs, $selected, $name);
		return $html;
	}

	public static function users($name = 'user_id', $selected = '', $attribs = array())
	{
		$model = InvoicingModelUsers::getInstance('Users', 'InvoicingModel');
		$db = \JFactory::getDbo();
		$query = $db->getQuery(true)
		->select('*')
		->from($db->qn('#__invoicing_users'))
		->order($db->qn('businessname') . 'ASC');

		$list = $db->setQuery($query)->loadObjectList();
	
		$options   = array();
	
		$include_none = false;
		$include_all = false;
		$include_add = false;
		$include_find = false;
		if(array_key_exists('include_none', $attribs)) {
			$include_none = $attribs['include_none'];
			unset($attribs['include_none']);
		}
		if(array_key_exists('include_all', $attribs)) {
			$include_all = $attribs['include_all'];
			unset($attribs['include_all']);
		}
		if(array_key_exists('include_add', $attribs)) {
			$include_add = $attribs['include_add'];
			unset($attribs['include_add']);
		}
		if(array_key_exists('include_find', $attribs)) {
			$include_find = $attribs['include_find'];
			unset($attribs['include_find']);
		}
	
		if($include_none) {
			$options[] = \JHTML::_('select.option','-1',\JText::_('INVOICING_COMMON_SELECTLEVEL_NONE'));
		}
		if($include_all) {
			$options[] = \JHTML::_('select.option','0',\JText::_('INVOICING_COMMON_SELECTLEVEL_ALL'));
		}
		if(!$include_none && !$include_all) {
			$options[] = \JHTML::_('select.option','','- '.\JText::_('INVOICING_COMMON_SELECT_USER').' -');
		}
	
		foreach($list as $item) {
			$options[] = \JHTML::_('select.option',$item->invoicing_user_id,$item->businessname." / ".@$item->username);
		}
		
		$html = self::genericlist($options, $name, $attribs, $selected, $name);
		
		if($include_find) {
			$html .= '&nbsp;<a class="modal" id="add'.$name.'" href="index.php?option=com_invoicing&view=users&layout=modal&tmpl=component&amp;field='.$name.'" rel="{handler: \'iframe\', size: {x: 800, y: 500}}"><img src="'.\JUri::root().'media/com_invoicing/images/menu/search.gif" border="0" /></a>';
		}
		if($include_add) {
			$html .= '&nbsp;<a class="modal" id="add'.$name.'" href="index.php?option=com_invoicing&view=user&task=add&layout=modal&tmpl=component&amp;field='.$name.'" rel="{handler: \'iframe\', size: {x: 800, y: 500}}"><img src="'.\JUri::root().'media/com_invoicing/images/menu/add.png" border="0" /></a>';
		}
		
		$html = '<span class="selector">'.$html.'</span>';
		
		return $html;
	}
	
	public static function items($tableName, $primaryKey, $selected = '', $nameField1 = null, $nameField2 = null, $attribs = array() )
	{
	
		echo $tableName."-";
		echo $primaryName."-";
		echo $selected."-";
		echo $nameField1."-";
		echo $nameField2;
		
		//Extract name of the primary key of the table after the first _
		$pos = strpos($primaryKey,'_');
		$project = ucfirst(substr($primaryKey, 0, $pos));
		$name = substr($primaryKey, $pos);
		
		//$pos must be equal 11, $project = Invoicing and $name = tablename_id
		$model = $tableName::getInstance();
		$list = $model->savestate(0)
		->filter_order('ordering')
		->filter_order_Dir('ASC')
		->limit(0)
		->offset(0)
		->getList();
	
		$options   = array();
	
		$include_none = false;
		$include_all = false;
		$include_add = false;
		$include_find = false;
		if(array_key_exists('include_none', $attribs)) {
			$include_none = $attribs['include_none'];
			unset($attribs['include_none']);
		}
		if(array_key_exists('include_all', $attribs)) {
			$include_all = $attribs['include_all'];
			unset($attribs['include_all']);
		}
		if(array_key_exists('include_add', $attribs)) {
			$include_add = $attribs['include_add'];
			unset($attribs['include_add']);
		}
		if(array_key_exists('include_find', $attribs)) {
			$include_find = $attribs['include_find'];
			unset($attribs['include_find']);
		}
	
		if($include_none) {
			$options[] = \JHTML::_('select.option','-1',\JText::_('INVOICING_COMMON_SELECTLEVEL_NONE'));
		}
		if($include_all) {
			$options[] = \JHTML::_('select.option','0',\JText::_('INVOICING_COMMON_SELECTLEVEL_ALL'));
		}
		if(!$include_none && !$include_all) {
			$options[] = \JHTML::_('select.option','','- '.\JText::_('INVOICING_COMMON_SELECT').' -');
		}
		
		if ($nameField2 == NULL) {
			foreach($list as $item) {
				$options[] = \JHTML::_('select.option',$item->$primaryKey);
			}
		}
		else {
			foreach($list as $item) {
				$options[] = \JHTML::_('select.option',$item->$primaryKey,$item->$nameField1." (".$item->$nameField2.")");
			}
		}
		
		$html = self::genericlist($options, $name, $attribs, $selected, $name);
		
		if($include_find) {
			$html .= '<a class="modal" id="add'.$name.'" href="index.php?option=com_invoicing&view=vendors&layout=modal&tmpl=component&amp;field='.$name.'" rel="{handler: \'iframe\', size: {x: 800, y: 500}}"><img src="'.\JUri::root().'media/com_invoicing/images/menu/search.gif" border="0" /></a>';
		}
		if($include_add) {
			$html .= '<a class="modal" id="add'.$name.'" href="index.php?option=com_invoicing&view=vendor&task=add&layout=modal&tmpl=component&amp;field='.$name.'" rel="{handler: \'iframe\', size: {x: 800, y: 500}}"><img src="'.\JUri::root().'media/com_invoicing/images/menu/add.png" border="0" /></a>';
		}
		return $html;
	}
	
	public static function itemtypes($name = 'apply_on', $selected = '', $attribs = array()) 
	{
		jimport('joomla.plugin.helper');
		\JPluginHelper::importPlugin('invoicinggenerator');
		$app = \JFactory::getApplication();
		$jResponse = $app->triggerEvent('getItemTypes');
		$list = array();
		if (is_array($jResponse)) {
			foreach($jResponse as $sublist) {
				$list = array_merge($list,$sublist);
			}
		}
		
		$options   = array();
		$options[] = \JHTML::_('select.option','0',\JText::_('INVOICING_COMMON_SELECT_ALL'));
		
		foreach($list as $value =>$label) {
			$options[] = \JHTML::_('select.option',$value,$label);
		}
		
		$multiple = false;
		if(array_key_exists('multiple', $attribs)) {
			$multiple = $attribs['multiple'];
			unset($attribs['multiple']);
		}
		
		if ($multiple == true) {
			$id = $name;
			$name = $name."[]";
			$attribs["multiple"]="multiple";
			$size = count($options);
			if ($size > 20) 
				$size = 20;
			$attribs["size"] = $size;
		} else {
			$id = $name;
		}
		
		return self::genericlist($options, $name, $attribs, $selected, $id);
	}
	
	public static function formatCountry($country = '',$empty="&mdash;")
	{
 		if(array_key_exists($country, self::$countries)) {
 			$name = \JText::_(self::$countries[$country]);
 		} else if ($country != "") {
 			$name = $country;
 		} else {
 			$name = $empty;
 		}
 		
 		return $name; 
	}

}
