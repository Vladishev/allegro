<?php

class Orba_Allegro_Model_Transaction_Address extends Mage_Core_Model_Abstract {

    const TYPE_BILLING = "billing";
    const TYPE_SHIPPING = "shipping";
    const TYPE_PICKPOINT = "pickpoint";
    
    protected $_addressMap = array(
        "postBuyFormAdrCountry"     => "country_code",
        "postBuyFormAdrStreet"      => "street",
        "postBuyFormAdrPostcode"    => "postcode",
        "postBuyFormAdrCity"        => "city",
        "postBuyFormAdrFullName"    => "fullname",
        "postBuyFormAdrCompany"     => "company",
        "postBuyFormAdrPhone"       => "phone",
        "postBuyFormAdrNip"         => "vatid"
    );


    protected function _construct() {
        $this->_init('orbaallegro/transaction_address');
    }
    
    /**
     * @return Orba_Allegro_Model_Transaction
     */
    public function getTransaction() {
        if(!$this->hasData("transaction")){
            $this->setData("transaction", Mage::getModel("orbaallegro/transaction")->load($this->getTransactionId()));
        }
        return $this->getData("transaction");
    }

    /** 
     * @param array $fields
     */
    public function addContractorDataIfEmtpy(array $fields) {
        $transaction = $this->getTransaction();
        $auctions = $transaction->getAuctionCollection();
        $auciton = $auctions->getFirstItem();
        $isIncomplete = false;
        foreach($fields as $field=>$allegroField){
            if(!$to->hasData($field) || $to->getData($field)==""){
                $isIncomplete = true;
                break;
            }
        }
        if($isIncomplete && $auciton && $auciton->getId()){
            $data = $auciton->allegroGetAuctionContractosrData($transaction->getAllegroBuyerId());
            var_dump($data);
            die;
        }
    }
    
    /**
     * @return bool
     */
    public function isPickpoint() {
        return strtolower($this->getType())==self::TYPE_PICKPOINT && (bool)$this->getPickpoint()->getId();
    }
    
    /**
     * @return Orba_Allegro_Model_Pickpoint
     */
    public function getPickpoint() {
        if(!$this->hasData('pickpoint')){
            $pickpoint = Mage::getModel("orbaallegro/pickpoint");
            if($this->getPickpointId()){
                $pickpoint->load($this->getPickpointId());
            }
            $this->setData("pickpoint", $pickpoint);
        }
        return $this->getData("pickpoint");
    }
    
    public function getCountryId() {
        return Mage::helper("orbaallegro")->getCountryMapped($this->getCountryCode());
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
    
    public function bindAllegroAddressData($data) {
        $this->setData($this->_mapAllegroAddressObejct($data));
        return $this;
    }
    
    protected function _mapAllegroAddressObejct($data) {
        $out = array();
        foreach($data as $key=>$value){
            if(isset($this->_addressMap[$key]) && !empty($value)){
                $out[$this->_addressMap[$key]] = $value;
            }
        }
        return $out;
    }
    
    
    
}