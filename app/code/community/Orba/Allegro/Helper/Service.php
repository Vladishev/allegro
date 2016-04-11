<?php
class Orba_Allegro_Helper_Service {
    public function checkCountryCode($countryCode=null){
        if(!is_int($countryCode)){
            return false;
        }
        return in_array(
            $countryCode, 
            Mage::getSingleton('orbaallegro/service')->getAllCountryCodes()
        );
    }
}
