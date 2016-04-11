<?php
class Orba_Allegro_Model_Resource_Transaction_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract {

    protected function _construct() {
        parent::_construct();
        $this->_init('orbaallegro/transaction');
    }
    
    public function addBidCount(Orba_Allegro_Model_Auction_Bid $bid) {
        $baseSelect = $this->getSelect();
        $select = $baseSelect->getAdapter()->select();
        $select->from(
            array("transaction_auction_item" => $this->getTable("orbaallegro/transaction_auction")),
            array(new Zend_Db_Expr("IFNULL(transaction_auction_item.quantity, 0)"))
        );
        $select->where("transaction_auction_item.transaction_id=main_table.transaction_id");
        $select->where("transaction_auction_item.bid_id=?", $bid->getId());

        $baseSelect->columns(array("bid_item_count"=>$select));
    }
    
    public function addBidFilter($bid) {
        if($bid instanceof Orba_Allegro_Model_Auction_Bid){
            $bid = $bid->getId();
        }
        $baseSelect = $this->getSelect();
        $onExpression =  "transaction_auction.transaction_id=main_table.transaction_id".
                " AND transaction_auction.bid_id=?";
        $baseSelect->join(
            array("transaction_auction"=>$this->getTable("orbaallegro/transaction_auction")),
            $baseSelect->getAdapter()->quoteInto($onExpression, $bid),
            array()
        );
        $baseSelect->group('main_table.transaction_id');
    }
    
    public function addItemCount() {
        $baseSelect = $this->getSelect();
        $select = $baseSelect->getAdapter()->select();
        $select->from(
            array("transaction_item" => $this->getTable("orbaallegro/transaction_auction")),
            array(new Zend_Db_Expr('SUM(transaction_item.quantity)'))
        );
        $select->where("transaction_item.transaction_id=main_table.transaction_id");
        
        $baseSelect->columns(array("item_count"=>$select));
        
        return $this;
    }

    /**
     * @return Orba_Allegro_Model_Resource_Transaction_Collection
     */
    public function addActiveFilter() {
        $this->addFieldToFilter("is_ignored", 0);
        return $this;
    }
    
    public function addIncrementId(){
        $select = $this->getSelect();
        $select->joinLeft(
            array("order"=>$this->getTable("sales/order")),
            "order.entity_id=main_table.order_id",
            array('increment_id')
        );
        return $this;
    }
    
    public function addAuctionFilter($auction, $count=true) {
        if($auction instanceof Orba_Allegro_Model_Auction){
            $auction = $auction->getId();
        }
        
        $baseSelect = $this->getSelect();
        $onExpression =  "transaction_auction.transaction_id=main_table.transaction_id".
                " AND transaction_auction.auction_id=?";
        
       
        $baseSelect->join(
            array("transaction_auction"=>$this->getTable("orbaallegro/transaction_auction")),
            $baseSelect->getAdapter()->quoteInto($onExpression, $auction),
            array()
        );
        $baseSelect->group('main_table.transaction_id');
        
        if($count){
            $select = $baseSelect->getAdapter()->select();
            $select->from(
                array("transaction_auction_item" => $this->getTable("orbaallegro/transaction_auction")),
                array(new Zend_Db_Expr('SUM(transaction_auction_item.quantity)'))
            );
            $select->where("transaction_auction_item.transaction_id=main_table.transaction_id");
            $select->where("transaction_auction_item.auction_id=?", $auction);

            $baseSelect->columns(array("auction_item_count"=>$select));
        }
        
        return $this;
    }

}