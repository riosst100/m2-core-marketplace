<?php

namespace CoreMarketplace\MarketPlace\Block\Onepage;

class Success extends \Magento\Checkout\Block\Onepage\Success
{
    public function getContinueUrl()
    {
        return $this->getUrl('marketplace/catalog/dashboard');
    }
}
