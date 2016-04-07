<?php
class Orba_Allegro_Adminhtml_Mapping_ConditionController extends Mage_Adminhtml_Controller_Action {
	
    public function newConditionHtmlAction() {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];
        $model = Mage::getModel($type)
                ->setId($id)
                ->setType($type)
                ->setRule(Mage::getModel('catalogrule/rule'))
                ->setPrefix('conditions');
        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }
        if ($model instanceof Mage_Rule_Model_Condition_Abstract) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }
    
}