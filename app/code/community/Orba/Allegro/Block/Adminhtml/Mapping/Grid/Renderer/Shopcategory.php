<?php

class Orba_Allegro_Block_Adminhtml_Mapping_Grid_Renderer_Shopcategory 
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
    
    public function render(Varien_Object $row) {
        if(!$row->getData('entity_id_2')){
            return '-';
        }
        return Mage::getSingleton('orbaallegro/shop_category')->
            getNamePath($row->getData('entity_id_2'));
    }
    
}