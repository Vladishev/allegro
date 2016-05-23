<?php
/**
 * An adminhtml controller for a price update report
 *
 * @category  Tim
 * @package   Tim_PriceUpdate
 * @author    Oleksii Rybin <orybin@divante.pl>
 * @copyright 2014 Divante
 */
class Tim_PriceUpdate_Adminhtml_PriceUpdateController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Shows "Price Update Report" page
     *
     * @return null
     */
    public function indexAction()
    {
        $this->_title($this->__('Price Update Report'));
        $this->loadLayout();
        $this->_setActiveMenu('report/products');
        $this->_addContent($this->getLayout()->createBlock('tim_priceupdate/adminhtml_report'));
        $this->renderLayout();
    }

    /**
     * Grid action
     *
     * @return null
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('tim_priceupdate/adminhtml_report_grid')->toHtml()
        );
    }
}