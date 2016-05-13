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
 * Class Orba_Allegro_Block_Adminhtml_System_Config_Form_Field_TimShipping
 */
class Orba_Allegro_Block_Adminhtml_System_Config_Form_Field_TimShipping extends Mage_Core_Block_Html_Select
{
    /**
     * Render HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $options = Mage::getModel('orbaallegro/system_config_source_timShipping')
            ->toOptionArray();
        foreach ($options as $option) {
            $this->addOption($option['value'], $option['label']);
        }

        return parent::_toHtml();
    }

    /**
     * @param $value
     * @return mixed
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }
}
