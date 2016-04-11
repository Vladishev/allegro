<?php
class Orba_Allegro_Model_System_Config_Source_Autorenew extends
    Orba_Allegro_Model_System_Config_Source_Abstract {
    
	const DO_NOT_RENEW = 0;
	const RENEW_COMPLETE = 1;
	const RENEW_NOT_SOLD = 2;
	
    public function toOptionArray() {
        $helper = Mage::helper('orbaallegro');
        return array(
            array(
                'value' => self::DO_NOT_RENEW,
                'label' => $helper->__('Do not renew')
            ),
            array(
                'value' => self::RENEW_COMPLETE,
                'label' => $helper->__('Renew with complete items set')
            ),
            array(
                'value' => self::RENEW_NOT_SOLD,
                'label' => $helper->__('Renew with not-sold items')
            )
        );
    }
    
}