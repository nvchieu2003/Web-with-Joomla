<?php
/**
 *  @package Invoicing
 *  @copyright Copyright (c)2012 JoomPROD
 *  @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

include_once('cparams.php');
include_once('load.php');
include_once('select.php');

class InvoicingHelperDates
{	
	/**
	 * 
	 * @param int $start (number of first month concerned, example : April = 4)
	 * @param int $end (number of last month)
	 * @return table with names of months in eng
	 */
	public static function getMonthsBetween($start, $end, $yearstart, $yearend)
	{	
		if($start > $end && $yearstart == $yearend)
		{
			return false;
		}    
		
		$january = 1;
		$december = 12;
		$cpt = 0;
		 
		if ($yearstart == $yearend) {
			for ($j = $start ; $j <= $end ; $j++) {
				$names[$cpt] = date("Y-m",mktime(0,0,0,$j,22,$yearstart));
				$cpt++;
			}
		}
		
		else {
		
			for ($j = $start ; $j <= $december ; $j++) {
				$names[$cpt] = date("Y-m",mktime(0,0,0,$j,22,$yearstart));
				$cpt++;
			}

			for ($y = $yearstart + 1 ; $y < $yearend ; $y++) {
				for ($i=$january;$i<=$december;$i++) {
					$i = (int)$i;
					$names[$cpt] = date("Y-m",mktime(0,0,0,$i,22,$y));
					$cpt++;
				}
			}
			
			for ($j = $january ; $j <=$end ; $j++) {
				$names[$cpt] = date("Y-m",mktime(0,0,0,$j,22,$yearend));
				$cpt++;
			}
		}

		return $names;
	}
	 	 
	 /**
	 * 
	 * @param int $start ( first date concerned, example : '2013-04-23')
	 * @param int $end (last date)
	 * @return table with all dates between, start and end included.
	 */
	public static function getDatesBetween($start, $end)
	{
		if($start > $end)
		{
			return false;
		}    
	   
		$sdate    = strtotime($start);
		$edate    = strtotime($end);
	   
		$dates = array();
	   
		for($i = $sdate; $i <= $edate; $i += strtotime('+1 day', 0))
		{
			$dates[] = date('Y-m-d', $i);
		}
	   
		return $dates;
	}
	
	/**
	 * 
	 * @param table $references ( table of dates with format 'Y-m-d')
	 * @param table $queryresult ( table with result of paid invoices found by day)
	 * @return table with 0 when no day concerned in the $queryresult
	 */
	public static function fillDatesWithZero($references,$queryresult) {

		$result = array();
		$i = 0;

		$sums = array();
		foreach($queryresult as $s) {
			$sums[$s->date]= $s->sum;
		}

		foreach($references as $ref) {
			if (isset($sums[$ref])) {
				$result[] = array(strftime("%d %b",strtotime($ref)),round((float)$sums[$ref],2));
			} else {
				$result[] = array(strftime("%d %b",strtotime($ref)),(float)0);
			}
		}
		return ($result);
	}
	
	/**
	 * 
	 * @param table $references ( table of names of months like [1] -> 'January')
	 * @param table $queryresult ( table with result of paid invoices found by month)
	 * @return table with 0 when no month concerned in the $queryresult
	 */
	public static function fillMonthsWithZero($references,$queryresult) {
		$result = array();

		$sums = array();
		
		
			foreach($queryresult as $s) {
				$sums[date('Y-m',strtotime($s->date))]= $s->sum;
			}
	
			foreach($references as $ref) {
				if (isset($sums[$ref])) {
					$result[] = array($ref,round((float)$sums[$ref],2));
				} else {
					$result[] = array($ref,(float)0);
				}
			}
		
		return ($result); 
	}
}
