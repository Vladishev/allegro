<?php

/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Allegro
 * @copyright  Copyright (c) 2016 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */
class Tim_Allegro_Model_Resource_Allegro_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize a collection, set model for it
     */
    protected function _construct()
    {
        $this->_init('tim_allegro/import');
    }
}