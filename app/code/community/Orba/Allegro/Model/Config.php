<?php
class Orba_Allegro_Model_Config extends Varien_Object{
    
    const XML_PATH_CONFIG_API_KEY               = 'orbaallegro/config/api_key';
    const XML_PATH_CONFIG_USER_LOGIN            = 'orbaallegro/config/user_login';
    const XML_PATH_CONFIG_USER_PASSWORD         = 'orbaallegro/config/user_password';
    const XML_PATH_CONFIG_IS_DEBUG_MODE         = 'orbaallegro/config/is_debug_mode';
    const XML_PATH_CONFIG_API_URL               = 'orbaallegro/config/api_url';
    const XML_PATH_CONFIG_SANDBOX_API_URL       = 'orbaallegro/config_sandbox/api_url';
    const XML_PATH_CONFIG_SANDBOX_SELECT        = 'orbaallegro/config/use_sandbox';
    const XML_PATH_CONFIG_COUNTRY_ID            = 'orbaallegro/config/country_id';
    const XML_PATH_CONFIG_IS_IMPORT_COMPLETE    = 'orbaallegro/config/is_import_complete';
	const XML_PATH_ORDER_OVERRIDE_SHIPPING_RATE = 'orbaallegro/orders/override_shipping_rate';
    const XML_PATH_ORDER_CREATE_CUSTOMER		= 'orbaallegro/orders/create_customer';
    
    const XML_PATH_CATEGORIES_ACTIVE            = 'orbaallegro/categories/active';
    
    const XML_PATH_TEMPLATE_LOCALIZTION_COUNTRY = 'orbaallegro/template_localization/country';
    
    const XML_PATH_TEMPLATE_IMAGE_KEEP_FRAME    = 'orbaallegro/template_image/keep_frame';
    const XML_PATH_TEMPLATE_IMAGE_WIDTH         = 'orbaallegro/template_image/width';
    const XML_PATH_TEMPLATE_IMAGE_HEIGHT        = 'orbaallegro/template_image/height';
    
    const XML_PATH_FLAT_CATALOG_PRODUCT         = 'catalog/frontend/flat_catalog_product';
    
    const CACHE_KEY                             = 'orba_allegro';
    const CACHE_KEY_VER_KEY                     = 'orba_allegro_ver_key';
    const CACHE_KEY_SESSION                     = 'orba_allegro_session';
    
    const ATTR_YOUTUBE_CODE                     = "orbaallegro_youtube_code";
	
	

    /**
     * Gets Allegro API URL.
     * @return string
     */
    public function getApiUrl() {
        return Mage::getStoreConfig(self::XML_PATH_CONFIG_API_URL);
    }
    
    
    public function getFullConfig() {
        $data = array();
        foreach(Mage::getModel("core/store")->getCollection() as $store){
            $data[$store->getId()] = $this->_fetchFullConfig($store->getId());
        }
        return $data;
        
    }
    
    /**
     * Gets array of country ids for which categories synchronization should be done.
     * @return array
     */
    public function getCategoriesSyncCountryIds() {
        throw new Orba_Allegro_Exception("Orba_Allegro_Model_Config::getCategoriesSyncCountryIds() - Deprecated");
    }
    

    
    
    /**
     * Gets Allegro API key for the specified store.
     * 
     * @param Mage_Core_Model_Store|string $store
     * @return string
     */
    public function getApiKey($store=null, $website=null) {
        return $this->getConfig(self::XML_PATH_CONFIG_API_KEY, $store, $website);
    }
    
    /**
     * Gets Allegro country id for the specified store or website.
     * 
     * @param Mage_Core_Model_Store|string $store
     * @param string $website
     * @return int
     */
    public function getCountryId($store=null, $website=null) {
        return $this->getCountryCode($store, $website);
    }
    
    public function getCountryCode($store=null, $website=null) {  
        return $this->getConfig(self::XML_PATH_CONFIG_COUNTRY_ID, $store, $website);
    }
    
    /**
     * Gets Allegro user name for the specified store.
     * 
     * @param Mage_Core_Model_Store|string $store
     * @return string
     */
    public function getLogin($store=null, $website=null) {
        return $this->getConfig(self::XML_PATH_CONFIG_USER_LOGIN, $store, $website);
    }
    
    /**
     * Gets Allegro user password for the specified store.
     * 
     * @param Mage_Core_Model_Store|string $store
     * @return string
     */
    public function getPassword($store=null, $website=null) {
        return $this->getConfig(self::XML_PATH_CONFIG_USER_PASSWORD, $store, $website);
    }
    

    public function getLoginData($store=null, $website=null) {
        return array(
            'login'         => $this->getConfig(self::XML_PATH_CONFIG_USER_LOGIN, $store, $website),
            'password'      => $this->getConfig(self::XML_PATH_CONFIG_USER_PASSWORD, $store, $website),
            'api_key'       => $this->getConfig(self::XML_PATH_CONFIG_API_KEY, $store, $website),
            'country_code'  => $this->getConfig(self::XML_PATH_CONFIG_COUNTRY_ID, $store, $website),
        );
    }
    
    public function getStoresByCountryCode($countryCode, $withAdmin=false) {
        $data = array();
        $collection=Mage::getModel("core/store")->
                getCollection()->
                setFlag('load_default_store', $withAdmin);
        foreach($collection as $store){
            $storeCountryCode = $this->getCountryCode($store->getId());
            if($storeCountryCode==$countryCode){
                $data[] = $store->getId();
            }
        }
        return $data;
    }
    
    /**
     * @param type $login
     * @param type $withAdmin
     * @return Mage_Core_Model_Store
     */
    public function getStoreByLogin($login, $withAdmin=false) {
        $collection=Mage::getModel("core/store")->
                getCollection()->
                setFlag('load_default_store', $withAdmin);
        foreach($collection as $store){
            $_login = $this->getLogin($store->getId());
            if($login == $_login){
                return $store;
            }
        }
        return null;
    }
    


    
    /**
     * Gets store object matching the specified store code.
     * 
     * @param string $storeCode
     * @return Mage_Core_Model_Store|boolean
     */
    public function getStoreByCode($storeCode) {
        $stores = array_keys(Mage::app()->getStores());
        foreach ($stores as $id) {
            $store = Mage::app()->getStore($id);
            if ($store->getCode() == $storeCode) {
                return $store;
            }
        }
        return false;
    }
    
    /**
     * Is debug mode (module internal)
     * @return bool
     */
    public function getIsDebugMode() {
        return Mage::getStoreConfig(self::XML_PATH_CONFIG_IS_DEBUG_MODE);
    }
    
    
    /**
     * Checks if flat catalog is enabled.
     * 
     * @return bool
     */
    public function isFlatCatalogEnabled($store=null, $website=null) {
        return $this->getConfig(self::XML_PATH_FLAT_CATALOG_PRODUCT, $store, $website);
    }
    
        
    public function getConfigByStoreAndWebsite($path, $store=null, $website=null) {
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
        return Mage::getConfig()->getNode($path, $scope, $scopeId);
    }
        
    public function isFirstImportComplete() {
        return Mage::getStoreConfig(
            self::XML_PATH_CONFIG_IS_IMPORT_COMPLETE, 
            Mage_Core_Model_Store::ADMIN_CODE
        );
    }
    
    public function setFirstImportComplete() {
        Mage::getModel("core/config")->saveConfig(self::XML_PATH_CONFIG_IS_IMPORT_COMPLETE, 1);
        Mage::getConfig()->reinit();
        Mage::app()->reinitStores();
    }

    public function getConfig($path, $store=null, $website=null) {
        if ($store || !$website) {
            return Mage::getStoreConfig($path, $store);
        } else {
            return Mage::app()->getWebsite($website)->getConfig($path);
        }
    }
    
    public function getImagesWidth($store=null, $website=null) {
        return (int)$this->getConfig(self::XML_PATH_TEMPLATE_IMAGE_WIDTH,$store, $website);
    }
    
    public function getImagesHeight($store=null, $website=null) {
        return (int)$this->getConfig(self::XML_PATH_TEMPLATE_IMAGE_HEIGHT,$store, $website);
    }
    
    public function getImagesKeepFrame($store=null, $website=null) {
        return (bool)(int)$this->getConfig(self::XML_PATH_TEMPLATE_IMAGE_KEEP_FRAME,$store, $website);
    }
    
    
    protected function _fetchFullConfig($store=null, $website=null) {
        $ret = array();
        $ret['config']['api_key']       = $this->getConfig(self::XML_PATH_CONFIG_API_KEY, $store, $website);
        $ret['config']['country_code']  = $this->getConfig(self::XML_PATH_CONFIG_COUNTRY_ID, $store, $website);
        $ret['config']['login']         = $this->getConfig(self::XML_PATH_CONFIG_USER_LOGIN, $store, $website);
        $ret['config']['password']      = $this->getConfig(self::XML_PATH_CONFIG_USER_PASSWORD, $store, $website);
        $ret['categories']['active']    = $this->getConfig(self::XML_PATH_CATEGORIES_ACTIVE, $store, $website);
        /**
         * @todo add another pathes
         */
        return $ret;
    }
    
    
    protected function _toArray($opts) {
        if(is_string($opts)){
            return explode(",", $opts);
        }
        return $opts;
    }
	
	public function getOverrideRate($storeId=null) {
        return Mage::getStoreConfig(self::XML_PATH_ORDER_OVERRIDE_SHIPPING_RATE, $storeId);
    }
	public function getCreateCustomer($storeId=null) {
        return Mage::getStoreConfig(self::XML_PATH_ORDER_CREATE_CUSTOMER, $storeId);
    }
}