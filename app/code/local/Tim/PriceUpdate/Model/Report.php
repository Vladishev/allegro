<?php
/**
 * Price update report model
 *
 * @category  Tim
 * @package   Tim_PriceUpdate
 * @author    Oleksii Rybin <orybin@divante.pl>
 * @copyright 2014 Divante
 */
class Tim_PriceUpdate_Model_Report extends Mage_Core_Model_Abstract
{
    /**
     * Initialize a report model, set resource model for it
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('tim_priceupdate/report');
    }
}