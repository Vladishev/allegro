<?php
class Orba_Allegro_Model_System_Config_Source_Quantitytype 
    extends Orba_Allegro_Model_System_Config_Source_Abstract {
    
    public function toOptionArray() {
        $helper = Mage::helper('orbaallegro');
        return array(
            array(
                'value' => 0,
                'label' => $helper->__('Pieces')
            ),
            array(
                'value' => 1,
                'label' => $helper->__('Sets')
            ),
            array(
                'value' => 2,
                'label' => $helper->__('Pairs')
            )
        );
    }
    
}