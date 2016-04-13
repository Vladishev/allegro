<?php

/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Allegro
 * @copyright  Copyright (c) 2016 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */

class Tim_Allegro_Model_System_Config_Source_TaxList
{
    public function toOptionArray () {
        return array (
            array('value'=>0, 'label'=>Mage::helper('tim_allegro')->__('None')),
            array('value'=>2, 'label'=>Mage::helper('tim_allegro')->__('Taxable Goods')),
            array('value'=>4, 'label'=>Mage::helper('tim_allegro')->__('Shipping')),
        );
    }

}