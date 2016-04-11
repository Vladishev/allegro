<?php

class Orba_Allegro_Helper_Gallery extends Mage_Core_Helper_Abstract
{
	/**
	 * Get Product Galery by Product Id(s)
	 * 
	 * @param mixded int|array $productId
	 * 
	 * @return array Media Gallery Data
	 */
	public function getMediaData($productId) {
		if (!$productId) {
			return array();
		}
		
        if (!is_array($productId)) {
			$productId = array($productId);
		}
		
        $_mediaGalleryByProductId = array();
        if (!empty($productId)) {
            $_mediaGalleryAttributeId = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'media_gallery')->getAttributeId();
            $_read = Mage::getSingleton('core/resource')->getConnection('catalog_read');

            $_mediaGalleryData = $_read->fetchAll('
				SELECT
					main.entity_id, `main`.`value_id`, `main`.`value` AS `file`,
					`value`.`label`, `value`.`position`, `value`.`disabled`, `default_value`.`label` AS `label_default`,
					`default_value`.`position` AS `position_default`,
					`default_value`.`disabled` AS `disabled_default`
				FROM `' . Mage::getSingleton('core/resource')->getTableName('catalog_product_entity_media_gallery') . '` AS `main`
					LEFT JOIN `' . Mage::getSingleton('core/resource')->getTableName('catalog_product_entity_media_gallery_value') . '` AS `value`
						ON main.value_id=value.value_id AND value.store_id=' . Mage::app()->getStore()->getId() . '
					LEFT JOIN `' . Mage::getSingleton('core/resource')->getTableName('catalog_product_entity_media_gallery_value') . '` AS `default_value`
						ON main.value_id=default_value.value_id AND default_value.store_id=0
				WHERE (
					main.attribute_id = ' . $_read->quote($_mediaGalleryAttributeId) . ') 
					AND (main.entity_id IN (' . $_read->quote($productId) . '))
				ORDER BY IF(value.position IS NULL, default_value.position, value.position) ASC    
			');
            foreach ($_mediaGalleryData as $_galleryImage) {
                $k = $_galleryImage['entity_id'];
                unset($_galleryImage['entity_id']);
                if (!isset($_mediaGalleryByProductId[$k])) {
                    $_mediaGalleryByProductId[$k] = array();
                }
                $_mediaGalleryByProductId[$k][] = $_galleryImage;
            }
            unset($_mediaGalleryData);
        }
        return $_mediaGalleryByProductId;
    }
}