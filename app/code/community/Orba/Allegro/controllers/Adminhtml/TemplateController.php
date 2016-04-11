<?php

class Orba_Allegro_Adminhtml_TemplateController extends Orba_Allegro_Controller_Adminhtml_Abstract {

    protected $_hasError = false;
    
    public function indexAction() {
        if(!is_null($msg=$this->_stopProcess())){
            $this->_getSession()->addError($msg);
            return $this->_redirectReferer();
        }
        $this->_title($this->__('Catalog'))
                ->_title($this->__('Allegro.pl'))
                ->_title($this->__('Templates'));
        $this->loadLayout();
        $this->renderLayout();
    }

    public function newAction() {
        if(!is_null($msg=$this->_stopProcess())){
            $this->_getSession()->addError($msg);
            return $this->_redirectReferer();
        }
        $model = $this->getModel();
        $this->_title($this->__('Catalog'))
            ->_title($this->__('Allegro.pl'))
            ->_title($this->__('New Template'));
        
        $sessionData = $this->_getSession()->getData('orbaallegro_template_form_data');
        if(is_array($sessionData)){
            $this->getModel()->addData($sessionData);
            $this->_getSession()->setData('orbaallegro_template_form_data', null);
        }
        
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
    }

    public function editAction() {
        if(!is_null($msg=$this->_stopProcess())){
            $this->_getSession()->addError($msg);
            return $this->_redirectReferer();
        }
        $model = $this->getModel();
        
        $this->loadLayout();
        $this->_setActiveMenu('orbaallegro/template');
        if ($model->getId()) {
            $breadcrumb_title = Mage::helper('orbaallegro')->__('Edit Template');
            $breadcrumb_label = $breadcrumb_title;
        } else {
            $breadcrumb_title = Mage::helper('orbaallegro')->__('New Template');
            $breadcrumb_label = Mage::helper('orbaallegro')->__('Create Template');
        }
        $this->_title($breadcrumb_title);
        $this->_addBreadcrumb($breadcrumb_label, $breadcrumb_title);
        // restore data
        if ($values = $this->_getSession()->getData('orbaallegro_template_form_data', true)) {
            $model->addData($values);
        }
        if ($edit_block = $this->getLayout()->getBlock('orbaallegro_template_edit')) {
            $edit_block->setEditMode($model->getId() > 0);
        }
        
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        
        $sessionData = $this->_getSession()->getData('orbaallegro_template_form_data');
        if(is_array($sessionData)){
            $this->getModel()->addData($sessionData);
            $this->_getSession()->setData('orbaallegro_template_form_data', null);
        }
        
        $this->renderLayout();
    }

    public function saveAction() {
        if(!is_null($msg=$this->_stopProcess())){
            $this->_getSession()->addError($msg);
            return $this->_redirectReferer();
        }
        $request = $this->getRequest();
        if (!$request->isPost()) {
            $this->getResponse()->setRedirect($this->getUrl('*/template'));
        }
        $model = $this->getModel();
        $redirected = false;
        $this->_getSession()->setData('orbaallegro_template_form_data', null);
        try {
            $data = $request->getParam('template');
            $model->addData($data);
            $model->save();
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('orbaallegro')->__('The template has been saved.'));
            $this->_redirect('*/*/index', array("store"=>$this->getRequest()->getParam('store')));
            $redirected = true;
        } catch (Exception $e) {
            $this->_getSession()->addException($e, Mage::helper('orbaallegro')->__('An error occurred while saving this template.'));
            $this->_getSession()->setData('orbaallegro_template_form_data', $this->getRequest()->getParam('template'));
        }
        if (!$redirected) {
            $this->_forward($model->getId() ? 'edit' : 'new');
        }
    }

    public function deleteAction() {
        $model = $this->getModel();
        
        if ($model->getId()) {
            $success = false;
            try {
                $model->delete();
                $success = true;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException($e, Mage::helper('orbaallegro')->__('An error occurred while deleting this template.'));
            }
            if ($success) {
                $this->_getSession()->addSuccess(Mage::helper('orbaallegro')->__('The template has been deleted.'));
            }
        }
        $this->_redirect('*/*');
    }
   
    /**
     * @return Orba_Allegro_Model_Template
     */
    public function getModel() {
        if(!Mage::registry('orbaallegro_current_template')){
            $model = Mage::getModel('orbaallegro/template');
            $model->setStoreId($this->getRequest()->getParam('store'));
            if ($id = $this->getRequest()->getParam('entity_id')) {
                $model->load($id);
            }
            Mage::register('orbaallegro_current_template', $model);
        };
        return Mage::registry('orbaallegro_current_template');
    }

}