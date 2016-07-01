<?php

class Orba_Allegro_Block_Adminhtml_Mapping_Edit_Form extends 
    Mage_Adminhtml_Block_Widget_Form {

    public function getModel() {
        return Mage::registry('orbaallegro_current_mapping');
    }

    protected function _prepareForm() {
        $helper = Mage::helper('orbaallegro');
        $model = $this->getModel();
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getData('action'),
            'method' => 'post'
        ));
        
        // Service chooser
        $serviceFieldset = $form->addFieldset('mapping_service', array(
            'legend' => $helper->__('Service'),
        ));
        
        $serviceFieldset->addType('orbaallegro_mapping_service', 
                Mage::getConfig()->getBlockClassName('orbaallegro/adminhtml_service_form_element_service'));
        
        // Fix disabled value
        if($this->_isServiceSpecified($model)){
            $serviceName = "country_code_select";
            $serviceFieldset->addField('country_code', 'hidden', array(
                'name' => 'country_code'
            ));
            $model->setData($serviceName, $this->_getService($model));
        }else{
            $serviceName = "country_code";
        }
        
        $serviceSelect = $serviceFieldset->addField($serviceName, 'orbaallegro_mapping_service', array(
            'name' => $serviceName,
            'require' => true,
            'is_service_specified' => $this->_isServiceSpecified($model),
            'url' => $this->getUrl('*/*/new'),
            'label' => $helper->__('Allegro Service'),
            'title' => $helper->__('Allegro Service'),
            'options' => Mage::getSingleton('orbaallegro/mapping_source_service')->toOptionHash()
        ));
        
        
        
        $attribute_code = Mage::registry('orbaallegro_current_mapping_attribute_code');
        // Mapping information
        if ($model->getId()) {
            $serviceFieldset->addField('mapping_id', 'hidden', array(
                'name' => 'mapping_id',
                'value' => $model->getId(),
            ));
        } else {
            $serviceFieldset->addField('attribute_code', 'hidden', array(
                'name' => 'attribute_code'
            ));
            $serviceFieldset->addField('attribute_code_2', 'hidden', array(
                'name' => 'attribute_code_2'
            ));
            $model->setAttributeCode($attribute_code);
            if($attribute_code==Orba_Allegro_Model_Mapping::ATTR_CODE_CATEGORY){
                 $model->setData('attribute_code_2', Orba_Allegro_Model_Mapping::ATTR_CODE_SHOP_CATEGORY);
            }
        }
        
        /**
         * No service specified during 
         */
        if(!$this->_isServiceSpecified($model)){
            $form->setValues($model->getData());
            $form->setUseContainer(true);
            $this->setForm($form);
            return parent::_prepareForm();
        }else{
            $model->setCountryCode($this->_getService($model));
        }
        
        
        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => $helper->__('Mapping Information'),
        ));
        
        switch ($attribute_code) {
            case Orba_Allegro_Model_Mapping::ATTR_CODE_CATEGORY:
                $fieldset->addType('text', Mage::getConfig()->getBlockClassName('orbaallegro/adminhtml_service_form_element_attributes'));
                $fieldset->addType('orbaallegro_category', Mage::getConfig()->getBlockClassName('orbaallegro/adminhtml_category_renderer'));
                $fieldset->addType('orbaallegro_shop_category', Mage::getConfig()->getBlockClassName('orbaallegro/adminhtml_shop_category_renderer'));
                
                $fieldset->addField('entity_id', 'orbaallegro_category', array(
                    'name' => 'entity_id',
                    'country_id' => $this->_getService($model),
                    'label' => $helper->__('Allegro Category'),
                    'required' => true,
                    'title' => $helper->__('Allegro Category')
                ));
                $fieldset->addField('entity_id_2', 'orbaallegro_shop_category', array(
                    'name' => 'entity_id_2',
                    'country_id' => $this->_getService($model),
                    'label' => $helper->__('Allegro Shop Category'),
                    'title' => $helper->__('Allegro Shop Category')
                ));
                break;
            case Orba_Allegro_Model_Mapping::ATTR_CODE_TEMPLATE:
                $options = Mage::getModel('orbaallegro/mapping_source_template')->toOptionHash();
                $fieldset->addField('entity_id', 'select', array(
                    'name' => 'entity_id',
                    'label' => $helper->__('Default Template'),
                    'title' => $helper->__('Default Template'),
                    'options' => $options
                ));
                break;
        }
        
        
        /**
         * Store field
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $field = $fieldset->addField('store_id', 'multiselect', array(
                'name'      => 'stores[]',
                'label'     => Mage::helper('cms')->__('Store View'),
                'title'     => Mage::helper('cms')->__('Store View'),
                'required'  => true,
                'note' => $helper->__('All stores views with the selected service assigment.'),
                'values'    => Mage::getSingleton('orbaallegro/mapping_store')->
                    getStoreValuesForForm(false,true,$this->_getService($model))
            ));
            $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
            $field->setRenderer($renderer);
        }
        else {
            $fieldset->addField('store_id', 'hidden', array(
                'name'      => 'stores[]',
                'value'     => Mage::app()->getStore(true)->getId()
            ));
            $model->setStoreId(Mage::app()->getStore(true)->getId());
        }
        
        $fieldset->addField('priority', 'text', array(
            'name' => 'priority',
            'label' => $helper->__('Priority'),
            'title' => $helper->__('Priority'),
            'class' => 'validate-digits'
        ));
        // Rules
        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl($this->getUrl('*/mapping_condition/newConditionHtml/form/conditions_fieldset'));

        $fieldset = $form->addFieldset('conditions_fieldset', array(
            'legend'=>Mage::helper('catalogrule')->__('Conditions (leave blank for all products)'))
        )->setRenderer($renderer);
        $fieldset->addField('conditions', 'text', array(
            'name' => 'conditions',
            'label' => Mage::helper('catalogrule')->__('Conditions'),
            'title' => Mage::helper('catalogrule')->__('Conditions'),
            'required' => true,
        ))->setRule($model)->setRenderer(Mage::getBlockSingleton('rule/conditions'));
        
        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
    
    protected function _isServiceSpecified($model=null) {
        if(is_null($this->getData('service_specified'))){
            $value = false;
            $countryCode = $this->_getService($model);
            if($countryCode){
                $model = Mage::getModel('orbaallegro/service')->load($countryCode, 'service_country_code');
                if($model->getId()){
                    $value = true;
                }
            }
            $this->setData('service_specified', $value);
        }
        return $this->getData('service_specified');
    }
    
    protected function _getService($model=null) {
        if($model && $model->getId()){
            return $model->getCountryCode();
        }
        return Mage::app()->getRequest()->getParam('country_code');
    }

}
