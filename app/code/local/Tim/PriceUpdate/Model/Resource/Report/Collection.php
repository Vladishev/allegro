<?php
/**
 * Price update report collection
 *
 * @category  Tim
 * @package   Tim_PriceUpdate
 * @author    Oleksii Rybin <orybin@divante.pl>
 * @copyright 2014 Divante
 */
class Tim_PriceUpdate_Model_Resource_Report_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize a collection, set model for it
     */
    protected function _construct()
    {
        $this->_init('tim_priceupdate/report');
    }
}