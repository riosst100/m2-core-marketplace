<?php

namespace Lofmp\Faq\Model\ResourceModel\Setting;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Lofmp\Faq\Model\Setting', 'Lofmp\Faq\Model\ResourceModel\Setting');
    }

}