<?php
/**
 * @method Varien_Object getInvoiceData
 * @method Varien_Object getCompanyExtraData
 * @method string getCompanySecondAddress
 * @method Varien_Object getPharmacyData
 * @method Varien_Object getAlcoholData
 * @method string getRelatedPersons
 * @method int getUserId
 * @method string getUserLogin
 * @method string getUserRating
 * @method string getUserFirstName
 * @method string getUserLastName
 * @method string getUserMaidenName
 * @method string getUserCompany
 * @method string getUserCountryId
 * @method string getUserStateId
 * @method string getUserPostcode
 * @method string getUserCity
 * @method string getUserAddress
 * @method string getUserEmail
 * @method string getUserPhone
 * @method string getUserPhone2
 * @method string getUserSsStatus
 * @method string getSiteCountryId
 * @method string getUserJuniorStatus
 * @method string getUserBirthDate
 * @method int getUserHasShop
 * @method string getUserCompanyIcon
 * @method string getUserIsAllegroStandard
 * 
 */
class Orba_Allegro_Model_User extends Varien_Object {
    
    protected $_storeId;
    protected $_isLoaded = false;
    
    public function __construct($storeId=null, $autoload=true) {
        if(null===$storeId){
            $storeId = Mage::app()->getStore()->getId();
        }
        
        $this->setStoreId($storeId);
        
        parent::__construct();
        
        if($autoload){
            $this->load();
        }
    }
    
    public function setStoreId($storeId) {
        $this->_storeId = $storeId;
    }
    
    public function getStoreId() {
        return $this->_storeId;
    }
    
    public function load() {
        if(!$this->_isLoaded){
            $this->_load();
        }
    }
    
    protected function _load() {
        
        $client = Mage::getModel("orbaallegro/client");
        /* @var $client Orba_Allegro_Model_Client */
        $client->setStoreId($this->getStoreId());
        
        
        $data = null;
        try{
            $data = $client->getUserData();
        }catch(Exception $e){
            Mage::logException($e);
        }
        
        
        if($data && is_object($data)){
            $this->setData($this->_recursiveUnderscore($data));
            if($this->getUserData()){
                // Copy basic data direct to ubject
                $this->addData($this->getUserData()->getData());;
                $this->unsUserData();
            }
            $this->_isLoaded = true;
        }
        
        
        
    }
    
    protected function _recursiveUnderscore($data) {
        $_data = array();
        foreach ($data as $key => $value) {
            if(is_object($value)){
                $value = new Varien_Object($this->_recursiveUnderscore((array)$value));
            }
            $_data[$this->_underscore($key)] = $value;
        }
        return $_data;
    }
    
}