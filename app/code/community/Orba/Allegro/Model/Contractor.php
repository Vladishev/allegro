<?php
class Orba_Allegro_Model_Contractor extends Mage_Core_Model_Abstract {

    /**
     * Resource & collection
     */
    protected $_resourceName = "orbaallegro/contractor";
    protected $_resourceCollectionName = "orbaallegro/contractor_collection";
    
    public function bindAllegroData($contractorData) {
        $userData = $contractorData->userData;
        $sendData = $contractorData->userSentToData;
        $companyData = $contractorData->companySecondAddress;
        
        $data = array(
            'country_code'      => $userData->siteCountryId,   
            'login'             => $userData->userLogin,          
            'email'             => $userData->userEmail,          
            'firstname'         => $userData->userFirstName,      
            'lastname'          => $userData->userLastName,       
            'company'           => $userData->userCompany,        
            'postcode'          => $userData->userPostcode,
            'street'            => $userData->userAddress,         
            'city'              => $userData->userCity,           
            'phone'             => $userData->userPhone,          
            'country'           => $userData->userCountryId,        
            'region'            => $userData->userStateId,         
            'has_send_data'     => 0,
            'send_firstname'    => "",
            'send_lastname'     => "",
            'send_company'      => "",
            'send_postcode'     => "",
            'send_street'       => "",
            'send_city'         => "",
            'send_country'      => 0,
            'has_company_data'  => 0,
            'company_firstname' => "",
            'company_lastname'  => "",
            'company_postcode'  => "",
            'company_street'    => "",
            'company_city'      => "",
            'company_country'   => 0,
            'company_region'    => 0
        );
        
        if($sendData && (int)$sendData->userCountryId){
            $data = array_merge($data, array(
                'has_send_data'  => 1,
                'send_firstname'    => $sendData->userFirstName, 
                'send_lastname'     => $sendData->userLastName,  
                'send_company'      => $sendData->userCompany,
                'send_postcode'     => $sendData->userPostcode,  
                'send_street'       => $sendData->userAddress,    
                'send_city'         => $sendData->userCity,      
                'send_country'      => $sendData->userCountryId,   
            ));
        }
        
        if($companyData && (int)$companyData->companyCountryId){
            $data = array_merge($data, array(
                'has_company_data'  => 1,
                'company_firstname' => $companyData->companyWorkerFirstName,
                'company_lastname'  => $companyData->companyWorkerLastName,
                'company_postcode'  => $companyData->companyPostcode,
                'company_street'    => $companyData->companyAddress, 
                'company_company'   => $userData->userCompany,
                'company_city'      => $companyData->companyCity,   
                'company_country'   => $companyData->companyCountryId,
                'company_region'    => $companyData->companyStateId
            ));
        }
        
        $this->addData($data);
        
        return $this;
    }
    
}