<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>
<table class="adminheading">
	<tr>
		<th class="config">AlloPASS</th>
	</tr>
  </table>
  <table cellpadding="4" cellspacing="1" border="0" width="100%" class="adminform">
		<tr>
			<td><?php echo \JText::_('SiteID'); ?></td>
			<td><input class="inputbox" type="text" name="allopass_siteid" size="50" value="<?php echo $this->config->siteid; ?>" /></td>
			<td align="left"><?php echo JHTML::_('tooltip',''); ?></td>
		</tr>
		<?php foreach($this->list as $p) { ?>
		<tr>
			<?php $name = "pageid_".$p->id; ?> 
			<td><?php echo $name; ?></td>
			<td><input class="inputbox" type="text" name="allopass_<?php echo $name ?>" size="50" value="<?php echo $this->config->$name; ?>" /></td>
			<td align="left"><?php echo JHTML::_('tooltip',''); ?></td>
		</tr>
		<tr>
			<?php $name = "fullid_".$p->id; ?> 
			<td><?php echo $name; ?></td>
			<td><input class="inputbox" type="text" name="allopass_<?php echo $name?>" size="50" value="<?php echo $this->config->$name; ?>" /></td>
			<td align="left"><?php echo JHTML::_('tooltip',''); ?></td>
		</tr>
		<?php } ?>
  </table>