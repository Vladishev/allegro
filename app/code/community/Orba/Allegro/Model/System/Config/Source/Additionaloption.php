<?php
class Orba_Allegro_Model_System_Config_Source_Additionaloption 
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
                'label' => $helper->__('Bold')
            ),
            array(
                'value' => 2,
                'label' => $helper->__('Thumbnail')
            ),
            array(
                'value' => 4,
                'label' => $helper->__('Backlight')
            ),
            array(
                'value' => 8,
                'label' => $helper->__('Distinction')
            ),
            array(
                'value' => 16,
                'label' => $helper->__('Category page')
            ),
            array(
                'value' => 32,
                'label' => $helper->__('Main page')
            ),
            array(
                'value' => 64,
                'label' => $helper->__('Watermark')
            )
        );
    }
    
}