<?php
class Orba_Allegro_Model_Resource_Auction_Bid_Note_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract {
    
    protected function _construct() {
        parent::_construct();
        $this->_init('orbaallegro/auction_bid_note');
    }
    
    /**
     * @param int|Orba_Allegro_Model_Auction_Bid $bid
     * @return Orba_Allegro_Model_Resource_Auction_Bid_Note_Collection
     */
    public function addBidFilter($bid) {
        if($bid instanceof Orba_Allegro_Model_Auction_Bid){
            $bid = $bid->getId();
        }
        $this->addFieldToFilter("main_table.bid_id", $bid);
        return $this;
    }
    
}