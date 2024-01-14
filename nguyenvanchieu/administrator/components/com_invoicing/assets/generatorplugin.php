<?php
/**
 * @package		invoicing
 * @copyright	Copyright (c)2010-2012 JoomPROD
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die();

jimport('joomla.plugin.plugin');
jimport('joomla.html.parameter');

/**
 * Invoicing abstract generator plugin class
 */
abstract class InvoicingAbstractGeneratorPlugin extends JPlugin
{
	/**
	 * This function is called when an invoice has been validated. In order to perform an action in the "generator component" of
	 * this invoice. Example, Adsmanager generate an invoice for a new ad submission, on invoice validation, we need to activate the ad
	 * 
	 * @param string $generatorname Check it against generator Name
	 * @param array $generator_key The Reference Key for the generator (the ad id for example in adsmanager)
	 * @return string
	 */
	abstract public function onInvoicingPaymentValidation($invoice);
}