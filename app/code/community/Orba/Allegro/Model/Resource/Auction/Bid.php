<?php
class   Orba_Allegro_Model_Resource_Auction_Bid extends 
    Mage_Core_Model_Resource_Db_Abstract {
  
    protected function _construct() {
        $this->_init('orbaallegro/auction_bid', 'bid_id');
    } 
    
    
    /**
     * @param Varien_Object $bid
     * @return Mage_Sales_Model_Resource_Order_Collection
     */
    public function getOrderCollection(Varien_Object $bid) {
        $collection = Mage::getResourceModel("sales/order_collection");
        /* @var $collection Mage_Sales_Model_Resource_Order_Collection */
        $select = $collection->getSelect();
        $orderItemTable = $this->getTable('sales/order_item');
        
        $select->join(
                array("order_item"=>$orderItemTable), 
                $this->getReadConnection()->quoteInto(
                        "order_item.order_id=main_table.entity_id AND order_item.orbaallegro_bid_id=?", 
                        $bid->getId()
                ),
                array()
        );
        
        // Order item bid id = this bid id
        $this->prepareOrderConditions($select, "main_table");
        $select->where("main_table.orbaallegro_transaction_id IS NULL");
        $select->group("main_table.entity_id");
        return $collection;
    }
    
    /**
     * Unhandled count = 
     *      Bid qunatity - 
     *      - Auction-linked (not ignored) item quntity - 
     *      - Order with bid and no transaction-id item qty
     * @return Zend_Db_Select
     */
    public function getUnhandledItemCountSelect($includeUnreleasedTransactions=false) {
        
        $stmt = $this->getReadConnection()->select();
        
        $bidTable = $this->getMainTable();
        $transactionTabel = $this->getTable('orbaallegro/transaction');
        $transactionAuctionTabel = $this->getTable('orbaallegro/transaction_auction');
        $orderTable = $this->getTable('sales/order');
        $orderItemTable = $this->getTable('sales/order_item');
        
        $stmt->from(array("uhi_main_table"=>$bidTable), array());
        
        $trItemCountStmt = $this->getReadConnection()->select();
        $trItemCountStmt->from(
                array("uhi_transaction_item"=>$transactionAuctionTabel), 
                array(new Zend_Db_Expr("SUM(uhi_transaction_item.quantity)"))
        );
        
        $trItemCountStmt->join(
                array("uhi_transaction"=>$transactionTabel),
                "uhi_transaction.transaction_id=uhi_transaction_item.transaction_id",
                array()
        );
        
        if($includeUnreleasedTransactions){
            $trItemCountStmt->joinLeft(
                    array("uhi_transaction_order"=>$orderTable),
                    "uhi_transaction_order.entity_id=uhi_transaction.order_id",
                    array()
            );
            $this->prepareOrderConditions($trItemCountStmt, "uhi_transaction_order");
        }
        
        
        $trItemCountStmt->where("uhi_transaction.is_deleted=?",0);
        if($includeUnreleasedTransactions){
            $trItemCountStmt->where("uhi_transaction.order_id IS NOT NULL");
        }
        $trItemCountStmt->where("uhi_transaction_item.bid_id=uhi_main_table.bid_id");
        $trItemCountStmt->group("uhi_transaction_item.bid_id");

        
        // Order item qty 
        $orderItemCount = $this->getReadConnection()->select();
        $orderItemCount->from(
                array("uhi_order_item"=>$orderItemTable), 
                array(new Zend_Db_Expr("SUM(uhi_order_item.qty_ordered)"))
        );
        $orderItemCount->join(
                array("uhi_order"=>$orderTable),
                // Order isn't created via transaction
                "uhi_order.entity_id=uhi_order_item.order_id AND uhi_order.orbaallegro_transaction_id IS NULL",
                array()
        );
        
        // Order item bid id = this bid id
        $this->prepareOrderConditions($orderItemCount, "uhi_order");
        $orderItemCount->where("uhi_order_item.orbaallegro_bid_id=uhi_main_table.bid_id");
        $orderItemCount->group("uhi_order_item.orbaallegro_bid_id");
        
        
        // Set count column
        $stmt->columns(array(
            new Zend_Db_Expr('GREATEST(uhi_main_table.item_quantity - IFNULL(('.$orderItemCount.'), 0) - IFNULL(('.$trItemCountStmt.'), 0), 0)'),
        ));
        
        return $stmt;
    }
    
    /**
     * @param Varien_Object $bid
     * @return int
     * @todo Make as indexer (?)
     */
    public function getUnhandledItemCount(Varien_Object $bid, $unrealsedTransactions=false) {
        $stmt = $this->getUnhandledItemCountSelect($unrealsedTransactions);
        // Assign to current bid
        $stmt->where("uhi_main_table.bid_id=?", $bid->getId());
        $result = $this->getReadConnection()->fetchOne($stmt);
        return $result!==null ? $result : 0;
    }
    
    public function prepareOrderConditions(Zend_Db_Select $select, $orderTabel){
        $select->where($orderTabel.".state<>?", Mage_Sales_Model_Order::STATE_CANCELED);
    }
    
    /**
     * @return Orba_Allegro_Model_Resource_Transaction_Collection
     */
    public function getTransactionCollection() {
        $collection = Mage::getResourceModel("orbaallegro/transaction");
        /* @var $collection Orba_Allegro_Model_Resource_Transaction_Collection */
        // Add join and conditions by bid id
        // $collection->
        return $collection;
    }
    
    /**
     * @param Mage_Core_Model_Abstract $object
     * @param mixed $value - can be array
     * @param type $field optional
     * @return Orba_Allegro_Model_Resource_Auction_Bid
     */
    
    public function load(Mage_Core_Model_Abstract $object, $value, $field = null) {
        if(is_array($value)){
            $read = $this->_getReadAdapter();
            if ($read && !is_null($value)) {
                $select = $this->_getLoadSelectMulti($value, $object);
                $data = $read->fetchRow($select);

                if ($data) {
                    $object->setData($data);
                }
            }

            $this->unserializeFields($object);
            $this->_afterLoad($object);
            return $this;
        }
        parent::load($object, $value, $field);
    }
    
    protected function _prepareDataForSave(Mage_Core_Model_Abstract $object){
        // Times
        $currentTime = Varien_Date::now();
        if ((!$object->getId() || $object->isObjectNew()) && !$object->getCreatedAt()) {
            $object->setCreatedAt($currentTime);
        }
        $object->setUpdatedAt($currentTime);
        return parent::_prepareDataForSave($object);
    }
    
    
    protected function _getLoadSelectMulti(array $fields, $object) {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable());
        foreach ($fields as $key=>$_value){
            $field  = $this->_getReadAdapter()->quoteIdentifier(sprintf('%s.%s', $this->getMainTable(), $key));
            if($_value instanceof Zend_Db_Expr){
                $select->where($field . $_value);
            }else{
                $select->where($field . "=?", $_value);
            }
        }
        return $select;
    }
}