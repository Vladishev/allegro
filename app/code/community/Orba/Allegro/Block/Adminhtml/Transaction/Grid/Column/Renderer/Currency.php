<?php
class Orba_Allegro_Block_Adminhtml_Transaction_Grid_Column_Renderer_Currency
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Currency {
    
    public function render(Varien_Object $row)
    {
        // Display also zero-value price
        if (null!==($data = (string)$row->getData($this->getColumn()->getIndex()))) {
            $currency_code = $this->_getCurrencyCode($row);

            if (!$currency_code) {
                return $data;
            }

            $data = floatval($data) * $this->_getRate($row);
            $sign = (bool)(int)$this->getColumn()->getShowNumberSign() && ($data > 0) ? '+' : '';
            $data = sprintf("%f", $data);
            $data = Mage::app()->getLocale()->currency($currency_code)->toCurrency($data);
            return $sign . $data;
        }
        return $this->getColumn()->getDefault();
    }
    
}
