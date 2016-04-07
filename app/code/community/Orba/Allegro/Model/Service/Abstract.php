<?php
/**
 * Special class for custom behavior per Allegro service
 */
abstract class Orba_Allegro_Model_Service_Abstract extends Mage_Core_Model_Abstract
{    
    protected $_resourceName = "orbaallegro/service";
    protected $_resourceCollectionName = "orbaallegro/service_collection";
    
    protected $_code; // Could be overriden;
    
    protected $_pickpointProviderIds;
    protected $_pickpointIds;
    protected $_providerByPickpoint;
    protected $_providerNameById;
    protected $_ignoredInEdit = array();


    abstract function getAuctionLink($auctionId);
    abstract function getCommentsLink($userId);
    abstract function getAboutLink($userId);
    abstract function getAucitonListLink($userId);
    abstract function getShopLink($userId);
    abstract function getAddFavouritesLink($userId);
    abstract function getContactLink($userId);
    
    abstract function isShopAvailable();
    
    public function getServiceCode() {
        return $this->_code;
    }
    
    public function load($id=null, $field=null) {
        return parent::load($this->_code, "service_code");
    }
    
    public function checkIsPickpoint($shipmentId){
        return in_array($shipmentId, $this->_pickpointIds);
    }
    
    public function getAllPickpoints() {
        return $this->_pickpointIds;
    }
    
    public function getAllPickpointProviders() {
        return $this->_pickpointProviderIds;
    }
    
    public function getProviderByPickpoint($pickpointId){
        return isset($this->_providerByPickpoint[$pickpointId]) ? $this->_providerByPickpoint[$pickpointId] : null;
    }
    
    public function getProviderNameById($providerId){
        return isset($this->_providerNameById[$providerId]) ? $this->_providerNameById[$providerId] : null;
    }
    
    public function getIgnoredInEdit(){
        return $this->_ignoredInEdit;
    }
    
    public function getSurchargeSumText() {
        return $this->_surchargeSumText;
    }
}