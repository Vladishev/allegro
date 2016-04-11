<?php

class Orba_Allegro_Model_Template_Filter extends Mage_Cms_Model_Template_Filter {

    /**
     * @var Mage_Catalog_Model_Product
     */
    protected $_product;
	
    /**
     * @var Mage_Catalog_Model_Product
     */
    protected $_parentProduct = null;

    /**
     *
     * @var Mage_Core_Model_Layout
     */
    protected $_layout;

    /**
     * @var Orba_Allegro_Model_User
     */
    protected $_user;

    /**
     * @var array
     */
    protected $_blocks = array();

    /**
     * Override desigin area - redering fronted from backend
     * @param string $value
     * @return string
     */
    public function filter($value) {
        $oldStore = Mage::getDesign()->getStore();
        $oldArea = Mage::getDesign()->getArea();
        $oldAppStore = Mage::app()->getStore();
        
        Mage::getDesign()->setStore($this->getStore())->setArea("frontend");
        Mage::app()->setCurrentStore($this->getStore());
        
        $this->_prepareVariables();
        $outPut = parent::filter($value);
        
        Mage::app()->setCurrentStore($oldAppStore);
        Mage::getDesign()->setStore($oldStore)->setArea($oldArea);
        
        return $outPut;
    }

    /**
     * @param Mage_Catalog_Model_Product $prodcut
     * @return Orba_Allegro_Model_Template_Filter
     */
    public function setProduct(Mage_Catalog_Model_Product $prodcut) {
        $this->_product = $prodcut;
        return $this;
    }

    /**
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct() {
		$productId = Mage::registry('product_id');
		$parentProductId = Mage::registry('parent_product_id');
		
		if ($productId && $parentProductId) {
			$productModel = Mage::getModel('catalog/product');
			$product = $productModel->load($productId);
			$this->setProduct($product);
		}		
        return $this->_product;
    }
	
    /**
     * @param Mage_Catalog_Model_Product $prodcut
	 * 
     * @return Mage_Catalog_Model_Product
     */
    public function setParentProduct() {
		$productId = Mage::registry('product_id');
		$parentProductId = Mage::registry('parent_product_id');
		
		if ($productId && $parentProductId) {
			$productModel = Mage::getModel('catalog/product');
			$product = $productModel->load($productId);
			$this->setProduct($product);
			$this->_parentProduct = $productModel->load($parentProductId);
		}
			
        return $this->_parentProduct;
    }
	
    /**
     * @return Mage_Catalog_Model_Product
     */
    public function getParentProduct() {
		if (is_null($this->_parentProduct)) {
			$this->setParentProduct();
		}
        return $this->_parentProduct;
    }

    /**
     * @param Orba_Allegro_Model_User $user
     * @return Orba_Allegro_Model_Template_Filter
     */
    public function setUser(Orba_Allegro_Model_User $user) {
        $this->_user = $user;
        return $this;
    }

    /**
     * @return Orba_Allegro_Model_User
     */
    public function getUser() {
        return $this->_user;
    }

    /**
     * @throws Orba_Allegro_Exception
     */
    protected function _prepareVariables() {
		$parentProduct = $this->getParentProduct();
        $product = $this->getProduct();
        if (!$product || !$product->getId()) {
            throw new Orba_Allegro_Exception("No product passed to filter");
        }

        $user = $this->getUser();
        if (!$user) {
            throw new Orba_Allegro_Exception("No user passed to filter");
        }

        $service = $this->_getService();

        /**
         * Objects
         */
        if (!isset($this->_templateVars['user'])) {
            $this->_templateVars['user'] = $user;
        }
        if (!isset($this->_templateVars['product'])) {
            $this->_templateVars['product'] = $product;
        }
        if (!isset($this->_templateVars['parent'])) {
            $this->_templateVars['parent'] = $parentProduct;
        }		
        if (!isset($this->_templateVars['youtube'])) {
            $this->_templateVars['youtube'] = $this->_getYoutubeBlock()->toHtml();
        }
        if (!isset($this->_templateVars['attributes'])) {
            $this->_templateVars['attributes'] = $this->_getAttributesBlock()->toHtml();
        }
        if (!isset($this->_templateVars['gallery'])) {
            $this->_templateVars['gallery'] = $this->_getGalleryBlock()->toHtml();
        }

        /**
         * Links
         */
        $userId = $user->getUserId();

        if (!isset($this->_templateVars['auction_list_link'])) {
            $this->_templateVars['auction_list_link'] = $service->getAucitonListLink($userId);
        }

        if (!isset($this->_templateVars['about_link'])) {
            $this->_templateVars['about_link'] = $service->getAboutLink($userId);
        }

        if (!isset($this->_templateVars['comments_link'])) {
            $this->_templateVars['comments_link'] = $service->getCommentsLink($userId);
        }

        if (!isset($this->_templateVars['add_favourites_link'])) {
            $this->_templateVars['add_favourites_link'] = $service->getAddFavouritesLink($userId);
        }

        if (!isset($this->_templateVars['contact_link'])) {
            $this->_templateVars['contact_link'] = $service->getContactLink($userId);
        }
        if (!isset($this->_templateVars['shop_link'])) {
            if ($user->getUserHasShop()) {
                $this->_templateVars['shop_link'] = $service->getShopLink($userId);
            }
        }


        /**
         * Resource Urls
         */
        if (!isset($this->_templateVars['product_photo_url'])) {
			if ($this->_getGalleryBlock()->getImageUrl()) {
				$this->_templateVars['product_photo_url'] = $this->_getGalleryBlock()->getImageUrl();
			} else {
				$this->_templateVars['product_photo_url'] = $this->_getGalleryBlock()->getParentImage();
			}
        }
        if (!isset($this->_templateVars['logo_url'])) {
            $this->_templateVars['logo_url'] = Mage::getDesign()->getSkinUrl(
                    $this->getStore()->getConfig('design/header/logo_src')
            );
        }
        if (!isset($this->_templateVars['template_url'])) {
            $this->_templateVars['template_url'] = Mage::getDesign()->getSkinUrl("orbaallegro");
        }
        if (!isset($this->_templateVars['youtube_url'])) {
            $this->_templateVars['youtube_url'] = $this->_getYoutubeBlock()->getYoutubeUrl();
        }
    }

    /**
     * @return Orba_Allegro_Block_Template_Attributes
     */
    protected function _getAttributesBlock() {
        if (!isset($this->_blocks["attributes"])) {
            $this->_blocks["attributes"] = $this->_getLayout()->
                    createBlock("orbaallegro/template_attributes")->
                    setTemplate("orbaallegro/template/attributes.phtml")->
                    setProduct($this->getProduct());
        }
        return $this->_blocks["attributes"];
    }

    /**
     * @return Orba_Allegro_Block_Template_Youtube
     */
    protected function _getYoutubeBlock() {
        if (!isset($this->_blocks["youtube"])) {
            $this->_blocks["youtube"] = $this->_getLayout()->
                    createBlock("orbaallegro/template_youtube")->
                    setTemplate("orbaallegro/template/youtube.phtml")->
                    setProduct($this->getProduct());
        }
        return $this->_blocks["youtube"];
    }

    /**
     * @return Orba_Allegro_Block_Template_Gallery
     */
    protected function _getGalleryBlock() {
        if (!isset($this->_blocks["gallery"])) {
            $this->_blocks["gallery"] = $this->_getLayout()->
                    createBlock("orbaallegro/template_gallery")->
                    setTemplate("orbaallegro/template/gallery.phtml")->
                    setProduct($this->getProduct());
        }
        return $this->_blocks["gallery"];
    }

    /**
     * @return Mage_Core_Model_Layout
     */
    protected function _getLayout() {
        if (!$this->_layout) {
            $this->_layout = Mage::getModel("core/layout")->setArea("frontend");
        }
        return $this->_layout;
    }

    /**
     * @return Orba_Allegro_Model_Service_Abstract
     */
    protected function _getService() {

        $storeId = $this->getStoreId();

        $config = Mage::getSingleton('orbaallegro/config');
        /* @var $config Orba_Allegro_Model_Config */

        $countryCode = $config->getCountryCode($storeId);

        $serviceFactory = Mage::getSingleton("orbaallegro/service");
        /* @var $serviceFactory Orba_Allegro_Model_Service */

        return $serviceFactory::factory($countryCode);
    }

    /**
     * @return Mage_Core_Model_Store
     */
    public function getStore() {
        return Mage::app()->getStore($this->getStoreId());
    }

}
