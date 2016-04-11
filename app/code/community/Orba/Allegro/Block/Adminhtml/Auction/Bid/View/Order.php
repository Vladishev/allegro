<?php
class Orba_Allegro_Block_Adminhtml_Auction_Bid_View_Order extends Mage_Adminhtml_Block_Widget {

    /**
     *  @return Orba_Allegro_Model_Auction_Bid
     */
    public function getModel() {
        return Mage::registry('orbaallegro_current_auction_bid');
    }

    /**
     * @return Mage_Sales_Model_Resource_Order_Collection
     */
    public function getCollection() {
        if(!$this->hasData("collection")){
            $bid = $this->getModel();
            $collection = $bid->getOrderCollection();
            $this->setData("collection", $collection);
        }
        return $this->getData("collection");
    }
    
    public function getOrderBidItemCount(Mage_Sales_Model_Order $order) {
        $count = 0;
        $bid = $this->getModel();
        foreach($order->getAllItems() as $item){
            /* @var $item Mage_Sales_Model_Order_Item */
            if($item->getOrbaallegroBidId()==$bid->getId()){
                $count+=$item->getQtyOrdered();
            }
        }
        return $count;
        
    }
    
}
