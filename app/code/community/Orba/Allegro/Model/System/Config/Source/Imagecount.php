<?php
class Orba_Allegro_Model_System_Config_Source_Imagecount 
    extends Orba_Allegro_Model_System_Config_Source_Abstract {
    
    public function toOptionArray() {
        $array = array(0=>Mage::helper("orbaallegro")->__("Don't use"));
        for($i=1;$i<=8;$i++){
            $array[$i] = $i;
        }
        return $array;
    }
    
}