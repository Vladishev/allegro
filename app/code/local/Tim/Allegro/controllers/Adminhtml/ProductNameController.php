<?php
/**
 * Tim
 *
 * @category  Tim
 * @package   Tim_Allegro
 * @copyright Copyright (c) 2016 Tim (http://tim.pl)
 * @author    Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Allegro_Adminhtml_ProductNameController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Start order create action
     */
    public function indexAction()
    {
        $this->_title(Mage::helper('tim_allegro')->__('Products(name > 50 chars)'));
        $this->loadLayout();
        $this->_setActiveMenu('report');
        $this->_addContent($this->getLayout()->createBlock('tim_allegro/adminhtml_productNameReport'));
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
            $this->getLayout()->createBlock('tim_allegro/adminhtml_productNameReport_grid')->toHtml()
        );
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('report/tim/tim_product_name_report');
    }
}
