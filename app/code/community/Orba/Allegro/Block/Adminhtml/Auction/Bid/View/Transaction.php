<?php
class Orba_Allegro_Block_Adminhtml_Auction_Bid_View_Transaction extends Mage_Adminhtml_Block_Widget {

    /**
     *  @return Orba_Allegro_Model_Auction_Bid
     */
    public function getModel() {
        return Mage::registry('orbaallegro_current_auction_bid');
    }

    /**
     * @return Orba_Allegro_Model_Resource_Transaction_Collection
     */
    public function getCollection() {
        if(!$this->hasData("collection")){
            $bid = $this->getModel();
            $collection = $bid->getTransactionCollection();
            $collection->addActiveFilter();
            $collection->addBidCount($bid);
            $this->setData("collection", $collection);
        }
        return $this->getData("collection");
    }
    
    public function getRowClass($item) {
        return Mage::helper("orbaallegro")->getTransactionRowClass($item);
    }
    
}
