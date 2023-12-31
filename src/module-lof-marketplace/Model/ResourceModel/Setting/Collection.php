<?php
/**
 * Copyright © asdfasd All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\MarketPlace\Model\ResourceModel\Setting;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'setting_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Lof\MarketPlace\Model\Setting::class,
            \Lof\MarketPlace\Model\ResourceModel\Setting::class
        );
    }
}

