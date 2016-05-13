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
            array('value' => 1, 'label'=>Mage::helper('orbaallegro')->__('Economic postal parcel')),
            array('value' => 2, 'label'=>Mage::helper('orbaallegro')->__('Economic postal letter')),
            array('value' => 3, 'label'=>Mage::helper('orbaallegro')->__('Priority postal parcel')),
            array('value' => 4, 'label'=>Mage::helper('orbaallegro')->__('Priority postal letter')),
            array('value' => 6, 'label'=>Mage::helper('orbaallegro')->__('Paczka48 COD')),
            array('value' => 7, 'label'=>Mage::helper('orbaallegro')->__('Economic registered letter')),
            array('value' => 8, 'label'=>Mage::helper('orbaallegro')->__('Paczka24 COD')),
            array('value' => 9, 'label'=>Mage::helper('orbaallegro')->__('Priority registered letter')),
            array('value' => 10, 'label'=>Mage::helper('orbaallegro')->__('Courier parcel')),
            array('value' => 11, 'label'=>Mage::helper('orbaallegro')->__('Courier COD parcel')),
            array('value' => 12, 'label'=>Mage::helper('orbaallegro')->__('Pickpoint after prepaid - Ruch')),
            array('value' => 13, 'label'=>Mage::helper('orbaallegro')->__('Pickpoint after prepaid - Paczkomaty')),
            array('value' => 14, 'label'=>Mage::helper('orbaallegro')->__('Pickpoint - Ruch')),
            array('value' => 15, 'label'=>Mage::helper('orbaallegro')->__('Pickpoint - Paczkomaty')),
            array('value' => 16, 'label'=>Mage::helper('orbaallegro')->__('Pickpoint after prepaid - DHL')),
            array('value' => 17, 'label'=>Mage::helper('orbaallegro')->__('Pickpoint after prepaid - Paczka48')),
            array('value' => 18, 'label'=>Mage::helper('orbaallegro')->__('Pickpoint - Paczka48')),
            array('value' => 19, 'label'=>Mage::helper('orbaallegro')->__('Pocztex Kurier48')),
            array('value' => 20, 'label'=>Mage::helper('orbaallegro')->__('Pocztex Kurier48 COD')),
            array('value' => 21, 'label'=>Mage::helper('orbaallegro')->__('Paczka24')),
            array('value' => 22, 'label'=>Mage::helper('orbaallegro')->__('Paczka48')),
            array('value' => 23, 'label'=>Mage::helper('orbaallegro')->__('Pickpoint after prepaid - Paczka24 pickpoint')),
            array('value' => 24, 'label'=>Mage::helper('orbaallegro')->__('Pickpoint - Paczka24 Pickpoint')),
            );
    }
}