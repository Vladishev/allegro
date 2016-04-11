<?php
class Orba_Allegro_Model_Resource_Auction_Bid_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract {
    
    protected function _construct() {
        parent::_construct();
        $this->_init('orbaallegro/auction_bid');
    }
    
    public function addAuctionFilter($auctionId) {
        if($auctionId instanceof Orba_Allegro_Model_Auction){
            $auctionId = $auctionId->getId();
        }
        $this->addFieldToFilter("auction_id", $auctionId);
    }
    
    public function bidStatusFilter($status) {
        $this->addFieldToFilter("bid_status", $status);
        return $this;
    }
    
    public function addContractorData() {
        $baseSelect = $this->getSelect();
        $baseSelect->joinLeft(
                array("contractor"=>$this->getTable('orbaallegro/contractor')), 
                "main_table.contractor_id=contractor.contractor_id"
        );
        return $this;
    }
    
    public function addTotal() {
        $this->getSelect()->columns(array(
            "total"=>new Zend_Db_Expr("main_table.item_quantity * main_table.item_price")
        ));
        return $this;
    }
    
    public function joinAuction(array $fields=array("*")) {
        $baseSelect = $this->getSelect();
        $baseSelect->joinLeft(
                array("auction"=>$this->getTable('orbaallegro/auction')), 
                "main_table.auction_id=auction.auction_id",
                $fields
        );
        return $this;
    }


    /**
     * @return Orba_Allegro_Model_Resource_Auction_Bid_Collection
     */
    public function addUnhandledItemInfo($includeUnreleasedTransactions=false) {
        $stmt = $this->getResource()->getUnhandledItemCountSelect($includeUnreleasedTransactions);
        /* @var $stmt Zend_Db_Select */
        $stmt->where("uhi_main_table.bid_id=main_table.bid_id");
        
        $this->getSelect()->columns(array(
            "unhandled_item_count" => $stmt
        ));
        
        return $this;
    }
    
    public function addTransactionInfo($withTransCount=false) {
        $baseSelect = $this->getSelect();
        
        // @todo check is deleted in trnsaction
       
        // Transaction Items number
        $trItemCountStmt = $baseSelect->getAdapter()->select();
        $trItemCountStmt->from(
                array("transaction_item1"=>$this->getTable("orbaallegro/transaction_auction")), 
                array(new Zend_Db_Expr("SUM(transaction_item1.quantity)"))
        );
        
        $trItemCountStmt->join(
                array("transaction1"=>$this->getTable("orbaallegro/transaction")),
                "transaction1.transaction_id=transaction_item1.transaction_id",
                array()
        );
        
        $trItemCountStmt->where("transaction1.is_deleted=?",0);
        $trItemCountStmt->where("transaction1.is_ignored=?",0);
        $trItemCountStmt->where("transaction_item1.bid_id=main_table.bid_id");
        $trItemCountStmt->group("transaction_item1.bid_id");

        $baseSelect->columns(array(
             "lonely_items"          => new Zend_Db_Expr('main_table.item_quantity - IFNULL(('.$trItemCountStmt.'), 0)'), 
        ));
        
        if($withTransCount){
            // Transaction count related with bid
            $trCountStmt = $baseSelect->getAdapter()->select();
            $trCountStmt->from(
                    array("transaction_item"=>$this->getTable("orbaallegro/transaction_auction")), 
                    array(new Zend_Db_Expr("COUNT(DISTINCT transaction_item.transaction_id)"))
            );
            
            $trCountStmt->join(
                    array("transaction2"=>$this->getTable("orbaallegro/transaction")),
                    "transaction2.transaction_id=transaction_item.transaction_id",
                    array()
            );
            
            $trCountStmt->where("transaction2.is_deleted=?",0);
            $trCountStmt->where("transaction2.is_ignored=?",0);
            $trCountStmt->where("transaction_item.bid_id=main_table.bid_id");
            $trCountStmt->group("transaction_item.bid_id");
            
            $baseSelect->columns(array(
                "transaction_count"     => new Zend_Db_Expr('IFNULL(('.$trCountStmt.'), 0)'),
            ));
        
        }
        
  
        return $this;
    }
    
    public function addOrderItemInfo() {
        $baseSelect = $this->getSelect();
        
        // Order item qty 
        $itCount = $baseSelect->getAdapter()->select();
        $itCount->from(
                array("order_item"=>$this->getTable("sales/order_item")), 
                array(new Zend_Db_Expr("SUM(order_item.qty_ordered)"))
        );
        $itCount->join(
                array("order"=>$this->getTable("sales/order")),
                // Order isn't created via transaction
                "order.entity_id=order_item.order_id AND order.orbaallegro_transaction_id IS NULL",
                array()
        );
        
        $this->getResource()->prepareOrderConditions($itCount, "order");
        
        // Order item bid id = this bid id
        $itCount->where("order_item.orbaallegro_bid_id=main_table.bid_id");
        
        $itCount->group("order_item.orbaallegro_bid_id");
        
        
        $baseSelect->columns(array(
            "order_item_count"      => new Zend_Db_Expr('IFNULL(('.$itCount.'), 0)'),
        ));
        
        
        return $this;
    }

}