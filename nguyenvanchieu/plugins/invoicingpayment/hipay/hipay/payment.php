<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>
<form name="payment" action="<?php echo $baseurl.'/index.php?option=com_paidsystem&tmpl=component&task=payment&no_html=1&type=hipay' ?>" method="post" onsubmit="javascript: return checkform();">
<input type="hidden" name="order_id" value="<?php echo $this->order_id;?>" />
<input type="submit" class="button" value="<?php echo sprintf(\JText::_('PAIDSYSTEM_PAY_WITH'),'Hipay')?>" /></form>