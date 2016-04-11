<?php
class Orba_Allegro_Model_System_Config_Source_Shippingtime 
    extends Orba_Allegro_Model_System_Config_Source_Abstract {
   
    public function toOptionArray() {
        $helper = Mage::helper('orbaallegro');
        return array(
            array(
                'value' => 0,
                'label' => ''
            ),
            array(
                'value' => 1,
                'label' => 0
            ),
            array(
                'value' => 24,
                'label' => 24
            ),
            array(
                'value' => 48,
                'label' => 48
            ),
            array(
                'value' => 72,
                'label' => 72
            ),
            array(
                'value' => 96,
                'label' => 96
            ),
            array(
                'value' => 120,
                'label' => 120
            ),
            array(
                'value' => 168,
                'label' => 168
            ),
            array(
                'value' => 240,
                'label' => 240
            ),
            array(
                'value' => 336,
                'label' => 336
            ),
            array(
                'value' => 504,
                'label' => 504
            )
        );
    }
    
}
