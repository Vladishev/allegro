<?php
class Orba_Allegro_Model_Resource_Template_Collection 
    extends Orba_Allegro_Model_Resource_Collection_Abstract
{

    /**
     * Initialize resources
     *
     */
    protected function _construct()
    {
        $this->_init('orbaallegro/template');
    }

    /**
     * @return Orba_Allegro_Model_Resource_Template_Collection
     */
    protected function _beforeLoad()
    {
        Mage::dispatchEvent('orbaallegro_template_collection_load_before', array('collection' => $this));
        return parent::_beforeLoad();
    }

    /**
     * @return Orba_Allegro_Model_Resource_Template_Collection
     */
    protected function _afterLoad()
    {
        if (count($this) > 0) {
            Mage::dispatchEvent('orbaallegro_template_collection_load_after', array('collection' => $this));
        }

        return $this;
    }


    /**
     * Add collection filters by identifiers
     *
     * @param mixed $productId
     * @param boolean $exclude
     * @return Orba_Allegro_Model_Resource_Template_Collection
     */
    public function addIdFilter($productId, $exclude = false)
    {
        if (empty($productId)) {
            $this->_setIsLoaded(true);
            return $this;
        }
        if (is_array($productId)) {
            if (!empty($productId)) {
                if ($exclude) {
                    $condition = array('nin' => $productId);
                } else {
                    $condition = array('in' => $productId);
                }
            } else {
                $condition = '';
            }
        } else {
            if ($exclude) {
                $condition = array('neq' => $productId);
            } else {
                $condition = $productId;
            }
        }
        $this->addFieldToFilter('entity_id', $condition);
        return $this;
    }


    /**
     * Return all attribute values as array in form:
     * array(
     *   [entity_id_1] => array(
     *          [store_id_1] => store_value_1,
     *          [store_id_2] => store_value_2,
     *          ...
     *          [store_id_n] => store_value_n
     *   ),
     *   ...
     * )
     *
     * @param string $attribute attribute code
     * @return array
     */
    public function getAllAttributeValues($attribute)
    {
        /** @var $select Varien_Db_Select */
        $select    = clone $this->getSelect();
        $attribute = $this->getEntity()->getAttribute($attribute);

        $select->reset()
            ->from($attribute->getBackend()->getTable(), array('entity_id', 'store_id', 'value'))
            ->where('attribute_id = ?', (int)$attribute->getId());

        $data = $this->getConnection()->fetchAll($select);
        $res  = array();

        foreach ($data as $row) {
            $res[$row['entity_id']][$row['store_id']] = $row['value'];
        }

        return $res;
    }

    /**
     * Get SQL for get record count without left JOINs
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        return $this->_getSelectCountSql();
    }

    /**
     * Get SQL for get record count
     *
     * @param bool $resetLeftJoins
     * @return Varien_Db_Select
     */
    protected function _getSelectCountSql($select = null, $resetLeftJoins = true)
    {
        $this->_renderFilters();
        $countSelect = (is_null($select)) ?
            $this->_getClearSelect() :
            $this->_buildClearSelect($select);
        $countSelect->columns('COUNT(DISTINCT e.entity_id)');
        if ($resetLeftJoins) {
            $countSelect->resetJoinLeft();
        }
        return $countSelect;
    }

    /**
     * Retreive clear select
     *
     * @return Varien_Db_Select
     */
    protected function _getClearSelect()
    {
        return $this->_buildClearSelect();
    }

    /**
     * Build clear select
     *
     * @param Varien_Db_Select $select
     * @return Varien_Db_Select
     */
    protected function _buildClearSelect($select = null)
    {
        if (is_null($select)) {
            $select = clone $this->getSelect();
        }
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);
        $select->reset(Zend_Db_Select::COLUMNS);

        return $select;
    }

    /**
     * Retrive all ids for collection
     *
     * @param unknown_type $limit
     * @param unknown_type $offset
     * @return array
     */
    public function getAllIds($limit = null, $offset = null)
    {
        $idsSelect = $this->_getClearSelect();
        $idsSelect->columns('e.' . $this->getEntity()->getIdFieldName());
        $idsSelect->limit($limit, $offset);
        $idsSelect->resetJoinLeft();

        return $this->getConnection()->fetchCol($idsSelect, $this->_bindParams);
    }

    /**
     * Add attribute to sort order
     *
     * @param string $attribute
     * @param string $dir
     * @return Orba_Allegro_Model_Resource_Template_Collection
     */
    public function addAttributeToSort($attribute, $dir = self::SORT_ORDER_ASC)
    {
        return parent::addAttributeToSort($attribute, $dir);
    }
    
    
    public function addProductCount() {
        $select = $this->getSelect();
        $adapter = $select->getAdapter();
        
        $attribute = Mage::getSingleton('eav/config')->
                getAttribute("catalog_product", Orba_Allegro_Model_Mapping::ATTR_CODE_TEMPLATE);
        /* @var $attribute Mage_Eav_Model_Attribute */
        
        $backendTabel = $attribute->getBackendTable();
        
        $select->joinLeft(array("at_temp"=>$backendTabel), 
            $adapter->quoteInto("at_temp.attribute_id=? AND ", $attribute->getId()). 
                $adapter->quoteInto("at_temp.store_id=? AND ", $this->getStoreId()). 
                "at_temp.value=e.entity_id",
            array('product_count'=> new Zend_Db_Expr('COUNT(at_temp.attribute_id)'))
        );
        
        $select->group('e.entity_id');
        
        return $this;
    }
    
    public function toOptionArray() {
        $this->addAttributeToSelect('name');
        return $this->_toOptionArray('entity_id', 'name');
    }
    
    public function toOptionHash() {
        $this->addAttributeToSelect('name');
        return $this->_toOptionHash('entity_id', 'name');
    }
    
    
    
}
