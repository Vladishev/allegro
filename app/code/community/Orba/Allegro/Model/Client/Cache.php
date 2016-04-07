<?php

/**
 * Description of Cache
 */
class Orba_Allegro_Model_Client_Cache extends Mage_Core_Model_Cache{
    
    const TAG_ORBAALLEGRO = "ORBAALLEGRO";
    
    public function __construct(array $options = array()) {
        $options['prefix'] = self::TAG_ORBAALLEGRO;
        parent::__construct($options);
    }
    
    
    public function save($data, $id, $tags=array(), $lifeTime=null) {
        if(!in_array($tags, self::ORBAALLEGRO)){
            $tags[] = self::TAG_ORBAALLEGRO;
        }
        return parent::save($data, $id, $tags, $lifeTime);
    }
}