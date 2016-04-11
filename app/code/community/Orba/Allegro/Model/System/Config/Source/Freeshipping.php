<?php
class Orba_Allegro_Model_System_Config_Source_Freeshipping
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
                'label' => $helper->__('Pickup by the customer')
            ),
            array(
                'value' => 2,
                'label' => $helper->__('Electronic shipment (e-mail)')
            ),
            array(
                'value' => 4,
                'label' => $helper->__('Pickup by the customer after prepaid')
            )
        );
    }
    
}