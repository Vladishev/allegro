<?php
class Orba_Allegro_Model_System_Config_Source_Shippingoptions
    extends Orba_Allegro_Model_System_Config_Source_Abstract {
   
    public function toOptionArray() {
        $helper = Mage::helper('orbaallegro');
        return array(
            array(
                'value' => 0,
                'label' => ''
            ),
            array(
                'value' => 16,
                'label' => $helper->__('Details in description')
            ),
            array(
                'value' => 32,
                'label' => $helper->__('Agree to send out of country')
            )
        );
    }
    
}