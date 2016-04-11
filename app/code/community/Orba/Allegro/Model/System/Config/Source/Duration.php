<?php
/**
 *  @todo implement using EAV structure (options/store labels)
 */
class Orba_Allegro_Model_System_Config_Source_Duration 
    extends Orba_Allegro_Model_System_Config_Source_Abstract {
    
    const DAYS_3 = 3;
    const DAYS_5 = 5;
    const DAYS_7 = 7;
    const DAYS_10 = 10;
    const DAYS_14 = 14;
    const DAYS_30 = 30;
    
    public function toOptionArray() {
        $helper = Mage::helper('orbaallegro');
        return array(
            array(
                'value' => 0,
                'label' => $helper->__(self::DAYS_3 . ' days')
            ),
            array(
                'value' => 1,
                'label' => $helper->__(self::DAYS_5 . ' days')
            ),
            array(
                'value' => 2,
                'label' => $helper->__(self::DAYS_7 . ' days')
            ),
            array(
                'value' => 3,
                'label' => $helper->__(self::DAYS_10. ' days')
            ),
            array(
                'value' => 4,
                'label' => $helper->__(self::DAYS_14 . ' days')
            ),
            array(
                'value' => 5,
                'label' => $helper->__(self::DAYS_30 . ' days')
            )
        );
    }
    
    public function toOptionHash() {
        return array(
            0 => self::DAYS_3,
            1 => self::DAYS_5,
            2 => self::DAYS_7,
            3 => self::DAYS_10,
            4 => self::DAYS_14,
            5 => self::DAYS_30,
        );
    }
    
    public function toOptionHashForModify() {
        return array(
            0 => self::DAYS_3,
            1 => self::DAYS_5,
            2 => self::DAYS_7,
            3 => self::DAYS_10,
            4 => self::DAYS_14
        );
    }
    
}