<?php
class Orba_Allegro_Model_System_Config_Source_Salesformat extends
    Orba_Allegro_Model_System_Config_Source_Abstract {
    
	const FORMAT_AUCTION = 0;
	const FORMAT_SHOP = 1;
	
    public function toOptionArray() {
        $helper = Mage::helper('orbaallegro');
        return array(
            array(
                'value' => self::FORMAT_AUCTION,
                'label' => $helper->__('Only Buy Now! (without bidding) or Bidding')
            ),
            array(
                'value' => self::FORMAT_SHOP,
                'label' => $helper->__('Shop (without bidding)')
            )
        );
    }
    
}