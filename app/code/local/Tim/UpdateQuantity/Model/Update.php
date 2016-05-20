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
        $timestamp = microtime(true);
//        $multistore = Mage::getModel('tim_update_quantity/multistore');

        foreach (Mage::getModel('tim_update_quantity/multistore')->getPackagesProductCollection() as $package)
        {
            $wms = Mage::getModel('tim_update_quantity/wms');
            $wms->setProductCollection($package);
            $products = $wms->getUpdatedData();
//            $multistore = new Multistore();

            if (Mage::getModel('tim_update_quantity/multistore')->update($products))
            {
                echo "\r Paczka pobrana i wykonana pomyślnie w czasie: ". round(microtime(true) - $timestamp, 2)."s \033[?25l \n";
            }
        }

        /**
         * save auction_ids from table to array
         */
        $auctionIds = array();

        $sql = "select oa.allegro_auction_id
from catalog_product_entity cpe
JOIN orba_allegro_auction oa ON oa.product_id = cpe.entity_id
JOIN cataloginventory_stock_item csi ON cpe.entity_id = csi.product_id
WHERE oa.closed_at IS NOT NULL";
        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
        $rows = $connection->fetchAll($sql);
        foreach ($rows as $row) {
            $auctionIds[] = $row['auction_number'];
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
            $ids = implode(",", $auctionIds);
            $query = 'UPDATE wsallegro_published SET finished = 1
WHERE auction_number IN ('.$ids.')';
            $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
            $connection->query($query);
            Mage::log('Updated ids : ' . $ids, NULL, 'doFinishItems.log');
        }

        echo "Czyszczenie cache...\n";
        Mage::app()->getCacheInstance()->cleanType('product_loading');
        echo "Koniec.\n";
    }
}

Mage::getModel('tim_update_quantity/update')->run();

