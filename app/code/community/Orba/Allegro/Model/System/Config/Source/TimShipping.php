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
 * Creates array of shipping methods based on data from system.xml
 *
 * Class Orba_Allegro_Model_System_Config_Source_TimShipping
 */
class Orba_Allegro_Model_System_Config_Source_TimShipping
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 36, 'label'=>Mage::helper('orbaallegro')->__('Economic postal parcel')),
            array('value' => 37, 'label'=>Mage::helper('orbaallegro')->__('Economic postal letter')),
            array('value' => 38, 'label'=>Mage::helper('orbaallegro')->__('Priority postal parcel')),
            array('value' => 39, 'label'=>Mage::helper('orbaallegro')->__('Priority postal letter')),
            array('value' => 40, 'label'=>Mage::helper('orbaallegro')->__('Paczka48 COD')),
            array('value' => 41, 'label'=>Mage::helper('orbaallegro')->__('Economic registered letter')),
            array('value' => 42, 'label'=>Mage::helper('orbaallegro')->__('Paczka24 COD')),
            array('value' => 43, 'label'=>Mage::helper('orbaallegro')->__('Priority registered letter')),
            array('value' => 44, 'label'=>Mage::helper('orbaallegro')->__('Courier parcel')),
            array('value' => 45, 'label'=>Mage::helper('orbaallegro')->__('Courier COD parcel')),
            array('value' => 46, 'label'=>Mage::helper('orbaallegro')->__('Pickpoint after prepaid - Ruch')),
            array('value' => 47, 'label'=>Mage::helper('orbaallegro')->__('Pickpoint after prepaid - Paczkomaty')),
            array('value' => 48, 'label'=>Mage::helper('orbaallegro')->__('Pickpoint - Ruch')),
            array('value' => 49, 'label'=>Mage::helper('orbaallegro')->__('Pickpoint - Paczkomaty')),
            array('value' => 50, 'label'=>Mage::helper('orbaallegro')->__('Pickpoint after prepaid - DHL')),
            array('value' => 51, 'label'=>Mage::helper('orbaallegro')->__('Pickpoint after prepaid - Paczka48')),
            array('value' => 52, 'label'=>Mage::helper('orbaallegro')->__('Pickpoint - Paczka48')),
            array('value' => 53, 'label'=>Mage::helper('orbaallegro')->__('Pocztex Kurier48')),
            array('value' => 54, 'label'=>Mage::helper('orbaallegro')->__('Pocztex Kurier48 COD')),
            array('value' => 55, 'label'=>Mage::helper('orbaallegro')->__('Paczka24')),
            array('value' => 56, 'label'=>Mage::helper('orbaallegro')->__('Paczka48')),
            array('value' => 57, 'label'=>Mage::helper('orbaallegro')->__('Pickpoint after prepaid - Paczka24 pickpoint')),
            array('value' => 58, 'label'=>Mage::helper('orbaallegro')->__('Pickpoint - Paczka24 Pickpoint')),
            );
    }

    /**
     * Returns array of Allegro ids for shipping methods
     *
     * @return array
     */
    public function getShippingIds()
    {
        return array(
            36,
            37,
            38,
            39,
            40,
            41,
            42,
            43,
            44,
            45,
            46,
            47,
            48,
            49,
            50,
            51,
            52,
            53,
            54,
            55,
            56,
            57,
            58,
        );
    }
}