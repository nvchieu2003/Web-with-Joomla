<?php
/**
 * @package		Invoicing
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license GNU General Public License version 3, or later
 */


defined('_JEXEC') or die();

//JHtml::_('behavior.modal');
//$this->loadHelper('Load');
$input = \JFactory::getApplication()->input;
if ($input->getInt('print', 0) == 1 ) {
?>

<div id="pic_actions">
				 <a id="print_pic_actions" href="#" onclick="window.print();return false;">
				 <span class="icon icon-print"></span>
				</a>
				</div>
	<?php 
}
echo $this->content;




