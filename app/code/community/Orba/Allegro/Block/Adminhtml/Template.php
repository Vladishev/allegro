<?php
class Orba_Allegro_Block_Adminhtml_Template extends Mage_Adminhtml_Block_Widget_Container {
    
    public function __construct() {
        parent::__construct();
        $this->setTemplate('orbaallegro/template.phtml');
    }

    protected function _prepareLayout() {
        $this->_addButton('add_new', array(
            'label'   => $this->__('Add Template'),
            'onclick' => "setLocation('{$this->getUrl('*/*/new')}')",
            'class'   => 'add'
        ));
        $this->setChild('grid', $this->getLayout()->createBlock('orbaallegro/adminhtml_template_grid', 'orbaallegro_template_grid'));
        return parent::_prepareLayout();
    }

    public function getAddNewButtonHtml() {
        return $this->getChildHtml('add_new_button');
    }
    
    public function getGridHtml() {
        return $this->getChildHtml('grid');
    }
    
    public function isSingleStoreMode()
    {
        if (!Mage::app()->isSingleStoreMode()) {
               return false;
        }
        return true;
    }
    
    public function getTemplateFilter() {
        if(!$this->getData("template_filter")){
            $filter = Mage::getModel("orbaallegro/template_filter");
            /* @var $filter Orba_Allegro_Model_Template_Filter */
            $filter->setTemplate($this);
            $this->setData("template_filter", $filter);
        }
        return $this->getData("template_filter");
    }
}
