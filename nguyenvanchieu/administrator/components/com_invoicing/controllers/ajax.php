<?php
/**
 *  @package Bruce
 *  @copyright Copyright (c)203 Juloa.com
 *  @license GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/controllers/default.php');

class InvoicingControllerAjax extends InvoicingControllerDefault
{
	protected $accessLabel = "ajax";
	protected $controllerLabel = "ajax";

	function __construct($config= array()) {
		parent::__construct($config);
	}

	public function execute($task) {

	if ($task == 'updateserverxml') {
			$db = \JFactory::getDbo();
			$db->setQuery("SELECT e.extension_id
							FROM #__extensions AS e
							WHERE e.type = 'component' AND e.element = 'com_invoicing'");
			$extension_id = $db->loadResult();

			$db->setQuery("DELETE FROM #__update_sites WHERE update_site_id IN
					       (SELECT ue.update_site_id FROM #__update_sites_extensions AS ue
							WHERE ue.extension_id = ".(int)$extension_id.")");
			$db->execute();

			$db->setQuery("DELETE FROM #__update_sites_extensions WHERE extension_id = ".(int)$extension_id);
			$db->execute();

			$data = new \stdClass();

			$input = \JFactory::getApplication()->input;

			$dlid = $input->get('dlid',"","String");

			$data->location = "";  // Free URL
			$data->location = "http://www.joomprod.com/updatestream?id=11&dlid=".$dlid."&dummy=/extension.xml";

			$data->name ="Invoicing Update XML";
			$data->type ="extension";
			$data->enabled = 1;
			$data->last_check_timestamp = 0;
			$db->insertObject("#__update_sites",$data);

			$update_site_id = $db->insertid();

			$data = new \stdClass();
			$data->update_site_id = $update_site_id;
			$data->extension_id = $extension_id;
			$db->insertObject("#__update_sites_extensions",$data);

			exit();
		}
		exit();
	}
}
