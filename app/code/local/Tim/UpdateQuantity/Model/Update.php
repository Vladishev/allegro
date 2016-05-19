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

        require_once '/home/vlado/workspace/allegro/shell/stock_update/doFinishItems.php';

        echo "Czyszczenie cache...\n";
        Mage::app()->getCacheInstance()->cleanType('product_loading');
        echo "Koniec.\n";
    }
}

Mage::getModel('tim_update_quantity/update')->run();

