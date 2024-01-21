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
            'utility_address' => __('Utility Address'),
            'company_registration' => __('Company Registration')
        ];
    }
}
