<?php

class Orba_Allegro_Block_Adminhtml_Template_Edit_Tab_Settings
    extends Orba_Allegro_Block_Adminhtml_Template_Form
{
    /**
     * Load Wysiwyg on demand and prepare layout
     */

    protected function _prepareLayout()
    {
        $this->setChild('continue_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('orbaallegro')->__('Continue'),
                    'onclick'   => "setSettings('".$this->getContinueUrl()."','country_code')",
                    'class'     => 'save'
                    ))
                );
        return parent::_prepareLayout();
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('settings', array('legend'=>Mage::helper('orbaallegro')->__('Create template settings')));


        $serviceCollection = Mage::getResourceModel('orbaallegro/service_collection')->
                addAvailableFilter()->
                addFieldToSelect(array("service_country_code", "service_name"));
        
        $config = Mage::getModel("orbaallegro/config");
        /* @var $config Orba_Allegro_Model_Config */
        
        $values = array();
        foreach ($serviceCollection as $service) {
            $values[] = array(
                'label' => $service->getServiceName(),
                'value' => $service->getServiceCountryCode()
            );
        }
        
        $fieldset->addField('country_code', 'select', array(
            'label' => Mage::helper('orbaallegro')->__('Allegro service'),
            'title' => Mage::helper('orbaallegro')->__('Allegro service'),
            'name'  => 'country_code',
            'values'=> $values,
            'required'  => true,
            'value' => $config->getCountryCode()
        ));

        $fieldset->addField('continue_button', 'note', array(
            'text' => $this->getChildHtml('continue_button'),
        ));

        $this->setForm($form);
    }

    public function getContinueUrl()
    {
        return $this->getUrl('*/*/new', array(
            '_current'      => true,
            'country_code'  => '{{country_code}}'
        ));
    }
}
