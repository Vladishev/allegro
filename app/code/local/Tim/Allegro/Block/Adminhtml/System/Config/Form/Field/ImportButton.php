<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Allegro
 * @copyright  Copyright (c) 2016 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */
class Tim_Allegro_Block_Adminhtml_System_Config_Form_Field_ImportButton extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /*
     * Set template
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('tim_allegro/system/config/form/field/export_import_button.phtml');
    }

    /**
     * Return element html
     *
     * @param  Varien_Data_Form_Element_Abstract $element element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->_toHtml();
    }

    /**
     * Generate Import button html
     *
     * @return string
     */
    public function getButtonHtml()
    {
        /* @var $buttonBlock Mage_Adminhtml_Block_Widget_Button */
        $buttonBlock = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'id'        => 'import_button',
                'label'     => $this->helper('tim_allegro')->__('Import'),
                'onclick'   => 'javascript:importProducts(); return false;'
            ));

        return $buttonBlock->toHtml();
    }

    /**
     * Return ajax url for import button
     *
     * @return string
     */
    public function getAjaxImportUrl()
    {
        return Mage::getSingleton('adminhtml/url')->getUrl('tim_allegro/adminhtml_import/importCsv');
    }
}