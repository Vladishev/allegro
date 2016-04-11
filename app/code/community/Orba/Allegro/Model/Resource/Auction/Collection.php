<?php
class Orba_Allegro_Model_Resource_Auction_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract {
    
    const JUST_CLOSED = 1; // Just ended auctions interval

    protected function _construct() {
        parent::_construct();
        $this->_init('orbaallegro/auction');
    }
	

    /**
     * @return Orba_Allegro_Model_Resource_Auction_Collection
     */
    public function addSerializedData() {
        $this->getSelect()->joinLeft(
            array("s" => $this->getTable('orbaallegro/auction_serialized')),
            "s.auciton_id=main_table.auction_id",
            array("serialized_data")
        );
        return $this;
    }

	/**
	 * @return Orba_Allegro_Model_Resource_Auction_Collection
	 */
	public function addDoRenewFilter() {
		 $this->addFieldToFilter("do_renew", 1);
		return $this;
	}
	
    /**
     * @return Orba_Allegro_Model_Resource_Auction_Collection
     */
    public function addToVerfiyFilter() {
        $this->addFieldToFilter("auction_status", array("in"=>array(
            Orba_Allegro_Model_Auction_Status::STATUS_NO_VERIFIED,
            Orba_Allegro_Model_Auction_Status::STATUS_WAITING,
            Orba_Allegro_Model_Auction_Status::STATUS_SELL_AGIAN,
            Orba_Allegro_Model_Auction_Status::STATUS_INCOMING
        )));
        return $this;
    }
    
    /**
     * @return Orba_Allegro_Model_Resource_Auction_Collection
     */
    public function addPlacedFilter() {
        $this->addFieldToFilter("auction_status", Orba_Allegro_Model_Auction_Status::STATUS_PLACED);
        return $this;
    }
    
    /**
     * @return Orba_Allegro_Model_Resource_Auction_Collection
     */
    public function addPlacedOrJustClosedFilter(){
        $select = $this->getSelect();
        $adapter = $select->getAdapter();
        
        $justClosed = new Zend_Date();
        $justClosed->subDay(self::JUST_CLOSED);
        
        $ors = array(
            // Status = place
            $adapter->quoteInto("main_table.auction_status=?", Orba_Allegro_Model_Auction_Status::STATUS_PLACED),
            // Status = (finished|ended|/*canceld*/) AND finished time IS NOT NULL AND finished time < jsutClosed
            
            "(" . 
                $adapter->quoteInto("main_table.auction_status IN (?)",array( 
                    Orba_Allegro_Model_Auction_Status::STATUS_FINISHED,
                    Orba_Allegro_Model_Auction_Status::STATUS_ENDED,
                    /*Orba_Allegro_Model_Auction_Status::STATUS_CANCELED,*/
                )) . 
            ")".
            " AND ".
                "(main_table.closed_at IS NOT NULL)".
            " AND ".
                "(".$adapter->quoteInto("main_table.closed_at > ?", $justClosed->toString(Varien_Date::DATETIME_INTERNAL_FORMAT)).")"
        );
        
        $select->where("(".join(") OR (", $ors).")");
        
        return $this;
    }
    
    /**
     * @return Orba_Allegro_Model_Resource_Auction_Collection
     */
    public function addTransactionItemCount($activeOnly=false) {
        $baseSelect = $this->getSelect();
        $select = $baseSelect->getAdapter()->select();
        $select->from(
            array("transaction_item" => $this->getTable("orbaallegro/transaction_auction")),
            array(new Zend_Db_Expr('IFNULL(SUM(transaction_item.quantity),0)'))
        );
        if($activeOnly){
            $select->join(
                array("transaction"=>$this->getTable("orbaallegro/transaction")),
                "transaction_item.transaction_id=transaction.transaction_id",
                array()
            );
            $select->where("transaction.is_ignored=?", 0);
        }
        $select->where("transaction_item.auction_id=main_table.auction_id");
        $baseSelect->columns(array("transaction_item_count"=>$select));
        return $this;
    }
    
    /**
     * @return Orba_Allegro_Model_Resource_Auction_Collection
     */
    public function addStockQty() {
        if (Mage::helper('orbaallegro')->isModuleEnabled('Mage_CatalogInventory')) {
            $this->getSelect()->joinLeft(
                array("stock"=>$this->getTable("cataloginventory/stock_item")),
                "stock.product_id=main_table.product_id AND stock.stock_id=1",
                array('stock_qty'=>'stock.qty')
            );
        }
        return $this;
    }

    /**
     * @return Orba_Allegro_Model_Resource_Auction_Collection
     */
    public function addEndTime() {
        $this->addExpressionFieldToSelect("end_time",
                new Zend_Db_Expr("IFNULL(main_table.ending_at, IF(main_table.auction_status='placed', DATE_ADD(FROM_UNIXTIME(main_table.allegro_starting_time), INTERVAL main_table.auction_duration DAY), NULL))"), array());
        return $this;
    }

    public function addTransactionFilter($transaction){
        
        if($transaction instanceof Orba_Allegro_Model_Transaction){
            $transaction = $transaction->getId();
        }
        
        $select = $this->getSelect();
        $select->join(
                array("transaction_auction"=>$this->getTable("orbaallegro/transaction_auction")),
                "transaction_auction.auction_id=main_table.auction_id",
                array()
        );
        $select->where("transaction_auction.transaction_id=?", $transaction);
    }

}