<?php

namespace CoreMarketplace\SellerIdentificationApproval\Helper;

class Data extends \Lofmp\SellerIdentificationApproval\Helper\Data
{
    /**
     * Returns Identification Types
     * @return array
     */
    public function getIdentificationTypes()
    {
        return [
            'company_registration' => __('Business Registration'),
            'identity' => __('Proof of Identity'),
            'utility_address' => __('Proof of Address')
        ];
    }
}
