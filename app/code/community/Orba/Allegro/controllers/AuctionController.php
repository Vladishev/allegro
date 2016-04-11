<?php
class Orba_Allegro_AuctionController extends Mage_Core_Controller_Front_Action{
    /**
     * Image proxy;
     * @todo improve performance
     */
     public function getImageAction() {
         $request = $this->getRequest();
         $productId = $request->getParam("product_id");
         $imageId = $request->getParam("image_id");

         if($productId){
            $product = Mage::getModel("catalog/product")->load($productId);
            if($product->getId()){
                
                /* @var $product Mage_Catalog_Model_Product */
                $mediaGallery = $product->getMediaGalleryImages();
                $found = false;
                $image = null;
                // Found some image
                
                if($imageId){
                    foreach($mediaGallery as $image){
                        if($image->getId()==$imageId){
                            $found = true;
                            break;
                        }
                    }
                }
                // If found use it
                if($found){
                    $redirectUrl = $this->_resize($product, "image", 
							$this->getBlockWidth(), $this->getBlockHeight(), 
							$image, true, $this->getKeepFrame());
                }else{
                    $redirectUrl = $this->_resize($product, "image", 
							$this->getBlockWidth(), $this->getBlockHeight(), 
							null, true, $this->getKeepFrame());
                }
				
                //return $this->_redirectUrl($redirectUrl);
				
				$filePath = substr($redirectUrl, strpos($redirectUrl, "//")+2);
				$filePath = substr($filePath, strpos($filePath, "/") + 1);
				$filePath = str_replace("/", DS, $filePath);
				
				if(file_exists($filePath)){
					$this->getResponse()->
							setHeader("content-type", "image/jpeg")->
							setBody(file_get_contents($filePath));
					return;
				}
				
            }
         }
         return $this->_redirect("no-route");
     }
     
    public function getBlockWidth(){
        return Mage::helper('orbaallegro')->getBlockWidth();
    }
    
    public function getBlockHeight(){
        return Mage::helper('orbaallegro')->getBlockHeight();
    }
    
    public function getKeepFrame(){
        return Mage::helper('orbaallegro')->getKeepFrame();
    }
    
    protected function _resize(
        Mage_Catalog_Model_Product $product,
        $type,
        $width,
        $height,
        $image = null,
        $constrain_only_flag = true,
        $keep_frame_flag = false){
        
        return Mage::helper('catalog/image')
            ->init($product, $type, ($image ? $image->getFile() : null))
            ->constrainOnly($constrain_only_flag)
            ->keepFrame($keep_frame_flag)
            ->resize($width, $height);
        
    }
	
	
//	public function generateAction() {
//		set_time_limit(3600);
//		$curStore = Mage::app()->getStore();
//		Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
//	
//		$coll = Mage::getResourceModel('catalog/product_collection');
//		/* @var $coll Mage_Catalog_Model_Resource_Product_Collection */
//		$select = $coll->getSelect()->
//				order("e.entity_id DESC")->
//				limit(1)->
//				reset(Zend_Db_Select::COLUMNS)->
//				columns(array("e.entity_id"));
//		
//		$id = $select->getAdapter()->fetchOne($select);
//
//		$t = time();
//		
//		for($i=0; $i<1000; $i++){
//			$id++;
//			$product = $this->_getMockupProduct();
//			$product->setSku("SKU-" . $id);
//			$product->setName("Product name " . $id);
//			$product->setId($id);
//			$product->save();
//		}
//		
//		echo time()-$t;
//		
//		Mage::app()->setCurrentStore($curStore);
//	}
//	
//	/**
//	 * @return Mage_Catalog_Model_Product
//	 */
//	protected function _getMockupProduct() {
//		$model = Mage::getModel("catalog/product");
//		/* @var $model Mage_Catalog_Model_Product */
//		
//
//		$model
//			->setWebsiteIds(array(1)) //website ID the product is assigned to, as an array
//			->setAttributeSetId(4) //ID of a attribute set named 'default'
//			->setTypeId('simple') //product type
//			->setCreatedAt(strtotime('now')) //product creation time
//			->setUpdatedAt(strtotime('now')) //product update time
//				
//			->setWeight(4.0000)
//			->setStatus(1) //product status (1 - enabled, 2 - disabled)
//			->setTaxClassId(2) //tax class (0 - none, 1 - default, 2 - taxable, 4 - shipping)
//			->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH) //catalog and search visibility
//
//			->setPrice(199) //price in form 11.22
//				
//			->setMetaTitle('test meta title 2')
//			->setMetaKeyword('test meta keyword 2')
//			->setMetaDescription('test meta description 2')
//
//			->setDescription('This is a long description')
//			->setShortDescription('This is a short description')
//				
//			->setStockData(array(
//					'use_config_manage_stock' => 0, //'Use config settings' checkbox
//					'manage_stock'=>1, //manage stock
//					'min_sale_qty'=>1, //Minimum Qty Allowed in Shopping Cart
//					'max_sale_qty'=>2, //Maximum Qty Allowed in Shopping Cart
//					'is_in_stock' => 1, //Stock Availability
//					'qty' => 999 //qty
//				)
//			);
// 
//   
//		return $model;
//		
//	}
}
