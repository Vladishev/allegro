<?php
class Orba_Allegro_Model_Resource_Transaction extends 
    Mage_Core_Model_Resource_Db_Abstract {
    
    protected function _construct() {
        $this->_init('orbaallegro/transaction', 'transaction_id');
    } 
    
   
    protected function _afterSave(Mage_Core_Model_Abstract $object) {
        if($object->getSerializedData()){
            $this->setSerializedData($object, $object->getSerializedData());
        }
        return parent::_afterSave($object);
    }
    
    /**
     * Set times
     * @param Mage_Core_Model_Abstract $object
     * @return type
     */
    protected function _prepareDataForSave(Mage_Core_Model_Abstract $object)
    {
        // Serilized fields
        if(!is_null($serilized=$object->getAuctionParamsSerialized())){
            if(!is_string($serilized)){
                $serilized = serialize($serilized);
                $this->setAuctionParamsSerialized($serilized);
            }
        }
        
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
                array('s'=>$this->getTable('orbaallegro/transaction_serialized')),
                array("serialized_data")
            )->
            where('s.transaction_id=?', $object->getId());
        
        if(!is_null($result = $adapter->fetchOne($stmt))){
            return unserialize($result);
        }
        
        return null;
    }
    
    public function setSerializedData(Mage_Core_Model_Abstract $object, $data) {
        
        if(!is_string($data)){
            $data = serialize($data);
        }
        
        $adapter = $this->_getWriteAdapter();
        $table = $this->getTable('orbaallegro/transaction_serialized');
        $stmt = $adapter->select();
        $stmt->
            from(
                array('s'=>$table),
                array("transaction_id")
            )->
            where('s.transaction_id=?', $object->getId());
        
        if($adapter->fetchOne($stmt)){
            $adapter->update($table, array("serialized_data"=>$data), 
                $adapter->quoteInto("transaction_id=?", $object->getId())
            );
        }else{
            $adapter->insert($table, 
                array("serialized_data"=>$data, 'transaction_id'=>$object->getId())
            );
        }
        
        return $this;
    }
    
    public function getItemCount(Orba_Allegro_Model_Transaction $trans) {
        
        $adapter = $this->_getReadAdapter();
        $stmt = $adapter->select();
        $stmt->
            from(
                array('transation_auction'=>$this->getTable('orbaallegro/transaction_auction')),
                array("counter"=> new Zend_Db_Expr("SUM(transation_auction.quantity)"))
            )
            ->where('transation_auction.transaction_id=?', $trans->getId())
            ->group('transation_auction.transaction_id');
        
        if(!is_null($result = $adapter->fetchOne($stmt))){
            return $result;
        }
        
        return 0;
    }
    public function getAuctionCount(Orba_Allegro_Model_Transaction $trans) {
        
        $adapter = $this->_getReadAdapter();
        $stmt = $adapter->select();
        $stmt->
            from(
                array('transation_auction'=>$this->getTable('orbaallegro/transaction_auction')),
                array("counter"=> new Zend_Db_Expr("COUNT(transation_auction.transaction_id)"))
            )
            ->where('transation_auction.transaction_id=?', $trans->getId())
            ->group('transation_auction.transaction_id');
        
        if(!is_null($result = $adapter->fetchOne($stmt))){
            return $result;
        }
        
        return 0;
    }
}