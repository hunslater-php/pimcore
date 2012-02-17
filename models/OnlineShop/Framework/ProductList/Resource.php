<?php

class OnlineShop_Framework_ProductList_Resource {

    /**
     * @var Zend_Db_Adapter_Abstract
     */
    private $db;

    /**
     * @var OnlineShop_Framework_ProductList
     */
    private $model;

    public function __construct(OnlineShop_Framework_ProductList $model) {
        $this->model = $model;
        $this->db = Pimcore_Resource::get();
    }


    public function load($condition, $orderBy = null, $limit = null, $offset = null) {

        if($condition) {
            $condition = "WHERE " . $condition;
        }

        if($orderBy) {
            $orderBy = " ORDER BY " . $orderBy;
        }

        if($limit) {
            if($offset) {
                $limit = "LIMIT " . $offset . ", " . $limit;
            } else {
                $limit = "LIMIT " . $limit;
            }
        }

        if($this->model->getVariantMode() == OnlineShop_Framework_ProductList::VARIANT_MODE_INCLUDE_PARENT_OBJECT) {
            if($orderBy) {
                $query = "SELECT DISTINCT o_virtualProductId as o_id, priceSystemName FROM " . OnlineShop_Framework_IndexService::TABLENAME . " " . $condition . " GROUP BY o_virtualProductId, priceSystemName" . $orderBy . " " . $limit;
            } else {
                $query = "SELECT DISTINCT o_virtualProductId as o_id, priceSystemName FROM " . OnlineShop_Framework_IndexService::TABLENAME . " " . $condition . " " . $limit;
            }
        } else {
            $query = "SELECT o_id, priceSystemName FROM " . OnlineShop_Framework_IndexService::TABLENAME . " " . $condition . $orderBy . " " . $limit;
        }
        Logger::log("Query: " . $query, Zend_Log::INFO);
        $result = $this->db->fetchAll($query);
        Logger::log("Query done.", Zend_Log::INFO);
        return $result;
    }

    public function loadGroupByValues($fieldname, $condition, $countValues = false) {

        if($condition) {
            $condition = "WHERE " . $condition;
        }

        if($countValues) {
            if($this->model->getVariantMode() == OnlineShop_Framework_ProductList::VARIANT_MODE_INCLUDE_PARENT_OBJECT) {
                $query = "SELECT `$fieldname` as `value`, count(DISTINCT o_virtualProductId) as `count` FROM " . OnlineShop_Framework_IndexService::TABLENAME . " " . $condition . " GROUP BY `" . $fieldname . "`";
            } else {
                $query = "SELECT `$fieldname` as `value`, count(*) as `count` FROM " . OnlineShop_Framework_IndexService::TABLENAME . " " . $condition . " GROUP BY `" . $fieldname . "`";
            }

            Logger::log("Query: " . $query, Zend_Log::INFO);
            $result = $this->db->fetchAll($query);
            Logger::log("Query done.", Zend_Log::INFO);
            return $result;
        } else {
            $query = "SELECT `$fieldname` FROM " . OnlineShop_Framework_IndexService::TABLENAME . " " . $condition . " GROUP BY `" . $fieldname . "`";
            Logger::log("Query: " . $query, Zend_Log::INFO);
            $result = $this->db->fetchCol($query);
            Logger::log("Query done.", Zend_Log::INFO);
            return $result;
        }
    }

    public function loadGroupByRelationValues($fieldname, $condition, $countValues = false) {

        if($condition) {
            $condition = "WHERE " . $condition;
        }

        if($countValues) {
            if($this->model->getVariantMode() == OnlineShop_Framework_ProductList::VARIANT_MODE_INCLUDE_PARENT_OBJECT) {
                $query = "SELECT dest as `value`, count(DISTINCT o_virtualProductId) as `count` FROM " . OnlineShop_Framework_IndexService::RELATIONTABLENAME . " WHERE fieldname = " . $this->quote($fieldname);
            } else {
                $query = "SELECT dest as `value`, count(*) as `count` FROM " . OnlineShop_Framework_IndexService::RELATIONTABLENAME . " WHERE fieldname = " . $this->quote($fieldname);
            }

            $query .= " AND src IN (SELECT o_id FROM " . OnlineShop_Framework_IndexService::TABLENAME . " " . $condition . ") GROUP BY dest";

            Logger::log("Query: " . $query, Zend_Log::INFO);
            $result = $this->db->fetchAll($query);
            Logger::log("Query done.", Zend_Log::INFO);
            return $result;
        } else {
            $query = "SELECT dest FROM " . OnlineShop_Framework_IndexService::RELATIONTABLENAME . " WHERE fieldname = " . $this->quote($fieldname);
            $query .= " AND src IN (SELECT o_id FROM " . OnlineShop_Framework_IndexService::TABLENAME . " " . $condition . ") GROUP BY dest";

            Logger::log("Query: " . $query, Zend_Log::INFO);
            $result = $this->db->fetchCol($query);
            Logger::log("Query done.", Zend_Log::INFO);
            return $result;
        }
    }

    public function getCount($condition, $orderBy = null, $limit = null, $offset = null) {
        if($condition) {
            $condition = "WHERE " . $condition;
        }

        if($orderBy) {
            $orderBy = " ORDER BY " . $orderBy;
        }

        if($limit) {
            if($offset) {
                $limit = "LIMIT " . $offset . ", " . $limit;
            } else {
                $limit = "LIMIT " . $limit;
            }
        }

        if($this->model->getVariantMode() == OnlineShop_Framework_ProductList::VARIANT_MODE_INCLUDE_PARENT_OBJECT) {
            $query = "SELECT count(DISTINCT o_virtualProductId) FROM " . OnlineShop_Framework_IndexService::TABLENAME . " " . $condition . $orderBy . " " . $limit;
        } else {
            $query = "SELECT count(*) FROM " . OnlineShop_Framework_IndexService::TABLENAME . " " . $condition . $orderBy . " " . $limit;
        }
        Logger::log("Query: " . $query, Zend_Log::INFO);
        $result = $this->db->fetchOne($query);
        Logger::log("Query done.");
        return $result;
    }

    public function quote($value) {
        return $this->db->quote($value);
    }

    
}
