<?php
class Orba_Allegro_Model_Resource_Pickpoint extends 
    Mage_Core_Model_Resource_Db_Abstract {
    
    protected function _construct() {
        $this->_init('orbaallegro/pickpoint', 'pickpoint_id');
    } 
   
    
    /**
     * Set times
     * @param Mage_Core_Model_Abstract $object
     * @return type
     */
    protected function _prepareDataForSave(Mage_Core_Model_Abstract $object)
    {
        // Times
        $currentTime = Varien_Date::now();
        if ((!$object->getId() || $object->isObjectNew()) && !$object->getCreatedAt()) {
            $object->setCreatedAt($currentTime);
        }
        $object->setUpdatedAt($currentTime);
        return parent::_prepareDataForSave($object);
    }
    
    /**
     * @param Mage_Core_Model_Abstract $object
     * @return mixed
     */
    public function getSerializedData(Mage_Core_Model_Abstract $object) {
        $adapter = $this->_getReadAdapter();
        $stmt = $adapter->select();
        $stmt->
            from(
                array('s'=>$this->getTable('orbaallegro/auction_serialized')),
                array("serialized_data")
            )->
            where('s.auction_id=?', $object->getId());
        
        if(is_null($result = $adapter->fetchOne($stmt))){
            return unserialize($result);
        }
        
        return null;
    }
    
    public function setSerializedData(Mage_Core_Model_Abstract $object, $data) {
        
        if(!is_string($data)){
            $data = serialize($data);
        }
        
        $adapter = $this->_getWriteAdapter();
        $table = $this->getTable('orbaallegro/auction_serialized');
        $stmt = $adapter->select();
        $stmt->
            from(
                array('s'=>$table),
                array("auction_id")
            )->
            where('s.auction_id=?', $object->getId());
        
        if($adapter->fetchOne($stmt)){
            $adapter->update($table, array("serialized_data"=>$data), 
                $adapter->quoteInto("auction_id=?", $object->getId())
            );
        }else{
            $adapter->insert($table, 
                array("serialized_data"=>$data, 'auction_id'=>$object->getId())
            );
        }
        
        return $this;
    }
}