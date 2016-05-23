<?php
/**
 * A renderer for percentage value grid column
 *
 * @category  Tim
 * @package   Tim_PriceUpdate
 * @author    Oleksii Rybin <orybin@divante.pl>
 * @copyright 2014 Divante
 */
class Tim_PriceUpdate_Block_Adminhtml_Widget_Grid_Column_Renderer_Percentage extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render percentage value grid column
     *
     * @param Varien_Object $row a grid row
     *
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $value = (string) $row->getData($this->getColumn()->getIndex());

        $sign = '';
        if ($value > 0) {
            $sign = '+';
        }

        $html = $sign . round($value, 2) . '%';

        return $html;
    }
}