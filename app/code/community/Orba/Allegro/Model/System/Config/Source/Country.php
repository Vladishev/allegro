<?php
class Orba_Allegro_Model_System_Config_Source_Country extends Orba_Allegro_Model_System_Config_Source_Abstract{
    
    const CACHE_KEY = 'allegro_countries';
    const CACHE_LIFETIME = 43200; // 60*60*12
    
    public function toOptionArray() {
        if(is_int($this->getCountryCode())){
            $countryCode = $this->getCountryCode();
        }else{
            $countryCode = $this->_getAdminhtmlValue("getCountryCode");
        }
        
        
        if($result = $this->_getExternalCountries($countryCode)){
            $countries_flat = array();
            foreach ($result->countryArray->item as $item) {
                $countries_flat[$item->countryId] = $item->countryName;
            }
            asort($countries_flat);
            foreach ($countries_flat as $id => $name) {
                //  Remove Neverland
                // if($countryCode!=Orba_Allegro_Model_Service::ID_WEBAPI && 
                //   $id==Orba_Allegro_Model_Service::ID_WEBAPI){
                //    continue;
                // }
                $countries[] = array(
                    'value' => $id,
                    'label' => $name
                );
            }
            return $countries;
        }
        return $this->_noItems;
    }
    
    public function toOptionHash() {;
        $out = array();
        foreach ($this->toOptionArray() as $opt){
            $out[$opt['value']]=$opt['label'];
        }
        return $out;
    }
    
    protected function _getExternalCountries($countryCode){
        

        $cacheOpts = Mage::getModel("orbaallegro/client_cache_config");
        $client = Mage::getModel("orbaallegro/client");
        /* @var $client Orba_Allegro_Model_Client */
        
        $client->setStoreId($this->getStore());
        $client->setWebsiteId($this->getWebsite());
        
        $cacheOpts->setCacheKey(self::CACHE_KEY . "_" . $countryCode);
        $cacheOpts->setLifetime(self::CACHE_LIFETIME);
        
        $client->setCacheOpts($cacheOpts);
        
        $result = false;
        try{
           $result = $client->getCountries($countryCode) ;
        }
        catch(Orba_Allegro_Model_Client_Exception $e){
            Mage::helper('orbaallegro/log')->log($e->getMessage());
        }catch(Exception $e){
            Mage::logException($e);
        }
        
        return $result;
    }
}