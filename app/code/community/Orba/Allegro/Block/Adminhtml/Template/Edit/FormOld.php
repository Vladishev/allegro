<?php
class Orba_Allegro_Block_Adminhtml_Template_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {
    
    public function __construct() {
        parent::__construct();
    }

    public function getModel() {
        return Mage::registry('orbaallegro_current_template');
    }

    protected function _prepareForm() {
        $model = $this->getModel();
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getData('action'),
            'method' => 'post'
        ));
        // Template data
        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => Mage::helper('orbaallegro')->__('Template'),
        ));
        if ($model->getId()) {
            $fieldset->addField('entity_id', 'hidden', array(
                'name' => 'entity_id',
                'value' => $model->getId(),
            ));
        }
        $fieldset->addField('name', 'text', array(
            'name' => 'name',
            'label' => Mage::helper('orbaallegro')->__('Name'),
            'title' => Mage::helper('orbaallegro')->__('Name'),
            'class' => 'requried-entry',
            'required' => true
        ));
        $fieldset->addField('html', 'textarea', array(
            'name' => 'html',
            'label' => Mage::helper('orbaallegro')->__('HTML Content'),
            'title' => Mage::helper('orbaallegro')->__('HTML Content'),
            'class' => 'requried-entry',
            'required' => true,
            'style' => 'width: 700px; height: 350px;',
            'after_element_html' => '<br /><small>'.Mage::helper('orbaallegro')->__('You can add any product attribute by inserting <strong>{attribute/code_of_the_attribute}</strong> code, eg. {attribute/name} for Name attribute.')
                .'<br />'.Mage::helper('orbaallegro')->__('You can add table with all the attributes selected below by inserting <strong>{attributes_table}</strong> code.').'</small>',
        ));
        // Attributes
        $fieldset = $form->addFieldset('product_attributes', array(
            'legend' => Mage::helper('orbaallegro')->__('Product attributes shown in table'),
        ));
        
        $eavModel = Mage::getModel('eav/config');
        /* @var $eavModel Mage_Eav_Model_Config */
        $entity_type_id = $eavModel->getEntityType('catalog_product')->getEntityTypeId();
        
        $attributes_collection = Mage::getModel('catalog/entity_attribute')->getCollection()
                ->addFieldToFilter('entity_type_id', $entity_type_id);
        $attributes = array();
        foreach ($attributes_collection as $attribute) {
            $attributes[$attribute->getAttributeCode()] = array(
                'label' => $attribute->getAttributeCode().($attribute->getData('frontend_label') ? ' ('.$attribute->getData('frontend_label').')' : ''),
                'value' => $attribute->getAttributeCode()
            );
        }
        ksort($attributes);

        
        
        $fieldset->addField('attributes', 'checkboxes', array(
            'label'     => Mage::helper('orbaallegro')->__('Attributes'),
            'name'      => 'attributes[]',
            'values'    => $attributes
        ));
        
        $data = $model->getData();
        
        if (isset($data['attributes']) && !is_array($data['attributes'])) {
            $data['attributes'] = explode(',', $data['attributes']);
        }
        
        $form->setValues($data);
        $form->setAction($this->getUrl('*/*/save'));
        $form->setUseContainer(false);
        $this->setForm($form);
        return parent::_prepareForm();
    }
    
}
