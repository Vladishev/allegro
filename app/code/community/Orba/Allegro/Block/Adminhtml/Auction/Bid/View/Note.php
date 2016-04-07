<?php
class Orba_Allegro_Block_Adminhtml_Auction_Bid_View_Note extends Mage_Adminhtml_Block_Widget {

    /**
     *  @return Orba_Allegro_Model_Auction_Bid
     */
    public function getModel() {
        return Mage::registry('orbaallegro_current_auction_bid');
    }

    /**
     * @return Orba_Allegro_Model_Resource_Auction_Bid_Note
     */
    public function getCollection() {
        if(!$this->hasData("collection")){
            $bid = $this->getModel();
            $collection = $bid->getNoteCollection();
            $this->setData("collection", $collection);
        }
        return $this->getData("collection");
    }
    
    /**
     * @return Varien_Data_Form
     */
    public function getForm(){
        if(!$this->getData("form")){
             $form = new Varien_Data_Form(array(
                'id' => 'edit_form',
                'action' => $this->getUrl("*/auction_bid/addNote"),
                'method' => 'post'
            ));


            $fieldset = $form->addFieldset("note", 
                    array("legend"=>Mage::helper("orbaallegro")->__("Bid notes"))
            );
            
            $fieldset->addField("content", "textarea", 
                    array("name"=>"content", "label"=>Mage::helper("orbaallegro")->__("Note"))
            );
            $fieldset->addField("send", "submit", 
                    array("name"=>"send", "value"=>Mage::helper("orbaallegro")->__("Add note"))
            );
            
            $this->setData("form", $form);
        }
        return $this->getData("form");
    }


    public function getNoteField() {
        return $this->getForm()->getElement("content");
    }
    public function getSendButton() {
        return $this->getForm()->getElement("send");
    }
}
