<?php
/**
 * @package Invoicing
 * @copyright Copyright (c)203 Juloa.com
 * @license GNU General Public License version 3, or later
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/models/default.php');

/**
 * Invoicing Component Controller
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class InvoicingModelTemplates extends InvoicingModelDefault {
	protected $_tableName = "templates";
	protected $_fieldId = "invoicing_template_id";

	public function onBeforeSave(&$data) {
		$input = \JFactory::getApplication()->input;
		$data->htmlcontent = $input->get('htmlcontent', '', 'RAW');
		$data->usehtmlforpdf = (int)$data->usehtmlforpdf;
		if ($data->usehtmlforpdf == 1) {
			$data->pdfcontent = $data->htmlcontent;
		} else {
			$data->pdfcontent = $input->get('pdfcontent', '', 'RAW');
		}

		return true;
	}
} 