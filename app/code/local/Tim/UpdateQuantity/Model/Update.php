<?php

/**
 * Class Tim_UpdateQuantity_Model_Update
 *
 * Start to update products quantity
 * Updates current auctions - set 'ended' if product has quantity 0
 */
class Tim_UpdateQuantity_Model_Update extends Mage_Core_Model_Abstract
{
    /**
     * Starts update process
     */
    public function run()
    {
        $timestamp = microtime(true);

        foreach (Mage::getModel('tim_update_quantity/multistore')->getPackagesProductCollection() as $package)
        {
            $wms = Mage::getModel('tim_update_quantity/wms');
            $wms->setProductCollection($package);
            $products = $wms->getUpdatedData();

            if (Mage::getModel('tim_update_quantity/multistore')->update($products))
            {
                echo "\r Paczka pobrana i wykonana pomyślnie w czasie: ". round(microtime(true) - $timestamp, 2)."s \033[?25l \n";
            }
        }

        /**
         * save auction_ids from table to array
         */
        $auctionIds = array();

        $collection = Mage::getModel('orbaallegro/auction')->getCollection();
        $collection->getSelect()->joinLeft(array('csi' => 'cataloginventory_stock_item'),
            "csi.product_id = main_table.product_id AND csi.qty = 0.0000");
        $collection->addFieldToFilter('auction_status', array('placed', 'sell_again'))
            ->addFieldToFilter('allegro_auction_id', array('neq' => '0' ))
            ->addFieldToFilter('qty', '0.0000')
            ->addFieldToSelect('allegro_auction_id')
            ->getData();
        foreach ($collection as $key => $value) {
            $auctionIds[] = $value['allegro_auction_id'];
        }
        /**
         * prepare doFinishItems_request
         */
        $webservice = Mage::getModel('tim_update_quantity/webservice');
        $sessionHandle = $webservice->getServiceSession()->sessionHandlePart;

        $i = 0;
        while ($i < count($auctionIds)) {
            $requestItemsLimit = 0;
            $doFinishItemsRequest = array();
            $doFinishItemsRequest['sessionHandle'] = $sessionHandle;
            while (($requestItemsLimit < 25)  && isset($auctionIds[$i])) {
                $doFinishItemsRequest['finishItemsList'][] = array(
                    'finishItemId' => $auctionIds[$i],
                    'finishCancelAllBids' => 0,
                    'finishCancelReason' => ''
                );
                $requestItemsLimit++;
                $i++;
            }
            $result = $webservice->doFinishItems($doFinishItemsRequest);
        }
        /**
         * update data in wsallegro_published table
         */
        if($result){
            $auctionCollection = Mage::getModel('orbaallegro/auction')
                ->getCollection()
                ->addFieldToFilter('allegro_auction_id', $auctionIds);
            foreach ($auctionCollection as $item) {
                $item->setAuctionStatus('ended');
                $item->save();
            }
        }

        echo "Czyszczenie cache...\n";
        Mage::app()->getCacheInstance()->cleanType('product_loading');
        echo "Koniec.\n";
    }
}

