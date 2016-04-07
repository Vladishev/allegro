<?php
class Orba_Allegro_Block_Template_Gallery extends Orba_Allegro_Block_Template_Abstract{

    
    public function getGalleryImages()
    {
        if ($this->_isGalleryDisabled) {
            return array();
        }
        $collection = $this->getProduct()->getMediaGalleryImages();
        return $collection;
    }
    
    public function getImageUrl($image=null) {
        if($this->getProduct()
			&& $this->getProduct()->getImage()
			&& $this->getProduct()->getImage() !== 'no_selection') {
            $params = array(
                "image_id" => $image ? $image->getId() : 0,
                "product_id" => $this->getProduct()->getId(),
                "_nosid" => true,
                "_query" => array("format"=>"image.jpg")
            );
            return Mage::getUrl(self::GETTER_PATH, $params);
        }
        return null;
    }
    public function getOriginImageUrl($image) {
        return Mage::getBaseUrl('media')  . "catalog/product" . $image->getFile();
    }
	
	public function getParentImage() {
		$productIds = Mage::helper('orbaallegro')->getParentIds($this->getProduct()->getId(), $this->getProduct()->getTypeId());
		return $this->getParentImageUrl($productIds);
	}
	
    public function getParentImageUrl($productIds, $image = null) {
		if (!$productIds || !is_array($productIds)) {
			return null;
		} else {
			$productId = array_shift($productIds);
		}
		
		$params = array(
			"image_id" => $image ? $image->getId() : 0,
			"product_id" => $productId,
			"_nosid" => true,
			"_query" => array("format"=>"image.jpg")
		);
		return Mage::getUrl(self::GETTER_PATH, $params);
    }
	
	public function getParentGalleryImages($productId, $productType) {
		$galeryHelper = Mage::helper('orbaallegro/gallery');
		return $galeryHelper->getMediaData(Mage::helper('orbaallegro')->getParentIds($productId, $productType));
	}
	
    public function getOriginParentImageUrl($image) {
        return Mage::getBaseUrl('media')  . "catalog/product" . $image['file'];
    }	
}
