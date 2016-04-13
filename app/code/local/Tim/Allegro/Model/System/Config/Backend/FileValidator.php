<?php

/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Allegro
 * @copyright  Copyright (c) 2016 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */

class Tim_Allegro_Model_System_Config_Backend_FileValidator extends Mage_Adminhtml_Model_System_Config_Backend_File
{
    protected function _getAllowedExtensions() {
        return array('csv');
    }
}