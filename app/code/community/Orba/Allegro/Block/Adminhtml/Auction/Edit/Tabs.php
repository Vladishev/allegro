<?php
class Orba_Allegro_Block_Adminhtml_Auction_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    const EXTERNAL_CATEGORY_ID = 2;

    public function __construct()
    {
        parent::__construct();
        $this->setId('auction_info_tabs');
        $this->setDestElementId('auction_edit_form');
        $this->setTitle(Mage::helper('orbaallegro')->__(($this->getEditMode() ? 'Edit' : 'New') . ' Auction'));
    }
    
    protected function _prepareLayout()
    {
        $auction = $this->getAuction();
		$parentProductId = $this->getParentProductId();
        
        if($auction->getId()){
            $this->_handleEdit($auction);
            return;
        }

        // Step 1 - choose product
        if(is_null($productId = $this->getProduct()->getId())){
            $this->addTab('product_grid', array(
                'label'     => Mage::helper('orbaallegro')->__('Settings'),
                'content'   => $this->getLayout()
                    ->createBlock('orbaallegro/adminhtml_auction_edit_tab_product')
                    ->toHtml(),
                'active'    => true
            ));
            return parent::_prepareLayout();	
        // Step 2 - choose store view (only if not single store mode)
        }elseif(is_null($storeId=$this->getStore()->getId())){
            $this->addTab('settings', array(
                'label'     => Mage::helper('orbaallegro')->__('Settings'),
                'content'   => $this->getLayout()
                    ->createBlock('orbaallegro/adminhtml_auction_edit_tab_store')
                    ->setProduct($this->getProduct())
                    ->toHtml(),
                'active'    => true
            ));
            return parent::_prepareLayout();
		// Step 2a - choose parent product
		}elseif($this->getProduct()->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE
			&& is_null($parentProductId)) {
            $this->addTab('settings', array(
                'label'     => Mage::helper('orbaallegro')->__('Settings'),
                'content'   => $this->getLayout()
                    ->createBlock('orbaallegro/adminhtml_auction_edit_tab_product_configure')
					->setProduct($this->getProduct())
					->setStore($this->getStore())
					->toHtml(),
                'active'    => true
            ));
            return parent::_prepareLayout();
        // Step 3 - choose category (required) & template
        }elseif(is_null($this->getCategory()->getId())){
            $this->addTab('settings', array(
                'label'     => Mage::helper('orbaallegro')->__('Settings'),
                'content'   => $this->getLayout()
                    ->createBlock('orbaallegro/adminhtml_auction_edit_tab_settings')
                    ->setProduct($this->getProduct())
					->setParentProductId($parentProductId)
                    ->setStore($this->getStore())
                    ->toHtml(),
                'active'    => true
            ));
            return parent::_prepareLayout();
        }
        
        // Everything ok, lets start make final form
        $template = $this->getAucitonTemplate();
        
        $attributeSetId = $template->getAttributeSetId();
        if(!$attributeSetId){
            $attributeSetId = $template->getDefaultAttributeSetId();
        }
        
        $groupCollection = Mage::getResourceModel('eav/entity_attribute_group_collection')
            ->setAttributeSetFilter($attributeSetId)
            ->setSortOrder()
            ->load();

        // Get Allegro stuff

        //This 'if' statement for mass action only
        if (Mage::getSingleton('core/session')->getPostRequest() && ($this->getExtraDone() !== '1')) {
            $defaultIdSet = array_flip(Mage::getModel('orbaallegro/service_defaultSet')->getDefaultIds());
                Mage::register('allegro_category_id', $this->getCategory()->getId());
                Mage::register('allegro_shop_cat_id', $this->getShopCategory()->getId());
                Mage::register('allegro_template_id', $this->getAucitonTemplate()->getId());
                Mage::register('extra_ids', $defaultIdSet);

                $this->addTab('settings', array(
                    'label'     => Mage::helper('orbaallegro')->__('Extra fields'),
                    'content'   => $this->getLayout()
                        ->createBlock('orbaallegro/adminhtml_auction_edit_tab_extra')
                        ->setProduct($this->getProduct())
                        ->setParentProductId($parentProductId)
                        ->setCategory($this->getCategory())
                        ->setShopCategory($this->getShopCategory())
                        ->setStore($this->getStore())
                        ->setUser($this->getUser())
                        ->toHtml(),
                    'active'    => true
                ));
                return parent::_prepareLayout();
        }
        //This 'if' statement for mass action only
        if ($postRequest = Mage::getSingleton('core/session')->getPostRequest()) {
            foreach ($postRequest as $product) {
                $productModel = Mage::getModel('catalog/product')->load($product);
                Mage::unregister('product');
                Mage::register('product', $productModel);
                $flatNewBlock = $this->getLayout()
                    ->createBlock("orbaallegro/adminhtml_auction_edit_tab_flatnew")
                    ->setParentProductId($parentProductId)
                    ->setCategory($this->getCategory())
                    ->setShopCategory($this->getShopCategory())
                    ->setStore($this->getStore())
                    ->setUser($this->getUser());
                $auctionData = $flatNewBlock->getAuctionData();
                $flatFields = $auctionData->getFlatFields();
                $resultArray = array();
                $extraData = Mage::getSingleton('adminhtml/session')->getExtraData();
                if (!empty($extraData)) {
                    $resultArray['opt'] = $extraData['opt'];
                    unset($extraData['res']);
                    unset($extraData['opt']);
                    $flatFields = array_replace($flatFields, $extraData);
                }
                $resultArray['res'] = $auctionData->getResData();
                foreach ($flatFields as $key => $obj) {
                    if (is_object($obj)) {
                        $value = $obj->getValue();
                    } else {
                        $value = $obj;
                    }
                    if (is_array($value)) {
                        foreach ($value as $num => $val) {
                            if (empty($val)) {
                                $resultArray[$key] = $val;
                            } else {
                                $resultArray[$key] = $value;
                            }
                        }
                    } else {
                        $resultArray[$key] = $value;
                        if ($key == self::EXTERNAL_CATEGORY_ID) {
                            $resultArray[$key] = $this->getCategory()->getExternalId();
                        }
                    }
                }
                Mage::getModel('orbaallegro/auction_save')->save($resultArray);
            }
            Mage::getSingleton('core/session')->unsPostRequest();
            Mage::getSingleton('adminhtml/session')->unsExtraData();
            Mage::getSingleton('core/session')->addSuccess(Mage::helper('orbaallegro')->
            __("Auction created. Status will change immediately. You can refresh statuses manually."));

            return Mage::app()
                ->getResponse()
                ->setRedirect(
                    Mage::helper('adminhtml')
                        ->getUrl(
                            'adminhtml/auction/index',
                            array('_secure' => true)
                        )
                );
        }

        $this->addTab('flat', array(
            'label'     => Mage::helper('orbaallegro')->__("Auction content"),
            'content'   => $this->getLayout()->
                    createBlock("orbaallegro/adminhtml_auction_edit_tab_flatnew")->
					setParentProductId($parentProductId)->
                    setCategory($this->getCategory())->
                    setShopCategory($this->getShopCategory())->
                    setStore($this->getStore())->
                    setUser($this->getUser())->
                    toHtml()
        ));
		
//         //Make tempalte groups
//         foreach ($groupCollection as $group) {
//                $this->addTab('group_'.$group->getId(), array(
//                    'label'     => Mage::helper('orbaallegro')->__($group->getAttributeGroupName()),
//                    'content'   => $this->getLayout()->
//                            createBlock(
//                                "adminhtml/widget_form",
//                                'adminhtml.orbaallegro.trmplate.edit.tab.attributes'
//                            )->
//                                setGroup($group)->
//                                toHtml()
//                ));
//         }
        
        
    }
    
    protected function _handleEdit(Orba_Allegro_Model_Auction $auction) {
        $this->addTab('status', array(
            'label'     => Mage::helper('orbaallegro')->__("General informations"),
            'content'   => $this->getLayout()->
                    createBlock("orbaallegro/adminhtml_auction_edit_tab_status")->
                    setAuction($auction)->
                    toHtml()
        ));
        $this->addTab('transaction', array(
            'label'     => Mage::helper('orbaallegro')->__("Transactions"),
            'content'   => $this->getLayout()->
                    createBlock("orbaallegro/adminhtml_auction_edit_tab_transaction")->
                    setAuction($auction)->
                    toHtml()
        ));
        $this->addTab('bid', array(
            'label'     => Mage::helper('orbaallegro')->__("Offers"),
            'content'   => $this->getLayout()->
                    createBlock("orbaallegro/adminhtml_auction_edit_tab_bid")->
                    setAuction($auction)->
                    toHtml()
        ));
        $this->addTab('flat', array(
            'label'     => Mage::helper('orbaallegro')->__("Auction content"),
            'content'   => $this->getLayout()->
                    createBlock("orbaallegro/adminhtml_auction_edit_tab_flat")->
                    setEditMode($this->getEditMode())->
                    setCategory($this->getCategory())->
                    setShopCategory($this->getShopCategory())->
                    setStore($this->getStore())->
                    setUser($this->getUser())->
                    setAuction($this->getAuction())->
                    toHtml()
        ));
    }
    
    public function getEditMode() {
        return $this->getAuction()->getId();
    }
    
    /**
     * @return Orba_Allegro_Model_Auction
     */
    public function getAuction(){
        return Mage::registry('auction');
    }
    
    /**
     * @return Orba_Allegro_Model_Template
     */
    public function getAucitonTemplate() { // getTemplate() is reserved!
        return Mage::registry('template');
    }
    
    /**
     * @return Orba_Allegro_Model_Category
     */
    public function getCategory() {
        return Mage::registry('category');
    }
    
    /**
     * @return Orba_Allegro_Model_Shop_Category
     */
    public function getShopCategory() {
        return Mage::registry('shop_category');
    }
    
    /**
     * @return Mage_Core_Model_Product
     */
    public function getProduct() {
        return Mage::registry('product');
    }
    
    /**
     * @return Mage_Core_Model_Store
     */
    public function getStore() {
        return Mage::registry('store');
    }

    /**
     * @return Orba_Allegro_Model_User
     */
    public function getUser() {
        return Mage::registry('user');
    }
    /**
     * @return mixed
     */
    public function getExtraDone() {
        return Mage::registry('extra_done');
    }

    /**
     * @return Mage_Core_Model_Product
     */
    public function getParentProductId() {
        return Mage::registry('parent_product_id');
    }	
}

