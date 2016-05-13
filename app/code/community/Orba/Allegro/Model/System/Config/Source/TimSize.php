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
 * Creates option array
 *
 * Class Orba_Allegro_Model_System_Config_Source_TimSize
 */
class Orba_Allegro_Model_System_Config_Source_TimSize
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_getProductAttributeOptions('tim_kategoria_rozmiaru');
    }

    /**
     * Returns full array of select type attribute
     *
     * @param string $attributeCode
     * @return bool
     */
    protected function _getProductAttributeOptions($attributeCode) {
        $attribute = Mage::getSingleton('eav/config')
            ->getAttribute(Mage_Catalog_Model_Product::ENTITY, $attributeCode);
        if (!$attribute->usesSource()) {
            return false;
        }

        return $attribute->getSource()->getAllOptions(false);
    }
}