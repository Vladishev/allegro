<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Allegro
 * @copyright  Copyright (c) 2016 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */
class Tim_Allegro_Adminhtml_ProductDescriptionController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Start report create action
     */
    public function indexAction()
    {
        $this->_title(Mage::helper('tim_allegro')->__('Products without description'));
        $this->loadLayout();
        $this->_setActiveMenu('report');
        $this->_addContent($this->getLayout()->createBlock('tim_allegro/adminhtml_productDescriptionReport'));
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
            $this->getLayout()->createBlock('tim_allegro/adminhtml_productDescriptionReport_grid')->toHtml()
        );
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('report/tim/tim_product_description_report');
    }
}