<?php

class Orba_Allegro_Adminhtml_Mapping_TemplateController 
    extends Orba_Allegro_Controller_Adminhtml_Mapping_Abstract {

    public function indexAction($title=null) {
        parent::indexAction('Default Templates');
    }
    
    public function editAction($attribute_code=null) {
        parent::editAction(Orba_Allegro_Model_Mapping::ATTR_CODE_TEMPLATE);
    }
    
    public function runallAction($attribute_code=null) {
        parent::runallAction(Orba_Allegro_Model_Mapping::ATTR_CODE_TEMPLATE);
    }

}