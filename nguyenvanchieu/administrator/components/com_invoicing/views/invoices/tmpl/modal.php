<?php
/**
 * @package		Invoicing
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license GNU General Public License version 3, or later
 */

	defined('_JEXEC') or die();

	//JHtml::_('behavior.modal');

	$input = \JFactory::getApplication()->input;
	$id = $input->getInt('id', 0);

	$status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';
	$url = 'index.php?option=com_invoicing&view=invoice&task=read&tmpl=component&print=1&id='.$id;
	$text = JHtml::_('image', 'system/printButton.png', \JText::_('JGLOBAL_PRINT'), NULL, true);
	$attribs['title']	= \JText::_('JGLOBAL_PRINT');
	$attribs['onclick'] = "window.open(this.href,'win2','".$status."'); return false;";
	$attribs['rel']		= 'nofollow';
	
	$content = '';

	ob_start();

?>

<div id="pic_actions">
				 <a id="print_pic_actions" href="#" onclick="window.print();return false;";>
				<span class="icon icon-print"></span></a>
				</div>
	<?php 
	//echo JHtml::_('link', \JRoute::_($url), $text, $attribs);
	echo $this->content;

	$content = ob_get_contents();
	ob_end_clean();

	echo HTMLHelper::_(
		'bootstrap.renderModal',
		'modal-invoice', // selector
		array( // options
			'modal-dialog-scrollable' => true,
			'title'  => \JText::_('INVOICING_FILELIST_TEXT'),
			'footer' => '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">'.\JText::_('INVOICING_CLOSE').'</button>',
		),
			'<div id="modal-body">'.$content.'</div>'
	);
?>