<?php
class Orba_Allegro_Block_Adminhtml_Service_Form_Element_Attributes
    extends Varien_Data_Form_Element_Text {

    public function __construct($attributes = array()) {
//        if($attributes['is_service_specified']){
//            $attributes['disabled'] = 'disabled';
//        }
        parent::__construct($attributes);
    }

    public function getAfterElementHtml() {
        $parentHtml  = parent::getAfterElementHtml();
//        if($this->getData('is_service_specified')){
//            return $parentHtml;
//        }

        $blockClass = Mage::getConfig()->getBlockClassName("adminhtml/widget_button");
        $button = new $blockClass;

        /* @var $button Mage_Adminhtml_Block_Widget_Button */
        $button->setLabel(Mage::helper("orbaallegro")->__("Get extra fields"));
        $button->setOnClick("setLocation('".$this->getUrl()."');");
        $button->setDisabled(true);
        $button->setId('TEST');

        $button = '<div style="margin-top: 10px;">'.$button->toHtml().'</div>';

        return $parentHtml . $button;
    }
}
