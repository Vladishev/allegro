<?php
class Orba_Allegro_Model_System_Config_Source_Payment extends
    Orba_Allegro_Model_System_Config_Source_Abstract {
    
    public function toOptionArray() {
        $helper = Mage::helper('orbaallegro');
        return array(
            array(
                'value' => 0,
                'label' => ''
            ),
            array(
                'value' => 1,
                'label' => $helper->__('Prepaid')
            ),
            array(
                'value' => 16,
                'label' => $helper->__('Another payment types')
            ),
            array(
                'value' => 32,
                'label' => $helper->__('Provide VAT Invoices')
            )
        );
    }
    
}