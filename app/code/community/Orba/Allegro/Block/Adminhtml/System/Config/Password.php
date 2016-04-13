<?php

/**
 * Description of Service
 */
class Orba_Allegro_Block_Adminhtml_System_Config_Password 
    extends Mage_Adminhtml_Block_System_Config_Form_Field {
    
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $request = $this->getRequest();
        $store = $request->getParam('store');
        $website = $request->getParam('website');
        
        $json = new stdClass;
        
        if($website!==null){
            $json->website = $website;
        }
        if($store!==null){
            $json->store = $store;
        }
        
        $config = Mage::getModel("orbaallegro/config");
        /* @var $config Orba_Allegro_Model_Config */
        
        $loginData = $config->getLoginData($store, $website);
        
        //    $result['content'] = $helper->__("Wrong login data");
        
        $isFirsImportComplete = Mage::helper("orbaallegro")->isFirstImportComplete();
        
        $canLogin = Mage::helper("orbaallegro")->canLogin($store, $website, $loginData);
        
        $block = $this->getLayout()->
                createBlock("core/template")->
                setTemplate("orbaallegro/system/config/password.phtml")->
                setElementId($element->getHtmlId())->
                setLoginAndImportUrl(Mage::getUrl("*/system_config/loginAndImport"))->
                setLoginUrl(Mage::getUrl("*/system_config/login"))->
                setSandboxUrl(Mage::getUrl("*/system_config/saveSandbox"))->
                setIsFirstImportComplete($isFirsImportComplete)->
                setCanLogin($canLogin)->
                setJsonParmas(Zend_Json::encode($json));
        
        return parent::_getElementHtml($element) . $block->toHtml();
    }
}

?>
