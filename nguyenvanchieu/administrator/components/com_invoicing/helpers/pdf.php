<?php
/**
 *  @package Invoicing
 *  @copyright Copyright (c)2012 JoomPROD
 *  @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/cparams.php');

class InvoicingHelperPDF		
{		

	/**
	 * Create a PDF representation of an invoice.
	 *
	 * @return  string  The (mangled) filename of the PDF file
	 */
	static public function createPDF($data,$filename,$stream = false)
	{
		// Repair the input HTML
		if (function_exists('tidy_repair_string'))
		{
			$tidyConfig = array(
				'bare'							=> 'yes',
				'clean'							=> 'yes',
				'drop-proprietary-attributes'	=> 'yes',
				'clean'							=> 'yes',
				'output-html'					=> 'yes',
				'show-warnings'					=> 'no',
				'ascii-chars'					=> 'no',
				'char-encoding'					=> 'utf8',
				'input-encoding'				=> 'utf8',
				'output-bom'					=> 'no',
				'output-encoding'				=> 'utf8',
				'force-output'					=> 'yes',
				'tidy-mark'						=> 'no',
				'wrap'							=> 0,
			);
			$repaired = tidy_repair_string($data, $tidyConfig, 'utf8');
			if ($repaired !== false)
			{
				$data = $repaired;
			}
		}
		
		// Fix any relative URLs in the HTML
		$data = self::fixURLs($data);
		
		//echo "<pre>" . htmlentities($invoiceRecord->html) . "</pre>"; die();

		// Create the PDF
		$pdf = self::initTCPDF();
		$pdf->AddPage();
		$pdf->writeHTML($data, true, false, true, false, '');
		$pdf->lastPage();

		if($stream) {
			$outputName = explode('/',$filename);
			$outputName = end($outputName);
			
			$pdfData = $pdf->Output($outputName.'.pdf', 'D');

			//unset($pdf);
		} else {
			$pdfData = $pdf->Output('', 'S');

			unset($pdf);

			// Write the PDF data to disk using JFile::write();
			\JLoader::import('joomla.filesystem.file');
			$name = $filename;
			
			$ret = \JFile::write($name, $pdfData);
			
			if ($ret)
			{
				// return the name of the file
				return $name;
			}
			else
			{
				return false;
			}
		}
	}

	static public function initTCPDF()
	{
		jimport('joomla.application.component.helper');
		$params = \JComponentHelper::getParams('com_invoicing');
		// Load PDF signing certificates
		$certificateFile = $params->get('invoice_certificatefile', 'certificate.cer');//InvoicingHelperCparams::getParam('invoice_certificatefile', 'certificate.cer');
		$secretKeyFile = $params->get('invoice_secretkeyfile', 'secret.cer');//InvoicingHelperCparams::getParam('invoice_secretkeyfile', 'secret.cer');
		$secretKeyPass = $params->get('invoice_secretkeypass', '');//InvoicingHelperCparams::getParam('invoice_secretkeypass', '');
		$extraCertFile = $params->get('invoice_extracert', 'extra.cer');//InvoicingHelperCparams::getParam('invoice_extracert', 'extra.cer');

		$certificate = '';
		$secretkey = '';
		$extracerts = '';

		$path = JPATH_ADMINISTRATOR . '/components/com_invoicing/assets/tcpdf/certificates/';
		if (\JFile::exists($path.$certificateFile))
		{
			$certificate = \JFile::read($path.$certificateFile);
		}
		if (!empty($certificate))
		{
			if (\JFile::exists($path.$secretKeyFile))
			{
				$secretkey = \JFile::read($path.$secretKeyFile);
			}
			if (empty($secretkey))
			{
				$secretkey = $certificate;
			}

			if (\JFile::exists($path.$extraCertFile))
			{
				$extracerts = \JFile::read($path.$extraCertFile);
			}
			if (empty($extracerts))
			{
				$extracerts = '';
			}
		}

		// Set up TCPDF
		$jreg = \JFactory::getConfig();
		$tmpdir = $jreg->get('tmp_path');
		$tmpdir = rtrim($tmpdir, '/' . DIRECTORY_SEPARATOR) . '/';
		$siteName = $jreg->get('sitename');

		$baseurl = \JURI::base();
		$baseurl = rtrim($baseurl, '/');

		define('K_TCPDF_EXTERNAL_CONFIG', 1);

		define ('K_PATH_MAIN', JPATH_BASE . '/');
		define ('K_PATH_URL', $baseurl);
		define ('K_PATH_FONTS', JPATH_ROOT.'/media/com_invoicing/tcpdf/fonts/');
		define ('K_PATH_CACHE', $tmpdir);
		define ('K_PATH_URL_CACHE', $tmpdir);
		define ('K_PATH_IMAGES', JPATH_ROOT.'/media/com_invoicing/tcpdf/images/');
		define ('K_BLANK_IMAGE', K_PATH_IMAGES.'_blank.png');
		define ('PDF_PAGE_FORMAT', 'A4');
		define ('PDF_PAGE_ORIENTATION', 'P');
		define ('PDF_CREATOR', 'Invoicing');
		define ('PDF_AUTHOR', $siteName);
		define ('PDF_UNIT', 'mm');
		define ('PDF_MARGIN_HEADER', 5);
		define ('PDF_MARGIN_FOOTER', 10);
		define ('PDF_MARGIN_TOP', 27);
		define ('PDF_MARGIN_BOTTOM', 25);
		define ('PDF_MARGIN_LEFT', 15);
		define ('PDF_MARGIN_RIGHT', 15);
		define ('PDF_FONT_NAME_MAIN', 'dejavusans');
		define ('PDF_FONT_SIZE_MAIN', 8);
		define ('PDF_FONT_NAME_DATA', 'dejavusans');
		define ('PDF_FONT_SIZE_DATA', 8);
		define ('PDF_FONT_MONOSPACED', 'dejavusansmono');
		define ('PDF_IMAGE_SCALE_RATIO', 1.25);
		define('HEAD_MAGNIFICATION', 1.1);
		define('K_CELL_HEIGHT_RATIO', 1.25);
		define('K_TITLE_MAGNIFICATION', 1.3);
		define('K_SMALL_RATIO', 2/3);
		define('K_THAI_TOPCHARS', true);
		define('K_TCPDF_CALLS_IN_HTML', false);

		require_once JPATH_ADMINISTRATOR . '/components/com_invoicing/assets/tcpdf/tcpdf.php';

		$pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor(PDF_AUTHOR);
		$pdf->SetTitle('Invoice');
		$pdf->SetSubject('Invoice');

		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		$pdf->setHeaderFont(array('dejavusans', '', 8, '', false));
		$pdf->setFooterFont(array('dejavusans', '', 8, '', false));
		$pdf->SetFont('dejavusans', '', 8, '', false);

		if (!empty($certificate))
		{
			$pdf->setSignature($certificate, $secretkey, $secretKeyPass, $extracerts);
		}

		return $pdf;
	}
	
	static public function fixURLs($buffer)
	{
		$pattern = '/(href|src)=\"([^"]*)\"/i';
		$number_of_matches = preg_match_all($pattern, $buffer, $matches, PREG_OFFSET_CAPTURE);

		if($number_of_matches > 0) {
			$substitutions = $matches[2];
			$last_position = 0;
			$temp = '';

			// Loop all URLs
			foreach($substitutions as &$entry)
			{
				// Copy unchanged part, if it exists
				if($entry[1] > 0)
					$temp .= substr($buffer, $last_position, $entry[1]-$last_position);
				// Add the new URL
				$temp .= self::replaceDomain($entry[0]);
				// Calculate next starting offset
				$last_position = $entry[1] + strlen($entry[0]);
			}
			// Do we have any remaining part of the string we have to copy?
			if($last_position < strlen($buffer))
				$temp .= substr($buffer, $last_position);

			return $temp;
		}

		return $buffer;
	}
	
	static public function replaceDomain($url)
	{
		static $mydomain = null;
		static $domainlen = null;

		if(empty($mydomain))
		{
			$mydomain = JURI::base(false);
			if(substr($mydomain,-1) == '/') $mydomain = substr($mydomain,0,-1);
			if(substr($mydomain,-13) == 'administrator') $mydomain = substr($mydomain,0,-13);

			$domainlen = strlen($mydomain);
		}

		// Do we have a domain name?
		if(substr($url, 0, 7) == 'http://')
		{
			return $url;
		}
		if(substr($url, 0, 8) == 'https://')
		{
			return $url;
		}

		return $mydomain . '/' . ltrim($url, '/');
	}

    static function generate($data,$filename) {
	$pdf = self::iniDomPdf();
		$data = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml"><body>'.
										 $data.
										 '</body></html>';
		
					self::fullPaths($data);
				   
					// TODO Voir s'il faut vraiment ca, mais ca gene la page traitement
					/*do
					{
					} while(@ob_end_clean());*/
					
					
					
					$pdf->load_html($data);
					$pdf->render();					
					$attachment = $pdf->output();					

		// Write the contents to the file
		file_put_contents($filename, $attachment);

	}
	
	static function stream($data,$filename) {
	
	$pdf = self::iniDomPdf();
		$data = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml"><body>'.
										 $data.
										 '</body></html>';
					self::fullPaths($data);
				   
					do
					{
					} while(@ob_end_clean());
					
					$pdf->load_html($data);
					$pdf->render();
					$pdf->stream($filename);
					
	
	}

	static protected function iniDomPdf(){
		static $engine;
		if ($engine == null) {
			$file = JPATH_LIBRARIES .'/dompdf/dompdf_config.inc.php';
			if (!JFile::exists($file))
			{
					return false;
			}
			if (!defined('DOMPDF_ENABLE_REMOTE'))
			{
					define('DOMPDF_ENABLE_REMOTE', true);
			}
			//set the font cache directory to Joomla's tmp directory
			$config = \JFactory::getConfig();
			if (!defined('DOMPDF_FONT_CACHE'))
			{
					define('DOMPDF_FONT_CACHE', $config->get('tmp_path'));
			}
			require_once($file);
			// Default settings are a portrait layout with an A4 configuration using millimeters as units	
			$engine = new DOMPDF();	
		}
		return $engine;
	}
	
		/**
	 * parse relative images a hrefs and style sheets to full paths
	 * @param       string  &$data
	 */
 
    static protected function fullPaths(&$data)
        {
			$data = str_replace("&nbsp;"," ",$data);
			$data = str_replace("&","&amp;",$data);
			$data = str_replace("&amp;lt;","&lt;",$data);
			$data = str_replace("&amp;gt;","&gt;",$data);
			$data = str_replace("&amp;quot;","&quot;",$data);
			$data = str_replace('xmlns=', 'ns=', $data);
		   // var_dump($data);
			libxml_use_internal_errors(true);
			try
			{
					$ok = new SimpleXMLElement($data);
					if ($ok)
					{
							$uri = JUri::getInstance();
							$base = $uri->getScheme() . '://' . $uri->getHost();
							$imgs = $ok->xpath('//img');
							foreach ($imgs as &$img)
							{
									if (!strstr($img['src'], $base))
									{
											$img['src'] = $base . $img['src'];
									}
                                    $img['src'] = str_replace($base,JPATH_ROOT,$img['src']);
							}
							//links
							$as = $ok->xpath('//a');
							foreach ($as as &$a)
							{
									if (!strstr($a['href'], $base))
									{
											$a['href'] = $base . $a['href'];
									}
							}

							// css files.
							$links = $ok->xpath('//link');
							foreach ($links as &$link)
							{
									if ($link['rel'] == 'stylesheet' && !strstr($link['href'], $base))
									{
											$link['href'] = $base . $link['href'];
									}
							}
							$data = $ok->asXML();
					}
			} catch (Exception $err)
			{
					//oho malformed html - if we are debugging the site then show the errors
					// otherwise continue, but it may mean that images/css/links are incorrect
					$errors = libxml_get_errors();
					if (JDEBUG)
					{
							echo "<pre>";print_r($errors);echo "</pre>";
							exit;
					} 
			}
 
        }
}