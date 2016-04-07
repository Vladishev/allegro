<?php
class Orba_Allegro_Block_Adminhtml_Template_Helper_Form_Wysiwyg_Content
    extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array('id' => 'wysiwyg_edit_form', 'action' => $this->getData('action'), 'method' => 'post'));

        $config['document_base_url']     = $this->getData('store_media_url');
        $config['store_id']              = $this->getData('store_id');
        $config['add_variables']         = false;
        $config['add_widgets']           = false;
        $config['widget_window_url']     = $this->getUrl('*/*/widgets');
        $config['add_directives']        = false;
        $config['use_container']         = true;
        $config['container_class']       = 'hor-scroll';

        $form->addField($this->getData('editor_element_id'), 'editor', array(
            'name'             => 'content',
            'style'            => 'width:1024px;height:460px',
            'required'         => true,
            'force_load'       => true,
            'config'           => Mage::getSingleton('cms/wysiwyg_config')->getConfig($config)
        ));
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
