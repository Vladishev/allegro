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
    /**
     * Returns array with TAX classes
     * @return mixed
     */
    public function toOptionArray () {
        return Mage::getSingleton('tax/class_source_product')->toOptionArray();
    }

}