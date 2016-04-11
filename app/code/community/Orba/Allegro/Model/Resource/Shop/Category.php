<?php
class Orba_Allegro_Model_Resource_Shop_Category extends 
    Mage_Core_Model_Resource_Db_Abstract {
    
    protected function _construct() {
        $this->_init('orbaallegro/shop_category', 'category_id');
    } 
    
}