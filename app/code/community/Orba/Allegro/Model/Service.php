<?php
class Orba_Allegro_Model_Service extends Orba_Allegro_Model_Abstract {

    /**
     * Country Codes
     */
    const CODE_ALLEGROPL        = "allegropl";
    const CODE_ALLEGROBY        = "allegroby";
    const CODE_ALLEGROBG        = "allegrobg";
    const CODE_ALLEGROKZ        = "allegrokz";
    const CODE_AUKROUA          = "aukroua";
    const CODE_AUKROSK          = "aukrosk";
    const CODE_UAAUKROUA        = "uaaukroua";
    const CODE_AUKROCZ          = "aukrocz";
    const CODE_MOLOTOKRU        = "molotokru";
    const CODE_VATERAHU         = "vaterahu";
    const CODE_WEBAPI           = "webapi";
    
    /**
     * Country Ids
     */
    const ID_ALLEGROPL          = 1;
    const ID_ALLEGROBY          = 22;
    const ID_ALLEGROBG          = 34;
    const ID_ALLEGROKZ          = 107;
    const ID_AUKROUA            = 209;
    const ID_AUKROSK            = 181;
    const ID_UAAUKROUA          = 232;
    const ID_AUKROCZ            = 56;
    const ID_MOLOTOKRU          = 168;
    const ID_VATERAHU           = 93;
    const ID_WEBAPI             = 228;
    
    /**
     * Country labels (static install)
     */
    const LABEL_ALLEGROPL       = "Allegro.pl";
    const LABEL_ALLEGROBY       = "Allegro.by";
    const LABEL_ALLEGROBG       = "Allegro.bg";
    const LABEL_ALLEGROKZ       = "Allegro.kz";
    const LABEL_AUKROUA         = "Aukro.ua";
    const LABEL_AUKROSK         = "Aukro.sk";
    const LABEL_UAAUKROUA       = "Ua.aukro.ua";
    const LABEL_AUKROCZ         = "Aukro.cz";
    const LABEL_MOLOTOKRU       = "Molotok.ru";
    const LABEL_VATERAHU        = "Vatera.hu";
    const LABEL_WEBAPI          = "Testwebapi.pl";
    
    /**
     * Currency symbols (static install)
     */
    const CURRENCY_ALLEGROPL    = "PLN";
    const CURRENCY_ALLEGROBY    = "BYR";
    const CURRENCY_ALLEGROBG    = "BGN";
    const CURRENCY_ALLEGROKZ    = "KZT";
    const CURRENCY_AUKROUA      = "UAH";
    const CURRENCY_AUKROSK      = "EUR";
    const CURRENCY_UAAUKROUA    = "UAH";
    const CURRENCY_AUKROCZ      = "EUR";
    const CURRENCY_MOLOTOKRU    = "RUB";
    const CURRENCY_VATERAHU     = "HUF";
    const CURRENCY_WEBAPI       = "PLN";
    
    
    /**
     * Functional meaning
     */
    const CODE_DEFAULT          = self::CODE_ALLEGROPL;
    const CODE_TEST             = self::CODE_WEBAPI;
    
    /**
     * Full mapping
     */
    protected static $_codeToId = array(
        self::CODE_ALLEGROPL    => self::ID_ALLEGROPL,
        self::CODE_ALLEGROBY    => self::ID_ALLEGROBY,
        self::CODE_ALLEGROBG    => self::ID_ALLEGROBG,
        self::CODE_ALLEGROKZ    => self::ID_ALLEGROKZ,
        self::CODE_AUKROUA      => self::ID_AUKROUA,
        self::CODE_AUKROSK      => self::ID_AUKROSK,
        self::CODE_UAAUKROUA    => self::ID_UAAUKROUA,
        self::CODE_AUKROCZ      => self::ID_AUKROCZ,  
        self::CODE_MOLOTOKRU    => self::ID_MOLOTOKRU,
        self::CODE_VATERAHU     => self::ID_VATERAHU, 
        self::CODE_WEBAPI       => self::ID_WEBAPI
    );
    
    /**
     * Full map labels
     */
    protected  $_idToLabel = array(
        self::ID_ALLEGROPL    => self::LABEL_ALLEGROPL,
        self::ID_ALLEGROBY    => self::LABEL_ALLEGROBY,
        self::ID_ALLEGROBG    => self::LABEL_ALLEGROBG,
        self::ID_ALLEGROKZ    => self::LABEL_ALLEGROKZ,
        self::ID_AUKROUA      => self::LABEL_AUKROUA,
        self::ID_AUKROSK      => self::LABEL_AUKROSK,
        self::ID_UAAUKROUA    => self::LABEL_UAAUKROUA,
        self::ID_AUKROCZ      => self::LABEL_AUKROCZ,  
        self::ID_MOLOTOKRU    => self::LABEL_MOLOTOKRU,
        self::ID_VATERAHU     => self::LABEL_VATERAHU, 
        self::ID_WEBAPI       => self::LABEL_WEBAPI
    );
    
    /**
     * Full map currency
     */
    protected  $_idToCurrency = array(
        self::ID_ALLEGROPL    => self::CURRENCY_ALLEGROPL,
        self::ID_ALLEGROBY    => self::CURRENCY_ALLEGROBY,
        self::ID_ALLEGROBG    => self::CURRENCY_ALLEGROBG,
        self::ID_ALLEGROKZ    => self::CURRENCY_ALLEGROKZ,
        self::ID_AUKROUA      => self::CURRENCY_AUKROUA,
        self::ID_AUKROSK      => self::CURRENCY_AUKROSK,
        self::ID_UAAUKROUA    => self::CURRENCY_UAAUKROUA,
        self::ID_AUKROCZ      => self::CURRENCY_AUKROCZ,  
        self::ID_MOLOTOKRU    => self::CURRENCY_MOLOTOKRU,
        self::ID_VATERAHU     => self::CURRENCY_VATERAHU, 
        self::ID_WEBAPI       => self::CURRENCY_WEBAPI
    );
    
    /**
     * Resource & collection
     */
    protected $_resourceName = "orbaallegro/service";
    protected $_resourceCollectionName = "orbaallegro/service_collection";
    
    /**
     * Factory of service interface
     * @restun Orba_Allegro_Model_Service_Abstract
     */
    public static function factory($service) {
        if(is_numeric($service)){
            $map = array_flip(self::$_codeToId);
            if(isset($map[$service])){
                $service = $map[$service];
            }
        }
        try{
            $model = Mage::getModel("orbaallegro/service_".strtolower($service));
            if(is_object($model)){
                return $model->load($service);
            }
            return false;
        }
        catch(Exception $e){
                Mage::logException($e);
                return false;
       }
    }
    
    /**
     * Only for installer
     * @param type $param
     * @return type
     */
    public function getAllConstCountryCodes() {
        return array_values(self::$_codeToId);
    }
    
    /**
     * Only for installer
     * @return array
     */
    public function getConstCodesToIds() {
        return self::$_codeToId;
    }
    
    /**
     * Returns all posible country codes from DB
     * @return array
     */
    public function getAllCountryCodes() {
        if(!$this->getData("all_country_codes")){
            $array = array();
            foreach($this->getCollection() as $service){
                $array[] = $service->getCountryCode();
            }
            $this->setData("all_country_codes", $array);
        }
        return $this->getData("all_country_codes");
    }
    
    
    public function mapCountryCodeToCode($countryCode) {
        $countryCodes = array_flip(self::$_codeToId);
        return isset($countryCodes[$countryCode]) ? $countryCodes[$countryCode] : null;
    }

    
    /**
     * Only for static install
     * @return array
     */
    public function getConstCountryCodesToLabel() {
        return $this->_idToLabel;
    }
    
    /**
     * Only for static install
     * @return array
     */
    public function getConstCountryCodesToCurrency() {
        return $this->_idToCurrency;
    }
    
    /**
     * Select widget content only services avaiable
     * @return array
     */
    public function getCountryCodesToLabel() {
        if(!$this->getData('country_codes_to_label')){
            $array = array();
            $collection = $this->getCollection()->addAvailableFilter();
            foreach($collection as $service){
                $array[$service->getServiceCountryCode()] = $service->getServiceName();
            }
            $this->setData('country_codes_to_label', $array);
        }
        return $this->getData('country_codes_to_label');
    }
    
    
}