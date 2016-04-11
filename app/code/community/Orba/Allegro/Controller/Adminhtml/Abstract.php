<?php

abstract class Orba_Allegro_Controller_Adminhtml_Abstract extends Mage_Adminhtml_Controller_Action
{

    protected function _sendJson($data)
    {
        return $this->getResponse()->
                setHeader("Content-type", "application/json")->
                setBody(Zend_Json::encode($data));
    }

    public function wysiwygAction()
    {
        $elementId = $this->getRequest()->getParam('element_id', md5(microtime()));
        $storeId = $this->getRequest()->getParam('store_id', 0);
        $storeMediaUrl = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);

        $content = $this->getLayout()->createBlock('orbaallegro/adminhtml_template_helper_form_wysiwyg_content', '', array(
            'editor_element_id' => $elementId,
            'store_id' => $storeId,
            'store_media_url' => $storeMediaUrl,
        ));
        $this->getResponse()->setBody($content->toHtml());
    }

    protected function _getConfigLink()
    {
        return $this->getUrl("*/system_config/edit", array("section" => "orbaallegro"));
    }

    protected function _stopProcess($checkLogin = false, $storeId = null)
    {
        $helper = Mage::helper('orbaallegro');
        /* @var $helper Orba_Allegro_Helper_Data */

        if (!$helper->isFirstImportComplete()) {
            return Mage::helper('orbaallegro')->__(
                    "Configuration is incomplte. " .
                    "Login to Allegro service " .
                    "<a href=\"{$this->_getConfigLink()}\">here</a>", $this->_getConfigLink());
        }

        $config = Mage::getModel("orbaallegro/config");
        /* @var $config Orba_Allegro_Model_Config */

        if ($checkLogin && is_null($storeId)) {
            Mage::helper('orbaallegro')->__("No store specified");
        }

        if ($checkLogin && !$helper->canLogin($storeId)) {
            $config = Mage::getModel("orbaallegro/config");
            /* @var $config Orba_Allegro_Model_Config */

            return Mage::helper('orbaallegro')->__(
                    "Wrong login data (depend on store view). " .
                    "Login to Allegro service (%s)" .
                    "<a href=\"%s\">here</a>.", $this->_getConfigLink(), $config->getCountryCode($storeId)
            );
        }

        return null;
    }

    /**
     * Catch api errors in blocks
     * 
     * @param array $ids
     * @param boolean $generateBlocks
     * @param boolean $generateXml
     * @return \Orba_Allegro_Controller_Adminhtml_Abstract
     */
    public function loadLayout($ids = null, $generateBlocks = true, $generateXml = true)
    {
        try {
            parent::loadLayout($ids, $generateBlocks, $generateXml);
        } catch (Orba_Allegro_Model_Client_Exception $ex) {
            $this->_getSession()->addError($ex->getMessage());
            return $this->_redirectReferer();
        }
        return $this;
    }
}
