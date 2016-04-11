<?php

/**
 * Description of Service
 */
class Orba_Allegro_Block_Adminhtml_System_Config_Country extends Mage_Adminhtml_Block_System_Config_Form_Field {
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        // Append controling script
        
        $json = new stdClass;
        
        $req = Mage::app()->getRequest();
        if($req->getParam('website')!==null){
            $json->website = $req->getParam('website');
        }
        if($req->getParam('store')!==null){
            $json->store = $req->getParam('store');
        }
        
        $block = $this->getLayout()->
                createBlock("core/template")->
                setTemplate("orbaallegro/system/config/country.phtml")->
                setElementId($element->getHtmlId())->
                setProvinceJsonUrl(Mage::getUrl("*/system_config/getProvincesJson"))->
                setJsonParmas(Zend_Json::encode($json));
        
        return parent::_getElementHtml($element) .  $block->toHtml();
    }
}

?>
