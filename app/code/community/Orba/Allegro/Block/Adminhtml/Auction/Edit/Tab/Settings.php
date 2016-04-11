<?php
class Orba_Allegro_Block_Adminhtml_Auction_Edit_Tab_Settings extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareLayout()
    {
        $this->setChild('continue_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Continue'),
                    'onclick'   => "setSettings('".$this->getContinueUrl()."','category_id', 'template_id', 'shop_category_id')",
                    'class'     => 'save'
                    ))
                );
        return parent::_prepareLayout();
    }

    protected function _prepareForm()
    {
        $helper = Mage::helper('orbaallegro');
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('settings', 
                array('legend'=>$helper->__('Create new auction settings')));
        
        // Add template & category choosers
        $fieldset->addType('orbaallegro_category', 
                Mage::getConfig()->getBlockClassName('orbaallegro/adminhtml_category_renderer'));
        
        $fieldset->addField('category_id', 'orbaallegro_category', array(
            'name'          => 'category_id',
            'country_id'    => $this->_getCountryCode(),
            'label'         => $helper->__('Category'),
            'required'      => true,
            'title'         => $helper->__('Category')
        ));
        
        $fieldset->addType('orbaallegro_shop_category', 
                Mage::getConfig()->getBlockClassName('orbaallegro/adminhtml_shop_category_renderer'));
        
        
        $fieldset->addField('shop_category_id', 'orbaallegro_shop_category', array(
            'name'          => 'shop_category_id',
            'country_id'    => $this->_getCountryCode(),
            'label'         => $helper->__('Shop category'),
            'required'      => false,
            'title'         => $helper->__('Shop category')
        ));
        
        $options = Mage::getModel('orbaallegro/auction_source_template')->toOptionHash();
        $fieldset->addField('template_id', 'select', array(
            'name' => 'template_id',
            'label' => $helper->__('Template'),
            'title' => $helper->__('Template'),
            'options' => $options
        ));
        
        $fieldset->addField('continue_button', 'note', array(
            'text' => $this->getChildHtml('continue_button'),
        ));

        $form->setValues($this->_getValues());
        
        $this->setForm($form);
    }
    
    protected function _getCountryCode() {
        $config = Mage::getSingleton('orbaallegro/config');
        /* @var $config Orba_Allegro_Model_Config */
        return $config->getCountryCode($this->getStore());
    }

    public function getContinueUrl()
    {
        return $this->getUrl('*/*/new', array(
            '_current'         => true,
            'template'      => '{{template_id}}',
            'category'      => '{{category_id}}',
            'shop_category'      => '{{shop_category_id}}',
        ));
    }
    
    protected function _getValues() {
        $values = array();
        $product = $this->getProduct();
        if($product && $product->getId()){
            if($product->hasData(Orba_Allegro_Model_Mapping::ATTR_CODE_CATEGORY)){
                $values['category_id'] = $product->getData(Orba_Allegro_Model_Mapping::ATTR_CODE_CATEGORY);
            }
            if($product->hasData(Orba_Allegro_Model_Mapping::ATTR_CODE_SHOP_CATEGORY)){
                $values['shop_category_id'] = $product->getData(Orba_Allegro_Model_Mapping::ATTR_CODE_SHOP_CATEGORY);
            }
            if($product->hasData(Orba_Allegro_Model_Mapping::ATTR_CODE_TEMPLATE)){
                $values['template_id'] = $product->getData(Orba_Allegro_Model_Mapping::ATTR_CODE_TEMPLATE);
            }
        }
        return $values;
    }
}
