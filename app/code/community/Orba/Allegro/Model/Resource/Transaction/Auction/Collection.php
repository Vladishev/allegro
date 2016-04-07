<?php
class Orba_Allegro_Model_Resource_Transaction_Auction_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract {

    protected function _construct() {
        parent::_construct();
        $this->_init('orbaallegro/transaction_auction');
    }
    
    public function addProductData() {
        // Add auction table
         $this->getSelect()->joinLeft(
            array("auction"=>$this->getTable("orbaallegro/auction")),
            "auction.auction_id=main_table.auction_id",
            array("product_id")
        );
         // Add sku
        $this->getSelect()->joinLeft(
            array("product"=>$this->getTable("catalog/product")),
            "product.entity_id=auction.product_id",
            array("sku")
        );
        // Add qty
        if (Mage::helper('orbaallegro')->isModuleEnabled('Mage_CatalogInventory')) {
            $this->getSelect()->joinLeft(
                array("stock"=>$this->getTable("cataloginventory/stock_item")),
                "stock.product_id=auction.product_id AND stock.stock_id=1",
                array('stock_qty'=>'stock.qty')
            );
        }
    }
}