<?php

class Orba_Allegro_Model_Transaction_Auction extends Mage_Core_Model_Abstract {

    protected $_itemMap = array(
        "postBuyFormItQuantity" => "quantity",
        "postBuyFormItAmount"   => "amount",
        "postBuyFormItPrice"    => "price",
        "postBuyFormItTitle"    => "title",
    );
    
    protected function _construct() {
        $this->_init('orbaallegro/transaction_auction');
    }
    
    /**
     * @return Orba_Allegro_Model_Resource_Transaction_Auction
     */
    public function getResource() {
        return parent::getResource();        
    }

    /**
     * @return Orba_Allegro_Model_Resource_Transaction_Auction_Collection
     */
    public function getCollection() {
       return parent::getCollection();
    }
    
    /**
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct() {
        if($auction=$this->getAuction()){
            return $auction->getProduct()->setStoreId($auction->getStore()->getId());
        }
        return null;
    }
    
    /**
     * @return Orba_Allegro_Model_Auction
     */
    public function getAuction() {
        if(!$this->hasData('auction')){
            $auction=Mage::getModel("orbaallegro/auction")->load($this->getAuctionId());
            $this->setData('auction', $auction);
        }
        return $this->getData("auction");
    }
    
    public function bindAllegroItemData($data) {
        if(isset($data->postBuyFormItId) && !empty($data->postBuyFormItId)){
            $auction = Mage::getModel("orbaallegro/auction")->
                load($data->postBuyFormItId, "allegro_auction_id");
            
            
            if($auction->getId()){
                $this->setData($this->_mapAllegroItemObejct($data));
                $this->setAuctionId($auction->getId());
            }
        }
        return $this;
    }
    
    protected function _mapAllegroItemObejct($data) {
        $out = array();
        foreach($data as $key=>$value){
            if(isset($this->_itemMap[$key]) && !empty($value)){
                $out[$this->_itemMap[$key]] = $value;
            }
        }
        return $out;
    }
}