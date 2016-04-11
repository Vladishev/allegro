<?php

class Orba_Allegro_Block_Adminhtml_Category_Renderer extends Varien_Data_Form_Element_Text {

    public function getElementHtml() {
        $helper = Mage::helper('orbaallegro');
        $_category = Mage::getModel('orbaallegro/category');
        $id = $this->getEscapedValue();
        $value = "";
        $name_path = "";
        
        if(!$this->getCountryId()){
            $this->setCountryId($_category->getRequestCountryId());
        }
        $model = Mage::getModel('orbaallegro/category')->load($id);
        
        // Check is valid for store
        if($model->getCountryId()==$this->getCountryId()){
            $name_path = $_category->getNamePath($id);
            if($this->getShowExternalValue()){
                $value = $model->getExternalId();
            }else{
                $value = $id;
            }
        }
        
        $layout = Mage::getSingleton('core/layout');
        $block = $layout->createBlock('core/template')->setTemplate('orbaallegro/category/renderer.phtml');
        $block->setNamePath($name_path)
                ->setCategory($this->getCountryId())
                ->setCountryId($this->getCountryId())
                ->setHtmlId($this->getHtmlId())
                ->setName($this->getName())
                ->setEscapedValue($value)
                ->setRequired($this->getRequired())
                ->setReadonly($this->getReadonly());
        $html = $block->toHtml();
        $html .= $this->getAfterElementHtml();
        return $html;
    }
    
    
}