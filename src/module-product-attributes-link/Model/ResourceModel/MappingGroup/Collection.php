<?php

namespace CoreMarketplace\ProductAttributesLink\Model\ResourceModel\MappingGroup;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';
    
    /**
     * Dependency Initilization
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(
            \CoreMarketplace\ProductAttributesLink\Model\MappingGroup::class,
            \CoreMarketplace\ProductAttributesLink\Model\ResourceModel\MappingGroup::class
        );
        $this->_map['fields']['entity_id'] = 'main_table.entity_id';
    }
}