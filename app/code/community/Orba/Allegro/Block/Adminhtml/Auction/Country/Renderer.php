<?php

class Orba_Allegro_Block_Adminhtml_Auction_Country_Renderer extends Varien_Data_Form_Element_Select {

    public function __construct($attributes = array()) {
        $attributes['options'] = $this->_getOptions();
        parent::__construct($attributes);
    }
    
    protected function _getOptions() {
        $model = Mage::getModel("orbaallegro/system_config_source_country");
        /* @var $model Orba_Allegro_Model_System_Config_Source_Country */
        return $model->toOptionHash();
    }
    
    public function toHtml() {
        if($this->getStateFieldId()){
            $block = Mage::getSingleton('core/layout')->
                createBlock("core/template")->
                setStateFieldId($this->getStateFieldId())->
                setJsonParmas($this->getJsonParmas())->
                setProvinceJsonUrl($this->getProvinceJsonUrl())->
                setHtmlId($this->getHtmlId())->
                setTemplate('orbaallegro/auction/country/renderer.phtml');
            return parent::toHtml().$block->toHtml();
        }
        return parent::toHtml();
    }
    
    public function getStateFieldId() {
        if($this->getData('state_field_id')){
            $field = $this->getData('state_field_id');
            if(is_int($field)){
                $field = $this->getForm()->getPrefix() . $field;
            }
            return $field;
        }
        return null;
    }
    
    public function getProvinceJsonUrl() {
        return Mage::getUrl("*/system_config/getProvincesJson");
    }
    
    public function getJsonParmas() {
        $json = new stdClass;
        $req = Mage::app()->getRequest();
        if($req->getParam('store')!==null){
            $json->store = $req->getParam('store');
        }
        return Zend_Json::encode($json);
    }
    
}