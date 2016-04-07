<?php
class Orba_Allegro_Model_Auction_Bid extends Mage_Core_Model_Abstract{
    
    const STATUS_USER_ACTIVE = 0;
    const STATUS_USER_BLOCKED = 1;
    
    const STATUS_BID_SOLD = 1;
    const STATUS_BID_NO_SOLD = 0;
    const STATUS_BID_CANCELED = -1;
    
    const STATUS_CANCEL_NO = 0;
    const STATUS_CANCEL_SELLER = 1;
    const STATUS_CANCEL_ADMIN = 2;


    protected function _construct() {
        $this->_init('orbaallegro/auction_bid');
    }
    
    /**
     * @return Orba_Allegro_Model_Resource_Auction_Bid
     */
    public function getResource() {
        return parent::getResource();        
    }

    /**
     * @return Orba_Allegro_Model_Resource_Auction_Bid_Collection
     */
    public function getCollection() {
       return parent::getCollection();
    }
    
    /**
     * @return Orba_Allegro_Model_Resource_Auction_Bid_Note_Collection
     */
    public function getNoteCollection() {
        $collection = Mage::getResourceModel("orbaallegro/auction_bid_note_collection");
        /* @var $collection Orba_Allegro_Model_Resource_Auction_Bid_Note_Collection */
        $collection->addBidFilter($this);
        return $collection;
    }
    
    /**
     * @return int
     */
    public function getUnhandledItemCount($unrealsedTransactions=false) {
        if($this->getId()){
            return $this->getResource()->getUnhandledItemCount($this, $unrealsedTransactions);
        }
        return 0;
    }

    /**
     * return Mage_Sales_Model_Resource_Order_Collection
     */
    public function getOrderCollection() {
        return $this->getResource()->getOrderCollection($this);
    }

    /**
     * @return Orba_Allegro_Model_Resource_Transaction_Collection
     */
    public function getTransactionCollection() {
        $collection = Mage::getResourceModel("orbaallegro/transaction_collection");
        /* @var $collection Orba_Allegro_Model_Resource_Transaction_Collection */
        $collection->addBidFilter($this);
        return $collection;
    }
    
    /**
     * @return string
     */
    public function getBidStatusText() {
        $map = Mage::getSingleton('orbaallegro/auction_bid_status')->toOptionHash();
        if(isset($map[$this->getBidStatus()])){
            return $map[$this->getBidStatus()];
        }
        return '';
    }
    
    /**
     * @return Orba_Allegro_Model_Contractor|null
     */
    public function getContractor() {
        if(!$this->getContractorId()){
            return null;
        }
        if(!$this->hasData("contractor")){
            $contractor = Mage::getModel("orbaallegro/contractor")->
                    load($this->getContractorId());
            $this->setData("contractor", $contractor->getId() ? $contractor : false);
        }
        return $this->getData("contractor")===false ? 
                null : $this->getData("contractor");
    }
    
    /**
     * @return Orba_Allegro_Model_Auction
     */
    public function getAuction() {
        if(!$this->hasData("auction")){
            $auction = Mage::getModel("orbaallegro/auction")->
                    load($this->getAuctionId());
            $this->setData("auction", $auction);
        }
        return $this->getData("auction");
    }
    
    /**
     * @param type $id
     * @param array $values
     * @return Orba_Allegro_Model_Auction_Bid
     */
    public function loadMulti(array $values) {
        $this->_beforeLoad($values, null);
        $this->getResource()->load($this, $values);
        $this->_afterLoad();
        $this->setOrigData();
        $this->_hasDataChanges = false;
        return $this;
    }
    
    public function bindAllegroData($bidItem) {
        $this->addData(array(         
            'item_price'            => $bidItem[6],
            'item_quantity'         => $bidItem[5],       
            'buyer_login'           => $bidItem[2],
            'buyer_status'          => $bidItem[4],
            'bid_status'            => $bidItem[8],          
            'cancel_status'         => $bidItem[11],       
            'allegro_created_at'    => $bidItem[7]
        ));
        if(!$this->getId()){
            $this->addData(array(
                'allegro_auction_id'=> $bidItem[0],  
                "allegro_user_id"   => $bidItem[1]
            ));
        }
        if(!empty($bidItem[9])){
            $this->setAllegroCanceledAt($bidItem[9]);
        }
        if(!empty($bidItem[10])){
            $this->setCancelReason($bidItem[10]);
        }
    }
    
    protected function _beforeSave() {
        if(!$this->getId()){
            // Auction relation
            if($this->getAllegroAuctionId()){
                $auction = Mage::getModel("orbaallegro/auction")->
                    load($this->getAllegroAuctionId(), "allegro_auction_id");
                if($auction->getId()){
                    $this->setAuctionId($auction->getId());
                }
            }
            // Contractor relation
            if($this->getAllegroUserId()){
                $contractor = Mage::getModel("orbaallegro/contractor")->
                    load($this->getAllegroUserId(), "allegro_user_id");
                if($contractor->getId()){
                    $this->setContractorId($contractor->getId());
                }
            }            
        }
        return parent::_beforeSave();
    }
    
    /**
     * @return Mage_Directory_Model_Currency
     */
    public function getCurrencyModel() {
        return $this->getAuction()->getCurrencyModel();
    }
}