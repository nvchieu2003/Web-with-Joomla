<?php
/**
 * @package		Invoicing
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;

//JHtml::_('behavior.tooltip');
if(version_compare(JVERSION, '3.0', 'ge')) {
	JHTML::_('jquery.framework');
} else {
	JHTML::_('behavior.mootools');
}
////JHtml::_('behavior.modal');

$document = \JFactory::getDocument();
$document->addScript(JURI::root().'media/com_invoicing/js/jquery.form.js');
$document->addScript(JURI::root().'media/com_invoicing/js/jquery.validate.js');

//$this->loadHelper('Select');
$content = '';

ob_start();
?>

<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm form form formValidation">
	<input type="hidden" name="option" value="com_invoicing" />
	<input type="hidden" name="view" value="user" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="ajaxcall" value="1" />
	<input type="hidden" name="invoicing_template_id" value="<?php echo $this->item->invoicing_template_id ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>

	<table id="account_form" cellspacing="0" cellpadding="0" width="100%">
	<tr valign="top"><td width="60%">	
	
	
	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
		<tr>
			<th><?php echo \JText::_('INVOICING_FILELIST_TEXT') ?></th>
		</tr>
		<tr>
			<td>
				<?php
					$input = \JFactory::getApplication()->input;
					$id = $input->getInt('id', 0);
					switch ($id) {
						default:
						case 1: 
							$type  = "order"; 
							break;
						case 2:
							$type  = "invoice"; 
							break;
						case 3:
							$type  = "quote"; 
							break;
					}
					
					$pathfile = JUri::root()."administrator/components/com_invoicing/templates/".$type."/";
					$path = JPATH_COMPONENT_ADMINISTRATOR."/templates/$type/";
					$result = array();
					$result = JFolder::files($path, $filter= '.html', $recurse=false, $fullpath=false, $exclude=array('.svn','CVS'));
					echo "<div id='container-templates'>";
					foreach ($result as $file) {
						echo "<div class='templatelink' data-bs-dismiss='modal' template='$file'>";
						echo "<a href='#'><img src='$pathfile"; echo basename($file,'.html'); echo ".jpg' ></a>";
						echo "</div>";
					}
					echo "</div>";
					?>
			</td>
		</tr>
	</table>
	</td></tr>
	</table>
	<br/>
	</td></tr>
	</table>
</form>
<?php

	$content = ob_get_contents();
	ob_end_clean();

	echo HTMLHelper::_(
		'bootstrap.renderModal',
		'modal-box', // selector
		array( // options
			'modal-dialog-scrollable' => true,
			'title'  => \JText::_('INVOICING_FILELIST_TEXT'),
			'footer' => '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">'.\JText::_('INVOICING_CLOSE').'</button>',
		),
			'<div id="modal-body">'.$content.'</div>'
	);
?>