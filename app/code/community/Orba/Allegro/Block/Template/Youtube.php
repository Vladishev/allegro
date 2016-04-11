<?php
class Orba_Allegro_Block_Template_Youtube extends Orba_Allegro_Block_Template_Abstract{

    const PLAY_FILENAME = "play.png";

    
    public function getYoutubeUrl() {
        if($code=$this->getYoutubeCode()){
            return "http://www.youtube.com/watch?v=" . $code;
        }
        return "";
    }
    
    public function getYoutubeCode() {
        $product = $this->getProduct();
        if($product && $product->getOrbaallegroYoutubeCode()){
            return $product->getOrbaallegroYoutubeCode();
        }
        return null;
    }
    
    public function getPlayButtonUrl() {
        return $this->getSkinUrl("orbaallegro" . "/"  ."images" . "/"  . self::PLAY_FILENAME);
    }
    
    public function getProductImageUrl() {
        if($this->getProduct()){
            $params = array(
                "product_id" => $this->getProduct()->getId(),
                "_nosid" => true,
                "_query" => array("format"=>"image.jpg")
            );
            return Mage::getUrl(self::GETTER_PATH, $params);
        }
        return null;
    }
  
}
