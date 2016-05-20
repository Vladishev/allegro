<?php
require '../../../../../Mage.php';
Mage::app();
//$timestamp = microtime(true);
//require_once '/var/www/multistore/live/scripts/Tim/stock_update/Wms.php';
//require_once '/var/www/multistore/live/scripts/Tim/stock_update/Multistore.php';
//require_once '/var/www/multistore/live/scripts/Tim/stock_update/General.php';
//require_once '/var/www/multistore/live/app/Mage.php';

class Tim_UpdateQuantity_Model_Update extends Mage_Core_Model_Abstract
{
    public function run()
    {
//        $timestamp = microtime(true);
////        $multistore = Mage::getModel('tim_update_quantity/multistore');
//
//        foreach (Mage::getModel('tim_update_quantity/multistore')->getPackagesProductCollection() as $package)
//        {
//            $wms = Mage::getModel('tim_update_quantity/wms');
//            $wms->setProductCollection($package);
//            $products = $wms->getUpdatedData();
////            $multistore = new Multistore();
//
//            if (Mage::getModel('tim_update_quantity/multistore')->update($products))
//            {
//                echo "\r Paczka pobrana i wykonana pomyślnie w czasie: ". round(microtime(true) - $timestamp, 2)."s \033[?25l \n";
//            }
//        }

        /**
         * save auction_ids from table to array
         */
        $auctionIds = array();

        $collection = Mage::getModel('orbaallegro/auction')->getCollection();
        $collection->getSelect()->joinLeft(array('csi' => 'cataloginventory_stock_item'),
            "csi.product_id = main_table.product_id AND csi.qty = 0.0000"/*, array('qty' => 'value')*/);
        $collection->addFieldToFilter('closed_at', array('null' => true))
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
        var_dump($result);
//        if($result){
//            $ids = implode(",", $auctionIds);
//            $query = 'UPDATE wsallegro_published SET finished = 1
//WHERE auction_number IN ('.$ids.')';
//            $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
//            $connection->query($query);
//            Mage::log('Updated ids : ' . $ids, NULL, 'doFinishItems.log');
//        }

        echo "Czyszczenie cache...\n";
        Mage::app()->getCacheInstance()->cleanType('product_loading');
        echo "Koniec.\n";
    }
}

Mage::getModel('tim_update_quantity/update')->run();

