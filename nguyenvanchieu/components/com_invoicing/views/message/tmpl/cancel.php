<?php
/**
 * @package		Invoicing
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license GNU General Public License version 3, or later
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>
<div class="row-fluid">
    <div class="span12">
<table class="contentpaneopen">
	<tr>
		<td class="contentheading" width="100%"><?php echo \JText::_('INVOICING_TITLE'); ?></td>
	</tr>
</table>
<p><b><font color="#FF0000"><?php echo \JText::_('INVOICING_CANCELLED'); ?></font></b><br />
<br />
<?php echo \JText::_('INVOICING_TRY_LATER'); ?></p>
        <?php echo \JText::_('INVOICING_FOOTER'); ?>
    </div>
</div>