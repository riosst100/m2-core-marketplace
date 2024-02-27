<?php

namespace CoreMarketplace\ProductAttributesLink\Model\ResourceModel;

class ProductAttributesLink extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Dependency Initilization
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init("marketplace_product_attributes_link", "entity_id");
    }
}