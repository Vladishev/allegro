<?php

/**
 * Tim
 *
 * @category  Tim
 * @package   Tim_Allegro
 * @copyright Copyright (c) 2016 Tim (http://tim.pl)
 * @author    Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Allegro_Block_Adminhtml_ProductNameReport extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'tim_allegro';
        $this->_controller = 'adminhtml_productNameReport';
        $this->_headerText = Mage::helper('tim_allegro')->__('Products where name > 50 chars');

        parent::__construct();
        $this->_removeButton('add');
    }
}
