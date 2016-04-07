<?php
class Orba_Allegro_Model_Misc_Yesno{
    
    const STATUS_YES = 1;
    const STATUS_NO = 0;
    
    public function toOptionHash() {
        return array(
            self::STATUS_YES  => Mage::helper("orbaallegro")->__("Yes"),
            self::STATUS_NO => Mage::helper("orbaallegro")->__("No")
        );
    }
    
}