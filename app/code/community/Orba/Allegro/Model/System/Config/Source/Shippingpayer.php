<?php
class Orba_Allegro_Model_System_Config_Source_Shippingpayer 
    extends Orba_Allegro_Model_System_Config_Source_Abstract {
   
    const VALUE_SELLER = 0;
    const VALUE_BUEYR = 1;
    
    
    public function toOptionArray() {
        $helper = Mage::helper('orbaallegro');
        return array(
            array(
                'value' => self::VALUE_SELLER,
                'label' => $helper->__('Seller')
            ),
            array(
                'value' => self::VALUE_BUEYR,
                'label' => $helper->__('Buyer')
            )
        );
    }
    
}