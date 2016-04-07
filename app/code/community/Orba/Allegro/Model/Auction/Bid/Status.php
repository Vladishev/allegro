<?php
class Orba_Allegro_Model_Auction_Bid_Status{
    public function toOptionHash() {
        return array(
            Orba_Allegro_Model_Auction_Bid::STATUS_BID_SOLD     => 
                Mage::helper("orbaallegro")->__("Sold"),
            Orba_Allegro_Model_Auction_Bid::STATUS_BID_NO_SOLD  => 
                Mage::helper("orbaallegro")->__("Not sold"),
            Orba_Allegro_Model_Auction_Bid::STATUS_BID_CANCELED => 
                Mage::helper("orbaallegro")->__("Canceled"),
        );
    }
}