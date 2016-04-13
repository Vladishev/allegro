<?php
class Orba_Allegro_Adminhtml_System_ConfigController 
    extends Orba_Allegro_Controller_Adminhtml_Abstract {
    
    
    public function loginAction() {
        $helper = Mage::helper('orbaallegro');
        
        $request = $this->getRequest();
        $clientData = $request->getParams();
        $store = $request->getParam('store');
        $website = $request->getParam('website');
        $result = array('result'=>false);
        
        $config = Mage::getModel("orbaallegro/config");
        /* @var $config Orba_Allegro_Model_Config */
        
        if(!$helper->canLogin($store, $website, $clientData)){
            $result['content'] = $helper->__("Wrong login data");
            return $this->_sendJson($result);
        }
        
        // Save config data
        $this->_saveConfigData($clientData, $store, $website);
        
        $result['result'] = true;
        $result['content'] = Mage::getModel("orbaallegro/system_config_source_service")->toOptionArray();
        
        return $this->_sendJson($result);
        
    }
    
    public function loginAndImportAction() {
        $helper = Mage::helper('orbaallegro');
        if($helper->isFirstImportComplete()){
            return false;
        }
        
        $request = $this->getRequest();
        $result = array('result'=>false);
        $store = $request->getParam('store');
        $website = $request->getParam('website');
        $clientData = $request->getParams();
        
        // Cannot login
        if(!$helper->canLogin($store, $website, $clientData)){
            $result['content'] = $helper->__("Wrong login data");
            return $this->_sendJson($result);
        }
        
        // Save config data
        $this->_saveConfigData($clientData, $store, $website);

        // Process setup based on basic data
        $setup = Mage::getResourceModel("orbaallegro/setup", "core_setup");
        /* @var $setup Orba_Allegro_Model_Resource_Setup */
        
        $setup->doFirstImport();
        
        $result['result'] = true;
        $result['content'] = Mage::getModel("orbaallegro/system_config_source_service")->toOptionArray();
        
        
        return $this->_sendJson($result);
    }
    
    /**
     * Get Countries
     * @return
     */
    public function getCountriesJsonAction() {
        return $this->_sendJson(
            Mage::getModel("orbaallegro/system_config_source_country")->
            setCountryCode((int)$this->getRequest()->getPost('country_code'))->
            toOptionArray()
        );
    }
    
    /**
     * Get provinces
     * @return 
     */
    public function getProvincesJsonAction() {
        return $this->_sendJson(
            Mage::getModel("orbaallegro/system_config_source_province")->
            setCountryCode((int)$this->getRequest()->getPost('country_code'))->
            toOptionArray()
         );
    }
    
    /**
     * @param array $clinetData
     * @param string|int $store
     * @param string|int $website
     */
    protected function _saveConfigData(array $clientData, $store=null, $website=null) {
        
        if($store){
            $scope = "stores";
            $scopeId = Mage::getModel("core/store")->load($store, "code")->getId();
        }elseif($website){
            $scope = "websites";
            $scopeId = Mage::getModel("core/website")->load($website, "code")->getId();
        }else{
            $scope = "default";
            $scopeId = Mage_Core_Model_App::ADMIN_STORE_ID;
        }
        
        // Correct data - save to config
        $config = Mage::getModel("core/config");
        /* @var $config Mage_Core_Model_Config */
        
        $clientData = $this->_extractLoginDataToSave($clientData);
        
        if(isset($clientData['country_code'])){
            $config->saveConfig(Orba_Allegro_Model_Config::XML_PATH_CONFIG_COUNTRY_ID, $clientData['country_code'], $scope, $scopeId);
        }
        if(isset($clientData['api_key'])){
            $config->saveConfig(Orba_Allegro_Model_Config::XML_PATH_CONFIG_API_KEY, $clientData['api_key'], $scope, $scopeId);
        }
        if(isset($clientData['login'])){
            $config->saveConfig(Orba_Allegro_Model_Config::XML_PATH_CONFIG_USER_LOGIN, $clientData['login'], $scope, $scopeId);
        }
        if(isset($clientData['password'])){
            $config->saveConfig(Orba_Allegro_Model_Config::XML_PATH_CONFIG_USER_PASSWORD, $clientData['password'], $scope, $scopeId);
        }
        if(isset($clientData['is_sandbox'])){
            $config->saveConfig(Orba_Allegro_Model_Config::XML_PATH_CONFIG_SANDBOX_SELECT, $clientData['is_sandbox'], $scope, $scopeId);
        }
        
        // Relaod config
        Mage::getConfig()->reinit();
        Mage::app()->reinitStores();
    }

    /**
     * Save data for Sandbox connection
     */
    public function saveSandboxAction()
    {
        $request = $this->getRequest();
        $store = $request->getParam('store');
        $website = $request->getParam('website');
        $clientData = $request->getParams();

        // Save config data
        $this->_saveConfigData($clientData, $store, $website);
    }
    
    /**
     * @param array $inputData
     * @return array
     */
    protected function _extractLoginDataToSave(array $inputData) {
        $data = array();
        foreach(array("password", "login", "api_key", "country_code", "is_sandbox") as $field){
            if((!isset($inputData[$field."_inherit"]) || empty($inputData[$field."_inherit"]))
                && isset($inputData[$field])){
                $data[$field] = $inputData[$field];
            }
        }
        return $data;
    }
}