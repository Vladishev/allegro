<?php

/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Allegro
 * @copyright  Copyright (c) 2016 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */
class Tim_Allegro_Model_Resource_Import extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize a resource model, set main table and ID field name
     */
    protected function _construct()
    {
        $this->_init('tim_allegro/import', 'id');
    }
}