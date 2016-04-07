<?php

class Orba_Allegro_Controller_Adminhtml_Mapping_Abstract 
    extends Orba_Allegro_Controller_Adminhtml_Abstract {

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('sales/orbaallegro')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Catalog'), Mage::helper('orbaallegro')->__('Allegro.pl'));
        return $this;
    }

    public function indexAction($title) {
        if(!is_null($msg=$this->_stopProcess())){
            $this->_getSession()->addError($msg);
            return $this->_redirectReferer();
        }
        $this->_title($this->__('Catalog'))
                ->_title($this->__('Allegro'))
                ->_title($this->__('Mass '.$title.' Mapping'));
        $this->_initAction();
        $this->renderLayout();
    }

    public function newAction() {
        if(!is_null($msg=$this->_stopProcess())){
            $this->_getSession()->addError($msg);
            return $this->_redirectReferer();
        }
        $this->_forward('edit');
    }
    
    public function editAction($attribute_code) {
        if(!is_null($msg=$this->_stopProcess())){
            $this->_getSession()->addError($msg);
            return $this->_redirectReferer();
        }
        $model = Mage::getModel('orbaallegro/mapping');
        if ($id = $this->getRequest()->getParam('mapping_id')) {
            $model->load($id);
        }
        Mage::register('orbaallegro_current_mapping', $model);
        Mage::register('orbaallegro_current_mapping_attribute_code', $attribute_code);
        
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true)->setCanLoadRulesJs(true);
        $this->_setActiveMenu('orbaallegro');
        if ($model->getId()) {
            $breadcrumb_title = Mage::helper('orbaallegro')->__('Edit Mapping');
            $breadcrumb_label = $breadcrumb_title;
        } else {
            $breadcrumb_title = Mage::helper('orbaallegro')->__('New Mapping');
            $breadcrumb_label = Mage::helper('orbaallegro')->__('Create Mapping');
        }
        $this->_title($breadcrumb_title);
        $this->_addBreadcrumb($breadcrumb_label, $breadcrumb_title);
        if ($values = $this->_getSession()->getData('mapping_form_data', true)) {
            $model->addData($values);
        }
        if ($edit_block = $this->getLayout()->getBlock('mapping_edit')) {
            $edit_block->setEditMode($model->getId() > 0);
        }
        $model->getConditions()->setJsFormObject('conditions_fieldset');
        $this->_addContent($this->getLayout()->createBlock('orbaallegro/adminhtml_mapping_edit'));
        $this->renderLayout();
    }

    public function saveAction() {
        if(!is_null($msg=$this->_stopProcess())){
            $this->_getSession()->addError($msg);
            return $this->_redirectReferer();
        }
        $request = $this->getRequest();
        if (!$request->isPost()) {
            $this->getResponse()->setRedirect($this->getUrl('*/*/index'));
        }
        $mapping = Mage::getModel('orbaallegro/mapping');
        if ($id = (int) $request->getParam('mapping_id')) {
            $mapping->load($id);
        }
        $redirected = false;
        try {
            $data = $request->getPost();
            $data['conditions'] = $data['rule']['conditions'];
            unset($data['rule']);
            $mapping->addData($data);
            $mapping->loadPost($data);
            $params = $request->getParams();
            $mapping->save();
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('orbaallegro')->__('The mapping has been saved.'));
            if (isset($params['back'])) {
                $this->_redirect('*/*/edit', array('mapping_id' => $mapping->getId()));
            } else {
                $this->_redirect('*/*/index');
            }
            $redirected = true;
        } catch (Exception $e) {
            $this->_getSession()->addException($e, Mage::helper('orbaallegro')->__('An error occurred while saving this mapping.'));
            $this->_getSession()->setData('mapping_form_data', $this->getRequest()->getParams());
        }
        if (!$redirected) {
            $this->_forward('new');
        }
    }

    public function deleteAction() {
        $mapping = Mage::getModel('orbaallegro/mapping')
                ->load($this->getRequest()->getParam('mapping_id'));
        if ($mapping->getId()) {

            $success = false;
            try {
                $mapping->delete();
                $success = true;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException($e, Mage::helper('orbaallegro')->__('An error occurred while deleting this mapping.'));
            }
            if ($success) {
                $this->_getSession()->addSuccess(Mage::helper('orbaallegro')->__('The mapping has been deleted.'));
            }
        }
        $this->_redirect('*/*/index');
    }

    public function runAction() {
        $mapping = Mage::getModel('orbaallegro/mapping')
                ->load($this->getRequest()->getParam('mapping_id'));
        $scope = array();
        if ($mapping->run()) {
            $this->_getSession()->addSuccess(Mage::helper('orbaallegro')->__('The mapping has been finished successfully.'));
        } else {
            $this->_getSession()->addError(Mage::helper('orbaallegro')->__('An error occurred while running this mapping.'));
        }
        $this->_redirect('*/*/index', $scope);
    }

    public function runallAction($attribute_code) {
        if (Mage::getModel('orbaallegro/mapping')->runAll($attribute_code)) {
            $this->_getSession()->addSuccess(Mage::helper('orbaallegro')->__('The mapping has been finished successfully.'));
        } else {
            $this->_getSession()->addError(Mage::helper('orbaallegro')->__('An error occurred while running mappings.'));
        }
        $this->_redirect('*/*/index');
    }

}