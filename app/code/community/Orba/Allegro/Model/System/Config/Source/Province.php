<?php
class Orba_Allegro_Model_System_Config_Source_Province extends Orba_Allegro_Model_System_Config_Source_Abstract{
    
   const CACHE_KEY = 'ORBAALLEGRO_PROVINCE';
   const CACHE_LIFETIME = 43200; // 60*60*12
    
   public function toOptionArray() {// Per sotre
        
        if(is_int($this->getCountryCode())){
            $countryCode = $this->getCountryCode();
        }else{
            $countryCode = $this->_getAdminhtmlConfigValue(
                Orba_Allegro_Model_Config::XML_PATH_TEMPLATE_LOCALIZTION_COUNTRY
             );
        }
        $result = $this->_getExternalsProvinces($countryCode);
        $provinces = array();
        
        if ($result) {
            foreach ($result->statesInfoArray->item as $item) {
                $provinces[] = array(
                    'label' => $item->stateName,
                    'value' => $item->stateId
                );
            }
            return $provinces;
        }
        
        return $this->_noItems;
   }
   
   protected function _getExternalsProvinces($countryCode) {
       
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
           $result = $client->getStatesInfo($countryCode) ;
        }
        catch(Orba_Allegro_Model_Client_Exception $e){
            Mage::helper('orbaallegro/log')->log($e->getMessage());
        }catch(Exception $e){
            Mage::logException($e);
        }
        return $result;
   }
}