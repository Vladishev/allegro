<?php
class Orba_Allegro_Model_Resource_Mapping extends Mage_Core_Model_Resource_Db_Abstract {
    
    protected function _construct() {
        $this->_init('orbaallegro/mapping', 'mapping_id');
    } 
    

    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId(), $object->getCountryCode());
            $object->setData('store_id', $stores);
            $object->setData('stores', $stores);
        }
        return parent::_afterLoad($object);
    }

    
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $stores = array(
                (int) $object->getStoreId(),
                Mage_Core_Model_App::ADMIN_STORE_ID,
            );

            $select->join(
                array('cbs' => $this->getTable('orbaallegro/mapping_store')),
                $this->getMainTable().'.mapping_id = cbs.mapping_id',
                array('store_id')
            )
            ->where('cbs.store_id in (?) ', $stores)
            ->order('store_id DESC')
            ->limit(1);
        }

        return $select;
    }
    
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array)$object->getStores();

        $table  = $this->getTable('orbaallegro/mapping_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);

        if ($delete) {
            $where = array(
                'mapping_id = ?'     => (int) $object->getId(),
                'store_id IN (?)'    => $delete
            );

            $this->_getWriteAdapter()->delete($table, $where);
        }

        if ($insert) {
            $data = array();

            foreach ($insert as $storeId) {
                $data[] = array(
                    'mapping_id'  => (int) $object->getId(),
                    'store_id'    => (int) $storeId
                );
            }

            $this->_getWriteAdapter()->insertMultiple($table, $data);
        }

        return parent::_afterSave($object);

    }


    public function lookupStoreIds($id, $countryCode=null)
    {
        $adapter = $this->_getReadAdapter();

        $select  = $adapter->select()
            ->from($this->getTable('orbaallegro/mapping_store'), 'store_id')
            ->where('mapping_id = :mapping_id');

        $binds = array(
            ':mapping_id' => (int) $id
        );

        $stores = $adapter->fetchCol($select, $binds);
            
        if($countryCode){
            /* @todo cache data */
            $config = Mage::getSingleton('orbaallegro/config');
            /* @var $config Orba_Allegro_Model_Config */
            $stores = array_intersect($stores, 
                    $config->getStoresByCountryCode($countryCode, true));
        }
        
        return $stores;
    }
}