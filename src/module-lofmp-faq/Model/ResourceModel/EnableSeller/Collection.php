<?php

namespace Lofmp\Faq\Model\ResourceModel\EnableSeller;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'seller_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Lofmp\Faq\Model\EnableSeller', 'Lofmp\Faq\Model\ResourceModel\EnableSeller');
    }

}