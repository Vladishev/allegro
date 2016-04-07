<?php

class Orba_Allegro_Model_Mapping_Category extends Mage_Rule_Model_Rule {

    public function run($load = false) {
        ini_set('max_execution_time', 0);
        if ($load) {
            $this->load($load);
        }
        if ($this->getId()) {
            $matched_product_ids = $this->getMatchingProductIds();
            $_product = Mage::getSingleton('orbaallegro/catalog_product');
            $res = $_product->updateCeneoCategory($matched_product_ids, $this->getCeneoproCategoryId());
            unset($matched_product_ids);
            unset($_product);
            return $res;
        }
        return false;
    }

    public function runAll() {
        $collection = $this->getCollection()
                ->setOrder('priority', 'ASC');
        $error = false;
        $ids = array();
        foreach ($collection as $key => $mapping) {
            $ids[] = $mapping->getId();
        }
        unset($collection);
        if (!empty($ids)) {
            foreach ($ids as $id) {
                $m = Mage::getModel('orbaallegro/mapping')->load($id);
                if (!$m->run($id, false)) {
                    $error = true;
                    break;
                }
                unset($m);
            }
        }
        return !$error;
    }
   
}