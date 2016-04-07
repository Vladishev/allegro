<?php
class Orba_Allegro_Block_Adminhtml_Template_Edit extends Mage_Adminhtml_Block_Widget {

    protected $_editMode = false;

    /**
     *  @return Orba_Allegro_Model_Template Description
     */
    public function getModel() {
        return Mage::registry('orbaallegro_current_template');
    }

    protected function _prepareLayout() {
        $this->setChild('back_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('catalog')->__('Back'),
                    'onclick' => "window.location.href = '" . $this->getUrl('*/*') . "'",
                    'class' => 'back'
                ))
        );
        $this->setChild('reset_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('catalog')->__('Reset'),
                    'onclick' => 'window.location.href = window.location.href'
                ))
        );
        $this->setChild('save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('orbaallegro')->__('Save Template'),
                    'onclick' => 'templateControl.save();',
                    'class' => 'save'
                ))
        );
        $this->setChild('delete_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('orbaallegro')->__('Delete Template'),
                    'onclick' => 'templateControl.remove();',
                    'class' => 'delete'
                ))
        );
        return parent::_prepareLayout();
    }

    public function getBackButtonHtml() {
        return $this->getChildHtml('back_button');
    }

    public function getResetButtonHtml() {
        return $this->getChildHtml('reset_button');
    }

    public function getSaveButtonHtml() {
        return $this->getChildHtml('save_button');
    }

    public function getDeleteButtonHtml() {
        return $this->getChildHtml('delete_button');
    }

    public function getSaveAsButtonHtml() {
        return $this->getChildHtml('save_as_button');
    }

    public function setEditMode($value = true) {
        $this->_editMode = (bool)$value;
        return $this;
    }

    public function getEditMode() {
        return $this->_editMode;
    }

    public function getHeaderText() {
        if ($this->getEditMode()) {
            return Mage::helper('orbaallegro')->__('Edit Template');
        }
        return  Mage::helper('orbaallegro')->__('New Template');
    }


    /**
     * Return return template name for JS
     *
     * @return string
     */
    public function getJsTemplateName()
    {
        return addcslashes($this->getModel()->getTemplateCode(), "\"\r\n\\");
    }

    public function getSaveUrl() {
        return $this->getUrl('*/*/save', array("_current"=>true));
    }

    public function getDeleteUrl() {
        return $this->getUrl('*/*/delete', array("_current"=>true));
    }

    public function getSaveAsFlag() {
        return $this->getRequest()->getParam('_save_as_flag') ? '1' : '';
    }

    protected function isSingleStoreMode() {
        return Mage::app()->isSingleStoreMode();
    }
    
    public function canShowSwitcher() {
        return false;
    }

    protected function getStoreId() {
        return Mage::app()->getStore(true)->getId();
    }
}
