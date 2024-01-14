<?php
/**
 * @package Invoicing
 * @copyright Copyright (c)2013 - 2021 Juloa.com
 * @license GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

class InvoicingModelDefault extends \JModelLegacy
{
    public function getItem($id) {
        $sql = "SELECT * FROM #__invoicing_".$this->_tableName." WHERE ".$this->_fieldId." = ".(int)$id;

        $this->_db->setQuery($sql);

        $item = $this->_db->loadObject();

        if($item === null) {
            return $this->initEmptyEntry();
        }
        
        $this->onAfterGetItem($item);

        return $item;
    }

    protected function getSQLFilters($filters = array()) {
        if(empty($filters)) return '';

		$isFiltered = false;
		foreach($this->_filters as $filter) {
			if($filter !== '') {
				$isFiltered = true;
			}
		}
		if(!$isFiltered) return '';

        return true;
    }

    public function getItems($filters = array()) {
        $where = $this->getSQLFilters($filters);

        $sql = "SELECT * FROM #__invoicing_".$this->_tableName.$where;

        $this->_db->setQuery($sql);

        $items = $this->_db->loadObjectList();

        if(!$items) {
            return array();
        }

        return $items;
    }

    public function getNbItems($filters = array()) {
        $where = $this->getSQLFilters($filters);
        
        $sql = "SELECT count(*) FROM #__invoicing_".$this->_tableName.$where;

        $this->_db->setQuery($sql);

        $total = $this->_db->loadResult();

        return $total;
    }

    protected function onAfterGetItem(&$record) {
        return true;
    }

    protected function initEmptyEntry() {
        return new stdClass;
    }

    protected function onBeforeSave(&$record) {
        return true;
    }

    protected function onAfterSave(&$post) {
        return true;
    }

    public function save($post) {
        $this->onBeforeSave($post);

        $this->_db->insertObject("#__invoicing_" . $this->_tableName, $post);
        $id = $this->getId($post);
        $fieldId = $this->_fieldId;
        $post->$fieldId = $id;

        $this->onAfterSave($post);

        return $id;
    }

    protected function getId($post) {
		$sql = "SELECT MAX(".$this->_fieldId.") FROM #__invoicing_".$this->_tableName;

		$this->_db->setQuery($sql);

		return $this->_db->loadResult();
	}

    public function update($post) {
        $this->onBeforeSave($post);
        $this->_db->updateObject("#__invoicing_" . $this->_tableName, $post, $this->_fieldId);
        $this->onAfterSave($post);

        $fieldId = $this->_fieldId;

        return $post->$fieldId;
    }

    public function delete($id) {
        $sql = "DELETE FROM #__invoicing_" . $this->_tableName . " 
                WHERE " . $this->_fieldId . " = ".(int)$id;

        $this->_db->setQuery($sql);

        $this->_db->execute();

        return true;
    }

    function changeState($state,$cid) {
		$cids = implode( ',', $cid );
		$this->_db->setQuery("UPDATE #__invoicing_".$this->_tableName." SET ".$this->_publishField." = $state WHERE ".$this->_fieldId." IN ($cids)");
		$this->_db->execute();
	}
}