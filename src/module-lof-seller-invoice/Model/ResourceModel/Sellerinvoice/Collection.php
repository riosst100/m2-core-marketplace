<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\SellerInvoice\Model\ResourceModel\Sellerinvoice;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'sellerinvoice_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Lof\SellerInvoice\Model\Sellerinvoice::class,
            \Lof\SellerInvoice\Model\ResourceModel\Sellerinvoice::class
        );
    }
}

