<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Orba_Allegro
 * @copyright  Copyright (c) 2016 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */

/**
 * Class Tim_Recommendation_Block_System_Config_UserType
 */
class Orba_Allegro_Block_Adminhtml_System_Config_TimShipping extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    /**
     * Define blocks for _getRenderer method
     */
    const SHIPPING_METHOD = 'orbaallegro/adminhtml_system_config_form_field_timShipping';
    const TIM_SIZE = 'orbaallegro/adminhtml_system_config_form_field_timSize';

    /**
     * @var object Mage_Core_Block_Abstract
     */
    protected $_itemRenderer;

    /**
     * @var object Mage_Core_Block_Abstract
     */
    protected $_renderShipping;

    /**
     * @var object Mage_Core_Block_Abstract
     */
    protected $_renderSize;

    /**
     * Prepare to render
     */
    public function _prepareToRender()
    {
        $this->addColumn('shipping_method', array(
            'label' => Mage::helper('orbaallegro')->__('Shipping method'),
            'renderer' => $this->_getRenderer(self::SHIPPING_METHOD),
        ));

        $this->addColumn('tim_kategoria_rozmiaru', array(
            'label' => Mage::helper('orbaallegro')->__('tim_kategoria_rozmiaru'),
            'renderer' => $this->_getRenderer(self::TIM_SIZE),
        ));

        $this->addColumn('tim_shipping_price', array(
            'label' => Mage::helper('orbaallegro')->__('Price'),
            'style' => 'width:50px',
            'class' => 'validate-not-negative-number required-entry',
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('orbaallegro')->__('Add new type');
    }

    /**
     * Prepare block
     *
     * @param string $blockName
     * @return Mage_Core_Block_Abstract|object
     */
    protected function _getRenderer($blockName)
    {
        if ($blockName == self::SHIPPING_METHOD) {
            $this->_itemRenderer = $this->getLayout()->createBlock(
                $blockName, '',
                array('is_render_to_js_template' => true)
            );
            $this->_renderShipping = $this->_itemRenderer;
        }
        if ($blockName == self::TIM_SIZE) {
            $this->_itemRenderer = $this->getLayout()->createBlock(
                $blockName, '',
                array('is_render_to_js_template' => true)
            );
            $this->_renderSize = $this->_itemRenderer;
        }

        return $this->_itemRenderer;
    }

    /**
     * Prepare existing row data object
     *
     * @param Varien_Object
     */
    protected function _prepareArrayRow(Varien_Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $this->_renderShipping
                ->calcOptionHash($row->getData('shipping_method')),
            'selected="selected"'
        );
        $row->setData(
            'option_extra_attr_' . $this->_renderSize
                ->calcOptionHash($row->getData('tim_kategoria_rozmiaru')),
            'selected="selected"'
        );
    }
}