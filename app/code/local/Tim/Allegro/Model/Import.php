<?php

/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Allegro
 * @copyright  Copyright (c) 2016 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */
class Tim_Allegro_Model_Import extends Mage_Core_Model_Abstract
{
    /**
     * Initialize import model, set resource model for it
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('tim_allegro/import');
    }
}