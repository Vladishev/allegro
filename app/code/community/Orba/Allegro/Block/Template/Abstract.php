<?php
class Orba_Allegro_Block_Template_Abstract extends Mage_Core_Block_Template{
    
    
    const GETTER_PATH = "orbaallegro/auction/getImage";

    public function getBlockWidth(){
        return Mage::helper('orbaallegro')->getBlockWidth();
    }
    
    public function getBlockHeight(){
        return Mage::helper('orbaallegro')->getBlockHeight();
    }
    
    public function getKeepFrame(){
        return Mage::helper('orbaallegro')->getKeepFrame();
    }
    
    protected function _resize(
        Mage_Catalog_Model_Product $product,
        $type,
        $width,
        $height,
        $image = null,
        $constrain_only_flag = true,
        $keep_frame_flag = false){
        
        return Mage::helper('catalog/image')
            ->init($product, $type, ($image ? $image->getFile() : null))
            ->constrainOnly($constrain_only_flag)
            ->keepFrame($keep_frame_flag)
            ->resize($width, $height);
        
    }
}