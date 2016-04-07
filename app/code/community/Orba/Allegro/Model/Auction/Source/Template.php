<?php
class Orba_Allegro_Model_Auction_Source_Template {
    public function toOptionHash() {
        $coll = Mage::getResourceModel("orbaallegro/template_collection");
        /* @var $coll Orba_Allegro_Model_Resource_Template_Collection */
        $out = array(''=> "(" . Mage::helper('orbaallegro')->__('No template') . ")");
        foreach($coll->toOptionHash() as $k=>$v){
            $out[$k]=$v;
        }
        return $out;
    }
}

?>
