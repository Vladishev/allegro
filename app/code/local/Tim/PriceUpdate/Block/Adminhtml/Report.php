<?php
/**
 * A grid container for a price update report
 *
 * @category  Tim
 * @package   Tim_PriceUpdate
 * @author    Oleksii Rybin <orybin@divante.pl>
 * @copyright 2014 Divante
 */
class Tim_PriceUpdate_Block_Adminhtml_Report extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Init grid container
     */
    public function __construct()
    {
        $this->_blockGroup = 'tim_priceupdate';
        $this->_controller = 'adminhtml_report';
        $this->_headerText = Mage::helper('tim_priceupdate')->__('Price Update Report');

        parent::__construct();
        $this->_removeButton('add');
    }
}