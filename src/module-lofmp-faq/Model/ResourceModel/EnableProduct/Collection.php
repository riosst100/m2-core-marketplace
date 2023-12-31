<?php

namespace Lofmp\Faq\Model\ResourceModel\EnableProduct;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'product_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Lofmp\Faq\Model\EnableProduct', 'Lofmp\Faq\Model\ResourceModel\EnableProduct');
    }

}