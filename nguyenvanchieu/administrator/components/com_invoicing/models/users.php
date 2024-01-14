<?php
/**
 * @package Invoicing
 * @copyright Copyright (c)203 Juloa.com
 * @license GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

include_once(JPATH_ADMINISTRATOR.'/components/com_invoicing/models/default.php');
include_once (JPATH_ADMINISTRATOR.'/components/com_invoicing/helpers/cparams.php');

use Joomla\CMS\Application\ApplicationHelper;

class InvoicingModelUsers extends InvoicingModelDefault {
	protected $_tableName = "users";
	protected $_fieldId = "invoicing_user_id";
	protected $_filters = array('search', 'city', 'zip', 'country');

	protected function getSQLFilters($filters = array()) {
		$isFiltered = parent::getSQLFilters($filters);

		if($isFiltered == '') return '';

		$where = ' WHERE ';
		$conditions = array();

		foreach($this->_filters as $filter) {
			if(!isset($filters[$filter]) || $filters[$filter] == '') {
				continue;
			}
			if($filter == 'search') {
				$conditions[] = '('.$this->_fieldId.' LIKE '.$this->_db->q('%'.$filters[$filter].'%').' 
								OR lastname LIKE '.$this->_db->q('%'.$filters[$filter].'%').'
								OR firstname LIKE '.$this->_db->q('%'.$filters[$filter].'%').'
								OR businessname LIKE '.$this->_db->q('%'.$filters[$filter].'%').')';
			} else {
				$conditions[] = $filter.' LIKE '.$this->_db->q('%'.$filters[$filter].'%');
			}
		}
		
		if(empty($conditions)) return '';

		return $where.implode(' AND ', $conditions);
	}

	public function getUser($userid) {
		$sql =" SELECT i.*,u.name,u.username, u.email "
		     ." FROM #__invoicing_users as i "
			 ." LEFT JOIN #__users as u ON u.id = i.user_id WHERE u.id=".(int)$userid;
		
		$db = $this->getDbo();
		$db->setQuery($sql);
		return $db->loadObject();
	}
	
	public function saveUser($userid,$data) {
		
		$user = $this->getUser($userid);
		$db = $this->getDbo();
		if ($user->invoicing_user_id > 0) {
			$db->updateObject('#__invoicing_users',$data,'user_id');
		} else {
			$db->insertObject('#__invoicing_users',$data);
		}
		
		$data = (array)$data;
		
		$user = new \stdClass();
		$user->name = $data['businessname'];
		$user->id = $userid; 
		$db->updateObject('#__users',$user,'id');
		
		
		$cb = InvoicingHelperCparams::getParam('cb',0);
		if ($cb == 1) {
			$obj = new \stdClass();
			$cb_name = InvoicingHelperCparams::getParam('cb_name','name');
			if ($cb_name != "name") {
				$obj->$cb_name = $data['businessname'];
			}
			$cb_city = InvoicingHelperCparams::getParam('cb_city','');
			$cb_address = InvoicingHelperCparams::getParam('cb_address','');
			$cb_address2 = InvoicingHelperCparams::getParam('cb_address2','');
			$cb_zip = InvoicingHelperCparams::getParam('cb_zip','');
			$cb_country = InvoicingHelperCparams::getParam('cb_country','');
				
			if ($cb_city != "")
				$obj->$cb_city = $data['city'];
			if ($cb_address != "")
				$obj->$cb_address = $data['address1'];
			if ($cb_address2 != "")
				$obj->$cb_address2 = $data['address2'];
			if ($cb_zip != "")
				$obj->$cb_zip = $data['zip'];
			if ($cb_country != "")
				$obj->$cb_country = $data['country'];
	
			$obj->user_id = $data['user_id'];
			$obj->id = $data['user_id'];
			
			$obj->confirmed = 1;
			$obj->approved = 1;
			
			$db->setQuery("SELECT id FROM #__comprofiler WHERE id = ".$data['user_id']);
			$result = $db->loadResult();
			if ($result) {
				$obj->id = $result;
				$db->updateObject('#__comprofiler', $obj,'id');
			}
			else
				$db->insertObject('#__comprofiler', $obj);
		}	
	}
	
	public function getItem($id = 0)
	{
		$db =\JFactory::getDBO();
		$cb = InvoicingHelperCparams::getParam('cb',0);
		if($id != 0) {
			$this->record = null;
		}
		
		if ($cb == 1) {
			if($id != 0) {
				
				$cb_name = InvoicingHelperCparams::getParam('cb_name','name');
				if ($cb_name == "name")
					$cb_name = "u.".$cb_name." as cb_businessname";
				else
					$cb_name = "c.".$cb_name." as cb_businessname";
				
				$select = array();
					
				$cb_city = InvoicingHelperCparams::getParam('cb_city','');
				if ($cb_city != "") {
					$select[]  = "c.".$cb_city." as cb_city";
				}
				
				$cb_address = InvoicingHelperCparams::getParam('cb_address','');
				if ($cb_address != "") {
					$select[]  = "c.".$cb_address." as cb_address";
				}
				
				$cb_address2 = InvoicingHelperCparams::getParam('cb_address2','');
				if ($cb_address2 != "") {
					$select[]  = "c.".$cb_address2." as cb_address2";
				}
					
				$cb_country = InvoicingHelperCparams::getParam('cb_country','');
				if ($cb_country != "") {
					$select[]  = "c.".$cb_country." as cb_country";
				}
					
				$cb_zip = InvoicingHelperCparams::getParam('cb_zip','');
				if ($cb_zip != "") {
					$select[]  = "c.".$cb_zip." as cb_zip";
				}
					
				$cb_firstname = InvoicingHelperCparams::getParam('cb_firstname','');
				if ($cb_firstname != "") {
					$select[]  = "c.".$cb_firstname." as cb_city";
				}
					
				$cb_lastname = InvoicingHelperCparams::getParam('cb_lastname','');
				if ($cb_lastname != "") {
					$select[]  = "c.".$cb_lastname." as cb_lastname";
				}
					
				$cb_mobile = InvoicingHelperCparams::getParam('cb_mobile','');
				if ($cb_mobile != "") {
					$select[]  = "c.".$cb_mobile." as cb_mobile";
				}
					
				$cb_landline = InvoicingHelperCparams::getParam('cb_landline','');
				if ($cb_landline != "") {
					$select[]  = "c.".$cb_landline." as cb_landline";
				}
					
				$cb_notes = InvoicingHelperCparams::getParam('cb_notes','');
				if ($cb_notes != "") {
					$select[]  = "c.".$cb_notes." as cb_notes";
				}
					
				$sqlselect = implode(",",$select);
				if ($sqlselect != "") {
					$sqlselect = ",".$sqlselect;
				}
				
		
				$query = " SELECT i.*,$cb_name,u.username, u.email".$sqlselect
				        ." FROM #__invoicing_users as i "
				        ." LEFT JOIN #__comprofiler as c ON c.user_id = i.user_id "
				        ." LEFT JOIN #__users as u ON u.id = i.user_id WHERE i.invoicing_user_id=".(int)$id;

				$db->setQuery($query);
				$values = $db->loadObject();
				$this->record = new \stdClass();
                $this->record->invoicing_user_id = null;
                $this->record->businessname = null;
                $this->record->username = null;
                $this->record->email = null;
                $this->record->city = null;
                $this->record->address1 = null;
                $this->record->address2 = null; 
                $this->record->zip = null;
                $this->record->firstname = null;
                $this->record->lastname = null;
                $this->record->mobile = null;
                $this->record->landline = null;
                $this->record->notes = null;
                $this->record->country = null;
                
				if ($values != null) {
					foreach($values as $key => $val) {
						$this->record->$key = $val;
					}
				}
				if ((isset($this->record->user_id)) && ($this->record->user_id != 0)) {
					$this->record->businessname = @$this->record->cb_businessname;
					$this->record->city = @$this->record->cb_city;
					$this->record->address1 = @$this->record->cb_address;
					$this->record->address2 = @$this->record->cb_address2;
					$this->record->zip = @$this->record->cb_zip;
					$this->record->country = @$this->record->cb_country;
					$this->record->firstname = @$this->record->cb_firstname;
					$this->record->lastname = @$this->record->cb_lastname;
					$this->record->mobile = @$this->record->cb_mobile;
					$this->record->landline = @$this->record->cb_landline;
					$this->record->notes = @$this->record->cb_notes;
				}
				$this->onAfterGetItem($this->record);
			}
			return $this->record;
		} else {
			if ($id != 0) {
				$query = "SELECT i.*,u.username, u.email FROM #__invoicing_users as i LEFT JOIN #__users as u ON u.id = i.user_id WHERE i.invoicing_user_id=".(int)$id;
				$db->setQuery($query);
				$values = $db->loadObject();
				$this->record = new stdClass();//var_dump($this->record);
                $this->record->invoicing_user_id = null;
                $this->record->businessname = null;
                $this->record->username = null;
                $this->record->email = null;
                $this->record->city = null;
                $this->record->address1 = null;
                $this->record->address2 = null; 
                $this->record->zip = null;
                $this->record->country = null;
                $this->record->firstname = null;
                $this->record->lastname = null;
                $this->record->mobile = null;
                $this->record->landline = null;
                $this->record->notes = null;
                
				if ($values != null) {
					foreach($values as $key => $val) {
						$this->record->$key = $val;
					}
				}
				$this->onAfterGetItem($this->record);
				return $this->record;
			} else {
				$item = parent::getItem($id);
				
				return $item;
			}
		}		
	}

	public function initEmptyEntry() {
		$item = new stdClass();
		$item->invoicing_user_id = null;
		$item->businessname = null;
		$item->username = null;
		$item->email = null;
		$item->city = null;
		$item->address1 = null;
		$item->address2 = null; 
		$item->zip = null;
		$item->country = null;
		$item->firstname = null;
		$item->lastname = null;
		$item->mobile = null;
		$item->landline = null;
		$item->notes = null;

		return $item;
	}

	public function createUser($user_id) {
		$db =\JFactory::getDBO();
        $sql = "SELECT * FROM #__users WHERE id = ".(int)$user_id;
		$db->setQuery($sql);
        $user = $db->loadObject();
		$obj = new \stdClass();
		$obj->user_id = $user_id;
        $obj->businessname = $user->username;
		$db->insertObject('#__invoicing_users', $obj);
	}

	public function getInvoicingUser($user_id) {
		$db =\JFactory::getDBO();
		$sql = "SELECT invoicing_user_id FROM #__invoicing_users WHERE user_id = ".(int)$user_id;
		$db->setQuery($sql);
		$invoicing_user_id = $db->loadResult();
		return $invoicing_user_id;
	}
	
	public function save($data = NULL, $orderingFilter = '', $ignore = NULL, $resetRelations = true)
	{
		$db =\JFactory::getDBO();
		$cb = InvoicingHelperCparams::getParam('cb',0);
		$input = \JFactory::getApplication()->input;
		if ($cb == 1) {
			$data = $input->get->post->getArray();
			if(!$this->onBeforeSave($data)) {
				return false;
			}
			if ($data['user_id'] != null) {
				$obj = new \stdClass();
				$cb_name = InvoicingHelperCparams::getParam('cb_name','name');
				if ($cb_name != "name") {
					$obj->$cb_name = $data['businessname'];
				}
				$cb_city = InvoicingHelperCparams::getParam('cb_city','');
				$cb_address = InvoicingHelperCparams::getParam('cb_address','');
				$cb_address2 = InvoicingHelperCparams::getParam('cb_address2','');
				$cb_zip = InvoicingHelperCparams::getParam('cb_zip','');
				$cb_country = InvoicingHelperCparams::getParam('cb_country','');
				$cb_firstname = InvoicingHelperCparams::getParam('cb_firstname','');
				$cb_lastname = InvoicingHelperCparams::getParam('cb_lastname','');
				$cb_mobile = InvoicingHelperCparams::getParam('cb_mobile','');
				$cb_landline = InvoicingHelperCparams::getParam('cb_landline','');
				$cb_notes = InvoicingHelperCparams::getParam('cb_notes','');
			
				if ($cb_city != "")
					$obj->$cb_city = $data['city'];
				if ($cb_address != "")
					$obj->$cb_address = $data['address1'];
				if ($cb_address2 != "")
					$obj->$cb_address2 = $data['address2'];
				if ($cb_zip != "")
					$obj->$cb_zip = $data['zip'];
				if ($cb_country != "")
					$obj->$cb_country = $data['country'];
				if ($cb_landline != "")
					$obj->$cb_landline = $data['landline'];
				if ($cb_mobile != "")
					$obj->$cb_mobile = $data['mobile'];
				if ($cb_firstname != "")
					$obj->$cb_firstname = $data['firstname'];
				if ($cb_lastname != "")
					$obj->$cb_lastname = $data['lastname'];
				if ($cb_notes != "")
					$obj->$cb_notes = $data['notes'];
				
				$obj->user_id = $data['user_id'];
				$obj->id = $data['user_id'];
				$db->updateObject('#__comprofiler',$obj,'id');	
			}	
		}
		parent::save($data, $orderingFilter, $ignore, $resetRelations);
		return true;
	}

	protected function getId($post) {
		$sql = "SELECT ".$this->_fieldId." FROM #__invoicing_".$this->_tableName."
				WHERE user_id = ".$this->_db->quote($post->user_id);

		$this->_db->setQuery($sql);

		return $this->_db->loadResult();
	}
	
	public function onBeforeSave(&$post) {
		$db =\JFactory::getDBO();

		$data = $post;
		$data = (array)$post;

		if ((@$data['user_id'] == null)&&(@$data['create_user'] != null)) {	
			$params = JComponentHelper::getParams('com_users');
			// Save User using Joomla code
			if (version_compare(JVERSION,'1.6.0','<')) {
				$authorize	= \JFactory::getACL();
			
				$user = clone(\JFactory::getUser());
					
				// Initialize new usertype setting
				$newUsertype = $params->get( 'new_usertype' );
				if (!$newUsertype) {
					$newUsertype = 'Registered';
				}
					
				// Bind the post array to the user object
				$input = \JFactory::getApplication()->input;
				$post = $input->get->post->getArray();
				$post['password2'] = $post['password'];
				if (!$user->bind($post, 'usertype' )) {
					throw new Exception($user->getError());
				}
			
				// Set some initial user values
				$user->set('id', 0);
				$user->set('usertype', $newUsertype);
				$user->set('gid', $authorize->get_group_id( '', $newUsertype, 'ARO' ));
			
				$date = \JFactory::getDate();
				$user->set('registerDate', $date->toMySQL());
			
				// If user activation is turned on, we need to set the activation information
				$useractivation = $params->get( 'useractivation' );
				if ($useractivation == '1')
				{
					jimport('joomla.user.helper');
					$user->set('activation', JUtility::getHash( JUserHelper::genRandomPassword()) );
					//$user->set('block', '1');
				}
			
				// If there was an error with registration, set the message and display form
				if ( !$user->save() )
				{
					\JFactory::getApplication()->enqueueMessage(\JText::_( $user->getError()), 'warning');
					return false;
				}
			} else {
				// Initialise the table with JUser.
				$user = new JUser;
			
				// Prepare the data for the user object.
				$useractivation = $params->get('useractivation');
			
				// Check if the user needs to activate their account.
				if (($useractivation == 1) || ($useractivation == 2)) {
					jimport('joomla.user.helper');
					if (version_compare(JVERSION,'3.0.0','>=')) {
						$data['activation'] = ApplicationHelper::getHash(JUserHelper::genRandomPassword());
					} else {
						$data['activation'] = JUtility::getHash(JUserHelper::genRandomPassword());
					}
					//$data['block'] = 1;
				}
					
				// Get the groups the user should be added to after registration.
				$data['groups'] = array();
				// Get the default new user group, Registered if not specified.
				$system	= $params->get('new_usertype', 2);
				$data['groups'][] = $system;
				
				$data['name'] = $data['businessname'];
			
				//var_dump($data);exit();
				// Bind the data.
				if (!$user->bind($data)) {
					$this->setError(\JText::sprintf('COM_USERS_REGISTRATION_BIND_FAILED', $user->getError()));
					return false;
				}
			
				// Load the users plugin group.
				JPluginHelper::importPlugin('user');
			
				// Store the data.
				if (!$user->save()) {
					$this->setError(\JText::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $user->getError()));
					return false;
				}
			}
			$query = "SELECT MAX(id) FROM #__users";
			$db->setQuery($query);
			$userid = $db->loadResult();
			$post->user_id = $userid;

			/*$obj = new \stdClass();
			$obj->invoicing_user_id = $userid;
			$obj->user_id = $userid;
			$db->insertObject('#__invoicing_users', $obj);*/
			
			$cb = InvoicingHelperCparams::getParam('cb',0);
			if ($cb == 1)
			{	
				$obj = new \stdClass();
				$obj->id = $userid;
				$obj->user_id = $userid;
				$db->insertObject('#__comprofiler', $obj);
			}
		} else if (@$data['user_id'] != null) {
			$input = \JFactory::getApplication()->input;
			$obj = new \stdClass();
			$obj->name = $input->get('businessname','', "String");
			$obj->email = $input->get('email','', "String");
			if (($obj->name != "")&&($obj->email != "")) {
				$obj->id = $input->getInt('user_id',0);
				
				if ($input->get('password','', "String")) {
					jimport('joomla.user.helper');
					$salt = JUserHelper::genRandomPassword(32);
					$crypt = JUserHelper::getCryptedPassword($input->getVar('password','', "String"), $salt);
					$obj->password = $crypt.':'.$salt;
				}
				$db->updateObject('#__users', $obj,'id');
			}
		}
		return true; 
	}
}
