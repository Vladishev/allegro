<?php

/**
 * Do not use as singleton!
 */

class Orba_Allegro_Model_Client extends Orba_Allegro_Model_Abstract {
    
    const CACHE_KEY_SESSION = "SESSION";
    const CACHE_LIFETIME_SESSION = 1800;
    
    protected $_fixturesPath;
    
    protected $_resourceName = "orbaallegro/client";
    
    /**
     * @var Orba_Allegro_Model_Config
     */
    protected $_config;
    

    public function _construct() {
        $this->_fixturesPath = Mage::getModuleDir('Model', 'Orba_Allegro'). DS . 'data' . DS . 'fixtures' . DS;
        $this->_config = Mage::getSingleton('orbaallegro/config');
        parent::_construct();
    }
    
    
    public function getBidItem($request) {
        return $this->_execute('doGetBidItem2', array_merge(array(
            'sessionHandle' => $this->getUserSession()
        ), $request));
    }
    
    public function getPostBuyData($request) {
        return $this->_execute('doGetPostBuyData', array_merge(array(
            'sessionHandle' => $this->getUserSession()
        ), $request));
    }
    
    /**
     * Finish auction
     * @param type $request
     * @return array
     */
    public function sellAgain($request) {
        return $this->_execute('doSellSomeAgain', array_merge(array(
            'sessionHandle' => $this->getUserSession()
        ), $request));
    }
    
    /**
     * Finish auction
     * @param type $request
     * @return array
     */
    public function finishItem($request) {
        return $this->_execute('doFinishItem', array_merge(array(
            'sessionHandle' => $this->getUserSession()
        ), $request));
    }
    
    /**
     * Check edited auction
     * @param type $request
     * @return array
     */
    public function changeItemFields($request) {
        return $this->_execute('doChangeItemFields', array_merge(array(
            'sessionId' => $this->getUserSession()
        ), $request));
    }
    
    /**
     * Check edited auction
     * @param type $request
     * @return array
     */
    public function checkChangeItemFields($request) {
        return $this->_execute('doChangeItemFields', array_merge(array(
            'sessionId' => $this->getUserSession(),
            'previewOnly' => 1
        ), $request));
    }
    
    /**
     * Get curretn user data
     * @return array
     */
    public function getUserData($forceNoCache=false) {
        /* @todo add cache */
        $cache = Mage::helper('orbaallegro')->getCache();
        /* @var $cache Varien_Cache_Core */
        
        $cache->setOption("automatic_serialization", true);
                
        $cacheKey = self::CACHE_KEY_SESSION . '_' . md5($this->getLogin());
        $result = false;
        
        if ($forceNoCache || ($result=$cache->load($cacheKey))===false) {
            // Session have to be false
            
            $result = false;
            $result = $this->_execute('doGetMyData', array('sessionHandle' => $this->getUserSession()));
            
            // Use cache if should
            if(!$forceNoCache && $result){
                $cache->save($result, $cacheKey, array(), self::CACHE_LIFETIME_SESSION);
            }
            
        }
        return $result;
    }


    /**
     * Get Transaction IDs By auction
     * @param array $request - required keys: fields, opt keys: localId, itemTemplateCreate, itemTemplateId
     * @return type
     */
    public function getTransactionsIDs($request = array()) {
        return $this->_execute('doGetTransactionsIDs', array_merge(array(
            'sessionHandle' => $this->getUserSession(),
        ), $request));
    }
    
    /**
     * Get post buy form data for sellers
     * @param array $request - required keys: transactionsIdsArray
     * @return type
     */
    public function getPostBuyFormsDataForSellers($request = array()) {
        return $this->_execute('doGetPostBuyFormsDataForSellers', array_merge(array(
            'sessionId' => $this->getUserSession(),
        ), $request));
    }
    
    public function getShipmentData($countryCode=null) {
        if(is_null($countryCode)){
            $countryCode = $this->getCountryCode();
        }
        return $this->_execute('doGetShipmentData', array(
            'countryId' => $countryCode,
            'webapiKey' => $this->getApiKey()
        ));
    }
    

     /**
     * Verify offer
     * @param array $request - required keys: fields, opt keys: localId, itemTemplateCreate, itemTemplateId
     * @return type
     */
    public function verifyItem($request = array()) {
        return $this->_execute('doVerifyItem', array_merge(array(
            'sessionHandle' => $this->getUserSession(),
        ), $request));
    }
    
    
    /**
     * New offer
     * @param array $request - required keys: fields, opt keys: localId, itemTemplateCreate, itemTemplateId
     * @return type
     */
    public function newAuctionExt($request = array()) {
        return $this->_execute('doNewAuctionExt', array_merge(array(
            'sessionHandle' => $this->getUserSession(),
        ), $request));
    }
    
    /**
     * Check the offer
     * @param array $request - required keys: fields, opt keys: localId, itemTemplateCreate, itemTemplateId
     * @return type
     */
    public function checkNewAuctionExt($request = array()) {
        return $this->_execute('doCheckNewAuctionExt', array_merge(array(
            'sessionHandle' => $this->getUserSession(),
        ), $request));
    }
    
    public function getStatesInfo($countryCode=null) {
        if(is_null($countryCode)){
            $countryCode = $this->getCountryCode();
        }
        return $this->_execute('doGetStatesInfo', array(
            'countryCode' => $countryCode,
            'webapiKey' => $this->getApiKey()
        ));
    }
    
    
    /**
     * Metoda pozwala na pobranie pełnego drzewa kategorii dostępnych we wskazanym kraju.
     * 
     * @param array $request Tablica służąca do nadpisania defaultowego requestu
     * @return boolean|stdClass
     */
    public function getCatsData($request = array()) {
        return $this->_execute('doGetCatsData', array_merge(array(
            'countryId' => $this->getCountryCode(),
            'webapiKey' => $this->getApiKey(),
            'localVersion' => 0
        ), $request));
    }
        /**
     * Metoda pozwala na pobranie pełnego drzewa kategorii dostępnych w sklepie użytkownika.
     * 
     * @param array $request Tablica służąca do nadpisania defaultowego requestu
     * @return boolean|stdClass
     */
    public function getShopCatsData($request = array()) {
        return $this->_execute('doGetShopCatsData', array_merge(array(
            'sessionHandle' => $this->getUserSession(),
        ), $request));
    }
    
    /**
     * Metoda pozwala na pobranie listy wszystkich krajów dostępnych w serwisie.
     * 
     * @param array $request Tablica służąca do nadpisania defaultowego requestu
     * @return boolean|stdClass
     */
    public function getCountries($countryCode=null) {
        if(is_null($countryCode)){
            $countryCode = $this->getCountryCode();
        }
        $apiKey = $this->getApiKey();
        
        return $this->_execute('doGetCountries', array(
            'countryCode' => $countryCode,
            'webapiKey' => $apiKey
        ));
    }
    
    /**
     * Metoda pozwala na pobranie listy wszystkich krajów dostępnych w serwisie.
     * 
     * UWAGA!!!!
     * Metoda doGetSitesInfo została usunięta z API Allegro 
     * i dla tego na potrzeby instalacji poszczególnych serwisów zrobiony została dirtyFix
     * 
     * @param array $request Tablica służąca do nadpisania defaultowego requestu
     * @return boolean|stdClass
     */
    public function getSitesInfo($countryCode=null) {
        if(is_null($countryCode)){
            $countryCode = $this->getCountryCode();
        }
        
        $path = $this->_fixturesPath . 'doGetSitesInfo.xml';
        if(file_exists($path)){
            $data = simplexml_load_file($path);
            foreach ($data->node as $service){
                if($service->countryCode == $countryCode){
                    return $service->doGetSitesInfo;
                }
            }
            throw new Exception("Country with id $countryCode not found!");
        }
        
        throw new Exception("File doGetSitesInfo.xml not found!");
    }
    
    
    
    
    /**
     * Metoda pozwala na pobranie listy pól formularza sprzedaży dostępnych we wskazanym kraju.
     * Wybrane pola mogą następnie posłużyć np. do zbudowania i wypełnienia formularza wystawienia nowej oferty z poziomu metody doNewAuctionExt.
     * 
     * @param array $request Tablica służąca do nadpisania defaultowego requestu
     * @return boolean|stdClass
     */
    public function getSellFormFieldsExt($request = array()) {
        return $this->_execute('doGetSellFormFieldsExt', array_merge(array(
            'countryCode' => $this->getCountryCode(),
            'webapiKey' => $this->getApiKey(),
            'localVersion' => $this->getLocalVersion()
        ), $request));
    }
    
    /**
     * Metoda pozwala na pobranie listy pól formularza sprzedaży dostępnych we wskazanym kraju.
     * Wybrane pola mogą następnie posłużyć np. do zbudowania i wypełnienia formularza wystawienia nowej oferty z poziomu metody doNewAuctionExt.
     * 
     * @param array $request Tablica służąca do nadpisania defaultowego requestu
     * @return boolean|stdClass
     */
    public function getSellFormFieldsForCategory($request = array()) {
        return $this->_execute('doGetSellFormFieldsForCategory', array_merge(array(
            'countryId' => $this->getCountryCode(),
            'webapiKey' => $this->getApiKey(),
            'localVersion' => $this->getLocalVersion()
        ), $request));
    }
    
    /**
     * Metoda pozwala na pobranie wartości wszystkich wersjonowanych komponentów oraz umożliwia podgląd kluczy wersji dla wszystkich krajów.
     * 
     * @param array $request Tablica służąca do nadpisania defaultowego requestu
     * @return boolean|stdClass
     */
    public function doQueryAllSysStatus($request = array()) {
        return $this->_execute('doQueryAllSysStatus', array_merge(array(
            'countryId' => $this->getCountryCode(),
            'webapiKey' => $this->getApiKey()
        ), $request));
    }
    
     /**
     * Pobranie informacji o ofercie
     * @param array $request - required keys: itemId, opt keys: getDesc, getImageUrl, getAttribs, getPostageOptions, getCompanyInfo, getProductInfo
     * @return type
     */
    public function getItemInfo($request = array()) {
        return $this->_execute('doShowItemInfoExt', array_merge(array(
            'sessionHandle' => $this->getUserSession(),
        ), $request));
    }
    
    /**
     * Gets Allegro local version for current country id.
     * 
     * @return string
     */
    protected function getVerKey($forceNew = false) {
        // $verKey = false;
        // if (!$forceNew) {
        //    $cache = Mage::helper('orbaallegro')->getCache();
        //    $verKey = $cache->load(Orba_Allegro_Model_Config::CACHE_KEY_VER_KEY . '_' . $countryId);
        // }
        // if (!$verKey) {
        
        $result = $this->_execute(
            'doQuerySysStatus', 
             array(
                'sysvar' => 3,
                'countryId' => $this->getCountryCode(),
                'webapiKey' => $this->getApiKey()
            )
        );
        $verKey = false;
        if($result){
            $verKey = (string)$result->verKey;
        }
            
        // if (!isset($cache)) {
        //        $cache = Mage::helper('orbaallegro')->getCache();   
        // }
        //    $cache->save($verKey, Orba_Allegro_Model_Config::CACHE_KEY_VER_KEY . '_' . $countryId, array(Orba_Allegro_Model_Config::CACHE_KEY));
        // }
        return $verKey;
    }
    
    /**
     * Gets Allegro user session.
     * 
     * At first the value is loaded from cache (ie. the user is logged in).
     * If it's empty, we log the user.
     * If needed cryptographic functions exist on the server, doLoginEnc method is used.
     * Else, doLogin method is used.
     * 
     * Has internal cache
     * 
     * @return string
     */
    public function getUserSession($forceNoCache=false) {
        $cache = Mage::helper('orbaallegro')->getCache();
        $cacheKey = self::CACHE_KEY_SESSION . '_' . md5(
            $this->getLogin().
            $this->getPassword().
            $this->getCountryCode().
            $this->getApiKey()
        );
        
        if ($forceNoCache || ($session=$cache->load($cacheKey))===false) {
            // Session have to be false
            $session = false;
            
            // Make request
            $request = array(
                'userLogin'     => $this->getLogin(),
                'userPassword'  => $this->getPassword(),
                'countryCode'   => $this->getCountryCode(),
                'webapiKey'     => $this->getApiKey(),
                'localVersion'  => $this->getVerKey()
            );
            
            $isCrypo = false;
            // do crypto functions exists?
            if (function_exists('hash') && in_array('sha256', hash_algos())) {
                $isCrypo = true;
                $password = hash('sha256', $this->getPassword(), true);
            } // older mhash
            else if (function_exists('mhash') && is_int(MHASH_SHA256)) {
                $isCrypo = true;
                $password = mhash(MHASH_SHA256, $this->getPassword());
            }
            if ($isCrypo) {
                $password = base64_encode($password);
                $request['userHashPassword'] = $password;
                unset($request['userPassword']);
                $result = $this->_execute('doLoginEnc', $request);
            } else {
                $result = $this->_execute('doLogin', $request);
            }
            
            if($result){
                $session = (string)$result->sessionHandlePart;
            }else{
                $cache->remove($cacheKey);
            }
            
            // Use cache if should
            if(!$forceNoCache && $session){
                $cache->save($session, $cacheKey, array(), self::CACHE_LIFETIME_SESSION);
            }
        }
        return $session;
    }
    
    
    /**
     *  Utils ....
     */
    
    public function setStoreId($id) {
        if(!is_numeric($id))
        {
            $store = Mage::app()->getStore($id);
            if($store->getId()){
                $id = $store->getId();
            }
        }
        return parent::setStoreId($id);
    }
    
    // Store Id is mandatory to use config - if not defined - use default values
    public function getStoreId() {
        if(is_null($this->getData("store_id"))){
            $this->setStoreId(Mage_Core_Model_App::ADMIN_STORE_ID);
        }
        return $this->getData("store_id");
    }
    
    // Other thinks
    public function getCountryCode() {
        if(is_null($this->getData("country_code"))){
            $this->setData("country_code", $this->_config->getCountryCode(
                    $this->getStoreId(), $this->getWebsiteId()));
        }
        return $this->getData("country_code");
    }
    
    public function getLogin() {
        if(is_null($this->getData("login"))){
            $this->setData("login", $this->_config->getLogin(
                    $this->getStoreId(), $this->getWebsiteId()));
        }
        return $this->getData("login");
    }
    
    public function getPassword() {
        if(is_null($this->getData("password"))){
            $this->setData("password", $this->_config->getPassword(
                    $this->getStoreId(), $this->getWebsiteId()));
        }
        return $this->getData("password");
    }
    
    public function getApiKey() {
        if(is_null($this->getData("api_key"))){
            $this->setData("api_key", $this->_config->getApiKey(
                    $this->getStoreId(), $this->getWebsiteId()));
        }
        return $this->getData("api_key");
    }
    
    /**
     * Execute wrapper
     */
    
    protected function _execute($method, array $request) {
        
        /** @todo implement cache if needed **/
        return $this->getResource()->execute($method, $request, $this);
    }
   
    
}