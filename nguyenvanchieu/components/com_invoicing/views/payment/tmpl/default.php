<?php
/**
 * @package		Invoicing
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license GNU General Public License version 3, or later
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>
<?php echo $this->form ?>
<?php
	$script = "
	jQ(document).ready(function() {
		if (jQ('#paymentForm')) {
			jQ('#paymentForm').submit();
		}
	});";

	\JFactory::getApplication()->getDocument()->addScriptDeclaration($script);
	