<?php

class Orba_Allegro_Block_Adminhtml_Auction_Grid_Column_Renderer_Category 
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
    
    public function render(Varien_Object $row) {
        return Mage::getSingleton('orbaallegro/category')->
            getNamePath($row->getData('category_id'));
    }
    
}