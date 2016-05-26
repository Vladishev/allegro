<?php

class Orba_Allegro_Model_Observer {
    // Event: core_block_abstract_prepare_layout_after
    // Area: ONLY Adminhtml
    public function handlePrepareLayoutAfter($data) {
        
        $block = $data->getBlock();
        $request = Mage::app()->getRequest();
        
        // Append button to edit panel
        if($block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit){
            /* @var $block Mage_Adminhtml_Block_Catalog_Product_Edit */
            
            $params = array();
            
            $product = Mage::registry('current_product');
            
            if($product && $product->getId()){         
                
                $params['product'] = $product->getId();
                
                if(!Mage::app()->isSingleStoreMode()){
                    $params['store'] = Mage::app()->getRequest()->getParam("store");
                }

                if($product->hasData(Orba_Allegro_Model_Mapping::ATTR_CODE_CATEGORY)){
                    $params['category'] = $product->getData(Orba_Allegro_Model_Mapping::ATTR_CODE_CATEGORY);
                }
                
                if($product->hasData(Orba_Allegro_Model_Mapping::ATTR_CODE_SHOP_CATEGORY)){
                    $params['shop_category'] = $product->getData(Orba_Allegro_Model_Mapping::ATTR_CODE_SHOP_CATEGORY);
                }
                
                if($product->hasData(Orba_Allegro_Model_Mapping::ATTR_CODE_TEMPLATE)){
                    $params['template'] = $product->getData(Orba_Allegro_Model_Mapping::ATTR_CODE_TEMPLATE);
                }
                
                $button = $block->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                        'label'     => Mage::helper('orbaallegro')->__('Create auction'),
                        'onclick'   => "setLocation('".$block->getUrl('*/auction/new', $params)."')"
                    ));

                // Append button to delete button (product has id)
                //$block->getChild('delete_button')->setAfterHtml($button->toHtml());
            }
            return;
        }
        
        // Append auction action
        /* @todo Add category and template */
        if($block instanceof Mage_Adminhtml_Block_Catalog_Product_Grid){
            /* @var $block Mage_Adminhtml_Block_Catalog_Product_Grid */
            $block->addColumnAfter("orbaallegro_auctions", array(
                "header" => Mage::helper('orbaallegro')->__("Auction"),
                "width" => "100px",
                "sortable" => false,
                "filter" => false,
                "renderer" => Mage::getConfig()->
                    getBlockClassName("orbaallegro/adminhtml_catalog_product_grid_column_renderer_auction")
            ), "action");
            $block->addColumnAfter("orbaallegro_auction_count", array(
                "header" => Mage::helper('orbaallegro')->__("Placed auction<br/>count"),
                "width" => "100px",
                "index" => "auction_count",
                "type"  => "number",
                'sort_callback' => array(
                        $this,
                        '_filterAllegroAuctionCount'
                 ),
            ), "qty");
            return;
        }
        
    }
    
    public function sortAllegroAuctionCount($collection, $column){
        die("TAK");
//        $value = $column->getFilter()->getValue();
//        if ($value && !empty($value)) {
//            $resource = Mage::getSingleton('core/resource');
//            $collection->getSelect()->joinLeft( 
//                array('shop_category'=>$resource->getTableName('orbaallegro/shop_category')), 
//                'main_table.entity_id_2 = shop_category.category_id', 
//                array('shop_category' => 'shop_category.name')
//            );
//            $collection->addFieldToFilter('shop_category.name', array('like' => '%'.$value.'%'));
//        }
    }
    
    // Event: catalog_product_load_after
    public function handleCatalogProductLoadAfter($data) {
        
        if(!Mage::app()->getStore()->isAdmin()){
            return;
        }
        
        $mapping = Mage::getSingleton("orbaallegro/mapping");
        /* @var $mapping Orba_Allegro_Model_Mapping */
        $product = $data->getDataObject();
        /* @var $product Mage_Catalog_Model_Product */
        
        $mapping->validTempalteId($product);
        $mapping->validCategoryId($product);
        $mapping->validShopCategoryId($product);
        
    }
    
    public function handleCatalogProductCollectionLoadBefore($data) {
        
        $collection = $data->getCollection();
        /* @var $collection Mage_Catalog_Model_Resource_Product_Collection */
        $collection->addAttributeToSelect(array(
            Orba_Allegro_Model_Mapping::ATTR_CODE_CATEGORY,
            Orba_Allegro_Model_Mapping::ATTR_CODE_SHOP_CATEGORY,
            Orba_Allegro_Model_Mapping::ATTR_CODE_TEMPLATE,
        ));
        
        Mage::getResourceModel("orbaallegro/auction")->
                addAuctionCountToProductCollection($collection, true);
    }
    
    public function verifyAucitons() {
        $collection = Mage::getResourceModel("orbaallegro/auction_collection");
        /* @var $collection Orba_Allegro_Model_Resource_Auction_Collection */
        $collection->addToVerfiyFilter();
        
        foreach ($collection as $auction){
            /* @var $auction  Orba_Allegro_Model_Auction */
            $auction->allegroVerify();
        }
    }
    
    public function checkPlacedAuctions() {
        $collection = Mage::getResourceModel("orbaallegro/auction_collection");
        /* @var $collection Orba_Allegro_Model_Resource_Auction_Collection */
        $collection->addPlacedFilter();
        foreach ($collection as $auction){
            /* @var $auction  Orba_Allegro_Model_Auction */
            // Check all things
            // @todo optimalize via multi-item requests
            $auction->allegroCheckStatus();
            $auction->allegroSyncAuctionContractors();
            $auction->allegroSyncAuctionBids();
            $auction->allegroSyncTransactions();
        }
        
    }
    
    public function checkPlacedAuctionTransactions(&$newTransactionCount=null) {
        $collection = Mage::getResourceModel("orbaallegro/auction_collection");
        /* @var $collection Orba_Allegro_Model_Resource_Auction_Collection */
        $collection->addPlacedOrJustClosedFilter();
        
        foreach ($collection as $auction){
            /* @var $auction  Orba_Allegro_Model_Auction */
            // Transaction check
            $auction->allegroSyncTransactions($newTransactionCount);
        }
    }
    
    public function syncPlacedAuctionBids(&$newBidsCount=null) {
        $collection = Mage::getResourceModel("orbaallegro/auction_collection");
        /* @var $collection Orba_Allegro_Model_Resource_Auction_Collection */
        $collection->addPlacedOrJustClosedFilter();
        
        foreach ($collection as $auction){
            /* @var $auction  Orba_Allegro_Model_Auction */
            // Transaction check
            $auction->allegroSyncAuctionBids($newBidsCount);
        }
    }
    
    public function syncPlacedAuctionContractors() {
        $collection = Mage::getResourceModel("orbaallegro/auction_collection");
        /* @var $collection Orba_Allegro_Model_Resource_Auction_Collection */
        $collection->addPlacedOrJustClosedFilter();
        
        foreach ($collection as $auction){
            /* @var $auction  Orba_Allegro_Model_Auction */
            // Add contractor data
            $auction->allegroSyncAuctionContractors();
        }
    }
    
    // Add a special shipping info of pickpoint as comment
    public function handleSalesOrderSaveBefore($observer){
         $order = $observer->getEvent()->getOrder();
         /* @var $order Mage_Sales_Model_Order */
         if(!$order->getId() || $order->isObjectNew()){
            if($order->getOrbaallegroTransactionId()){
                $order->setTransaferTransactionId(true);
            }
            if($order->getOrbaallegroContractorId()){
                $order->setAddAllegroLoginComment(true);
            }
            if($order->getOrbaallegroShipmentId()){
                $shipment = Mage::getModel("orbaallegro/mapping_shipment");
                /* @var $shipment Orba_Allegro_Model_Mapping_Shipment */
                $shipment = $shipment->load($order->getOrbaallegroShipmentId());
                if($shipment->getId()){
                    if($shipment->getIsPickpoint()){
                        // Add shipment info
                        $order->setCustomerNote($shipment->getName());
                    }
                }

            }
         }
    }
    
    // Add order id to transaction
    public function handleSalesOrderSaveAfter($observer) {
        $order = $observer->getEvent()->getOrder();
        /* @var $order Mage_Sales_Model_Order */
        
        // Set transaciton ID
        if($order->getTransaferTransactionId() && $order->getOrbaallegroTransactionId()){
            $transaction = Mage::getModel("orbaallegro/transaction")->
                    load($order->getOrbaallegroTransactionId());
            /* @var $transaction Orba_Allegro_Model_Transaction */
            if($transaction->getId()){
                $transaction->setOrderId($order->getId());
                $transaction->save();
                $order->setTransaferTransactionId(null);
            }
        }
        
        // Add info comment
        if($order->getAddAllegroLoginComment() && $order->getOrbaallegroContractorId()){
            $contractor = Mage::getModel("orbaallegro/contractor")->
                load($order->getOrbaallegroContractorId());
            
            if($contractor->getId() && $contractor->getLogin()){
                $order->addStatusHistoryComment(Mage::helper("orbaallegro")->__(
                    "Order created via ORBA | Allegro. Auction client is %s.", 
                    $contractor->getLogin())
                );
            }
            $order->setAddAllegroLoginComment(null);
        }
    }
	
	/**
	 * Auto renew aucitons
	 */
	public function doRenewAuctions() {
        $collection = Mage::getResourceModel("orbaallegro/auction_collection");
        /* @var $collection Orba_Allegro_Model_Resource_Auction_Collection */
        $collection->addDoRenewFilter();
		$toDisableIds = array();
		try{
			foreach ($collection as $auction){
				/* @var $auction  Orba_Allegro_Model_Auction */
				// Stack to disable ids
				$oldId = $auction->get();
				// Set new items qty
				if($auction->getRenewItems()){
					$auction->setItemsPlaced($auction->getRenewItems());
					$auction->allegroRenewAuction();
					$toDisableIds[] = $oldId;
				}				
			}
		}catch(Exception $e){
			Mage::logException($e);
		}
		
		if($toDisableIds){
			$collection->getResource()->disableRenew($toDisableIds);
		}
	}

    public function addMassAction($observer)
    {
        $block = $observer->getEvent()->getBlock();
        if(get_class($block) =='Mage_Adminhtml_Block_Widget_Grid_Massaction'
            && $block->getRequest()->getControllerName() == 'catalog_product')
        {
            $block->addItem('new_auction', array(
                'label' => 'New Auction',
                'url' => Mage::app()->getStore()->getUrl('*/auction/new'),
            ));
        }
    }
}