<?php
abstract class Orba_Allegro_Model_System_Config_Source_Abstract {
    
    protected $_countryCode;

    protected $_noItems;
    
    public function __construct() {
        $this->_noItems = array(array(
            "label"=>Mage::helper("orbaallegro")->__("-- No Items --"), 
            "value"=>""
        )); 
    }
    
    public function getCountryCode() {
        return $this->_countryCode;
    }
    
    public function setCountryCode($value) {
        $this->_countryCode = $value;
        return $this;
    }
    
    public function getStore() {
        return Mage::app()->getRequest()->getParam("store");
    }
    
    public function getWebsite() {
        return Mage::app()->getRequest()->getParam("website");
    }
    
    
    /**
     * @return Orba_Allegro_Model_Config;
     */
    protected function _getConfig() {
        return Mage::getSingleton('orbaallegro/config');
    }
    
    
    /**
     * Get value usgin orballegro/config method. 
     * @param strim $method
     * @return mixed
     * @throws Orba_Allegro_Exception
     */
    protected function _getAdminhtmlValue($method){
        $websiteCode = $this->getWebsite();
        $storeCode = $this->getStore();
        $config = $this->_getConfig();
        
        
        if(!method_exists($config, $method)){
            throw new Orba_Allegro_Exception("No method $method found");
        }
        $value  = $config->{$method}($storeCode, $websiteCode);
        
        return $value;
    }
    
    /**
     * Get value usgin orballegro/config duttent value. 
     * @param strim $method
     * @return mixed
     * @throws Orba_Allegro_Exception
     */
    protected function _getAdminhtmlConfigValue($path){
        $websiteCode = $this->getWebsite();
        $storeCode = $this->getStore();
        $config = $this->_getConfig();
        return $config->getConfig($path ,$storeCode, $websiteCode);
    }
    
    public function toOptionHash() {
        $out = array();
        foreach ($this->toOptionArray() as $item){
            $out[$item['value']] = $item['label'];
        }
        return $out;
    }

}
