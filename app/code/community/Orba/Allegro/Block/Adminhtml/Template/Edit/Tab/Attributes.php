<?php

class Orba_Allegro_Block_Adminhtml_Template_Edit_Tab_Attributes 
    extends Orba_Allegro_Block_Adminhtml_Template_Form
{
    /**
     * Load Wysiwyg on demand and prepare layout
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (Mage::helper('catalog')->isModuleEnabled('Mage_Cms')
            && Mage::getSingleton('cms/wysiwyg_config')->isEnabled()
        ) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }

    /**
     * Prepare attributes form
     *
     * @return null
     */
    protected function _prepareForm()
    {
        $group = $this->getGroup();
        if ($group) {
            
            $model = Mage::registry('orbaallegro_current_template');
            $form = new Varien_Data_Form();

            // Initialize product object as form property to use it during elements generation
            $form->setDataObject($model);
            
            $fieldset = $form->addFieldset('group_fields' . $group->getId(), array(
                'legend' => Mage::helper('catalog')->__($group->getAttributeGroupName()),
                'class' => 'fieldset-wide'
            ));

            $attributes = $this->getGroupAttributes();        
            $values = $model->getData();
            $this->_setFieldset($attributes, $fieldset);
            
            if($group->getAttributeGroupName()==Orba_Allegro_Model_Template::GROUP_GENERAL){
                // Add attribute set id & country code
                $fieldset->addField('country_code', 'hidden', array('name'=>'country_code'));
                $fieldset->addField('attribute_set_id', 'hidden', array('name'=>'attribute_set_id'));
            }
            
            // Set default attribute values for new product
            if (!$model->getId()) {
                foreach ($attributes as $attribute) {
                    if (!isset($values[$attribute->getAttributeCode()])) {
                        $values[$attribute->getAttributeCode()] = $attribute->getDefaultValue();
                    }
                }
            }
            
            
            if ($description = $form->getElement('description')){
                /* @var $description Varien_Data_Form_Element_Textarea */
                // Do sth
            }

            $form->addValues($values);
            $form->setFieldNameSuffix('template');
            $this->setForm($form);
        }
    }

    /**
     * Retrieve additional element types
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        $result = array(
            'textarea' => Mage::getConfig()->getBlockClassName('orbaallegro/adminhtml_template_helper_form_wysiwyg')
        );

        $response = new Varien_Object();
        $response->setTypes(array());
        foreach ($response->getTypes() as $typeName => $typeClass) {
            $result[$typeName] = $typeClass;
        }

        return $result;
    }
}
