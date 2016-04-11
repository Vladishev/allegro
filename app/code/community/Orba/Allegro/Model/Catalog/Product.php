<?php

class Orba_Allegro_Model_Catalog_Product extends Mage_Catalog_Model_Product {
        
    protected function getConfig() {
        return Mage::getModel('orbaallegro/config');
    }

    public function update($product_ids = array(), $attribute_code, $entity_id, array $stores = null) {
        $error = false;
        if(!$stores || empty($stores)){
            $stores = array(Mage_Core_Model_App::ADMIN_STORE_ID);
        }
        try {
            foreach($stores as $storeId){
                $product_collection = $this->getCollection()
                    ->addAttributeToSelect($attribute_code, 'left')
                    ->addAttributeToFilter('entity_id', array('in' => $product_ids));
                /* @var $product_collection Mage_Catalog_Model_Resource_Product_Collection */
                $product_collection->addStoreFilter($storeId);

                $product_collection->addAttributeToFilter(array(
                    array(
                        'attribute' => $attribute_code,
                        'neq' => $entity_id,
                    ),
                    array(
                        'attribute' => $attribute_code,
                        'null' => true,
                    )
                ));

                foreach ($product_collection as $key => $product) {
                    /* @var $product Mage_Catalog_Model_Product */
                    $product->setData($attribute_code, $entity_id);
                    $product->setStoreId($storeId);
                    $this->getResource()->saveAttribute($product, $attribute_code);       
                    $product_collection->removeItemByKey($key);
                    $product->clearInstance();
                }
            }
        } catch (Exception $e) {
            $error = true;
            Mage::getModel('adminhtml/session')->addException($e, Mage::helper('orbaallegro')->__('An error occurred while running this mapping.'));
        }
        return !$error;
    }

}