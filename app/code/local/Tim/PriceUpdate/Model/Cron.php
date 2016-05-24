<?php

/**
 * PriceUpdate cron model
 *
 * @category  Tim
 * @package   Tim_PriceUpdate
 * @author    Oleksii Rybin <orybin@divante.pl>
 * @copyright 2014 Divante
 */
class Tim_PriceUpdate_Model_Cron
{
    /**
     * Updates prices
     *
     * @return null
     */
    public function updatePrice()
    {
        /* @var $priceImport Tim_PriceUpdate_Model_Import */
        $priceImport = Mage::getModel('tim_priceupdate/import');
        $priceImport->run();
    }
}