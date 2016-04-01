<?php

/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Allegro
 * @copyright  Copyright (c) 2016 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */
class Tim_Allegro_Block_Adminhtml_ProductImageReport extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'tim_allegro';
        $this->_controller = 'adminhtml_productImageReport';
        $this->_headerText = Mage::helper('tim_allegro')->__('Products without images');

        parent::__construct();
        $this->_removeButton('add');
    }
}
