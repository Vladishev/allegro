<?php

class Orba_Allegro_Block_Adminhtml_Auction_Image_Renderer extends Varien_Data_Form_Element_Image {
    /**
     * Return html code of delete checkbox element
     *
     * @return string
     */
    protected function _getDeleteCheckbox()
    {
        $html = '';
        if ($this->getValue()) {
            $label = Mage::helper('orbaallegro')->__('Skip this image');
            $html .= '<span class="delete-image">';
            $html .= '<input type="checkbox"'
                . ' name="' . parent::getName() . '[skip]" value="1" class="checkbox"'
                . ' id="' . $this->getHtmlId() . '_skip"' . ($this->getDisabled() ? ' disabled="disabled"': '')
                . ($this->getChecked() ? ' checked="checked"' : '').'/>';
            $html .= '<label for="' . $this->getHtmlId() . '_skip"'
                . ($this->getDisabled() ? ' class="disabled"' : '') . '> ' . $label . '</label>';
            $html .= $this->_getHiddenInput();
            $html .= '</span>';
        }

        return $html;
    }
    
    public function getElementHtml() {
        return str_replace('height="22" ', "", parent::getElementHtml());
    }
}