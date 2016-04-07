<?php
class Orba_Allegro_Block_Adminhtml_Auction_Grid_Column_Renderer_Additionaloptions
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
        
    protected $_services = array();
    
    public function render(Varien_Object $row)
    {   
        if($bitMask = $row->getAuctionAdditionalOptions()){
            $retArr = array();
            $opts = Mage::getSingleton("orbaallegro/system_config_source_attribute_template_additionaloption")->toOptionHash();
            foreach($opts as $key=>$val){
                if($bitMask & $key){
                    $retArr[] = $val;
                }
            }
            if(count($retArr)){
                return join("<br/>", $retArr);
            }
        }
        return "";
    }
}
