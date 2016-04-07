<?php
class Orba_Allegro_Block_Adminhtml_Transaction_Grid_Column_Renderer_Unhandled
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
        
    public function render(Varien_Object $row)
    {
        $html = '<a href="#" onclick="updateUnhandledQty(event, this, '. $row->getId() .'); return false">' . Mage::helper("orbaallegro")->__("Get Item Count") . '</a>';
        return $html;
    }
}
