<?php

/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Allegro
 * @copyright  Copyright (c) 2016 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */
class Tim_Allegro_Model_SendReports extends Mage_Core_Model_Abstract
{
    /**
     * Initializes report sending
     */
    public function sendReport()
    {
        $recipients = explode(',', Mage::getStoreConfig('tim_reports_settings/tim_reports_group/tim_reports_to'));
        $recipients = array_map('trim', $recipients);
        $templateVar = $this->_getReportInformation();

        if (!empty($recipients[0])) {
            foreach ($recipients as $recipient) {
                $this->_sendEmail($recipient, $templateVar);
            }
        }
    }

    /**
     * Sends emails
     * @param $recipient
     * @param $templateVar
     */
    protected function _sendEmail($recipient, $templateVar)
    {
        $templateId = 'tim_products_report';
        $subject = 'Products report';

        $emailTemplate = Mage::getModel('core/email_template')->loadDefault($templateId);
        $processedTemplate = $emailTemplate->getProcessedTemplate($templateVar);
        Mage::getModel('core/email')
            ->setToEmail($recipient)
            ->setBody($processedTemplate)
            ->setSubject(Mage::helper('tim_allegro')->__($subject))
            ->setFromName(Mage::getStoreConfig('trans_email/ident_general/name'))
            ->setType('html')
            ->send();
    }

    /**
     * Prepares array for report
     * @return array
     */
    protected function _getReportInformation()
    {
        $reportInformation = array();
        $reportInformation['longName'] = $this->_getLongNameProducts();
        $reportInformation['noImages'] = $this->_getNoImagesProducts();
        $reportInformation['noDescription'] = $this->_getNoDescriptionProducts();

        return $reportInformation;
    }

    /**
     * Prepares count of products with names longer then 50 characters
     * @return array
     */
    protected function _getLongNameProducts()
    {
        $names = Mage::getModel('catalog/product')
            ->getCollection()
            ->addExpressionAttributeToSelect('length_prod_name', 'length({{name}})', array('name'))
            ->addAttributeToFilter('length_prod_name', array('gt' => 50))
            ->getData();

        return count($names);
    }

    /**
     * Prepares count of products without images
     * @return array
     */
    protected function _getNoImagesProducts()
    {
        $noImages = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect(array('image', 'thumbnail', 'small_image'))
            ->addAttributeToFilter(array(
            array (
                'attribute' => 'image',
                'like' => 'no_selection'
            ),
            array (
                'attribute' => 'image', // null fields
                'null' => true
            ),
            array (
                'attribute' => 'image', // empty, but not null
                'eq' => ''
            ),
            array (
                'attribute' => 'image', // check for information that doesn't conform to Magento's formatting
                'nlike' => '%/%/%'
            ),
            ))
            ->addAttributeToFilter(array(
            array (
                'attribute' => 'thumbnail',
                'like' => 'no_selection'
            ),
            array (
                'attribute' => 'thumbnail', // null fields
                'null' => true
            ),
            array (
                'attribute' => 'thumbnail', // empty, but not null
                'eq' => ''
            ),
            array (
                'attribute' => 'thumbnail', // check for information that doesn't conform to Magento's formatting
                'nlike' => '%/%/%'
            ),
            ))
            ->addAttributeToFilter(array(
            array (
                'attribute' => 'small_image',
                'like' => 'no_selection'
            ),
            array (
                'attribute' => 'small_image', // null fields
                'null' => true
            ),
            array (
                'attribute' => 'small_image', // empty, but not null
                'eq' => ''
            ),
            array (
                'attribute' => 'small_image', // check for information that doesn't conform to Magento's formatting
                'nlike' => '%/%/%'
            ),
            ))
            ->getData();

        return count($noImages);
    }

    /**
     * Prepares count of products without description
     * @return array
     */
    protected function _getNoDescriptionProducts()
    {
        $noDescription = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect('name', 'description')
            ->addAttributeToFilter(array(
            array(
                'attribute' => 'description',
                'null' => true
            ),
            array(
                'attribute' => 'description',
                'eq' => ''
            ),
            ))
            ->getData();

        return count($noDescription);
    }
}