<?php

class Orba_Allegro_Block_Adminhtml_System_Config_Shipping extends Mage_Adminhtml_Block_System_Config_Form_Field {
    
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
        $block = $this->getLayout()->
                createBlock("core/template")->
                setTemplate("orbaallegro/system/config/shipping.phtml");
        return $block->toHtml() . parent::_getElementHtml($element);
    }
    
}