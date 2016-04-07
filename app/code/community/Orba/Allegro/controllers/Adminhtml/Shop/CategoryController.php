<?php
class Orba_Allegro_Adminhtml_Shop_CategoryController extends Orba_Allegro_Controller_Adminhtml_Abstract {
	
    public function indexAction() {
        if(!is_null($msg=$this->_stopProcess())){
            $this->_getSession()->addError($msg);
            return $this->_redirectReferer();
        }
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function syncAction() {
        if(!is_null($msg=$this->_stopProcess())){
            $this->_getSession()->addError($msg);
            return $this->_redirectReferer();
        }
        $result = Mage::getSingleton('orbaallegro/shop_category')->doImport();
        if ($result) {
            if (!empty($result)) {
                $message = Mage::helper('orbaallegro')->__('Shop categories synchronization is finished.');
                $_service = Mage::getSingleton('orbaallegro/service');
                foreach ($result as $countryId => $res) {
                    $message .= '<br />' . $_service->getLabelByCountryId($countryId) . ': ' . Mage::helper('orbaallegro')->__('Created: %s. Updated: %s. Deleted: %s.', $res['created'], $res['updated'], $res['deleted']);
                }
                Mage::getSingleton('adminhtml/session')->addSuccess($message);
            } else {
                Mage::getSingleton('adminhtml/session')->addWarning(Mage::helper('orbaallegro')->__('There are no services checked for categories synchronization in the extension config.'));
            }
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('orbaallegro')->__('Unable to synchronize categories.'));
        }
        $this->_redirectReferer();
    }

    public function jsonChildrenAction() {
        $params = $this->getRequest()->getParams();
        $parent_id = isset($params['id']) ? $params['id'] : 0;
        $country_id = isset($params['country_id']) ? $params['country_id'] : false;
        
        $request = Mage::app()->getRequest();
        $store = $request->getParam("store");
        $website = $request->getParam("website");
        $useExternal = $request->getParam("use_external", false);
        
        $login = Mage::getSingleton('orbaallegro/config')->getLogin($store, $website);
        $res = Mage::getSingleton('orbaallegro/shop_category')->getChildren($parent_id, $country_id, $login, $useExternal);
        $json = json_encode($res);
        $this->getResponse()->setBody($json);
    }
    
}