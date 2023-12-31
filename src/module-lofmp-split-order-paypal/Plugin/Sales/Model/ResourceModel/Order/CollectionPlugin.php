<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/terms
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_SplitOrderPaypal
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SplitOrderPaypal\Plugin\Sales\Model\ResourceModel\Order;

use Lofmp\SplitOrderPaypal\Helper\Data;
use Magento\Sales\Model\ResourceModel\Order\Collection as OrderCollection;

class CollectionPlugin
{
    /**
     * Const
     */
    const HAS_PAYPAL_ORDER_FILTER = 'has_paypal_order_filter';

    /**
     * @var Data
     */
    private $helperData;

    /**
     * @param Data $helperData
     */
    public function __construct(
        Data $helperData
    ) {
        $this->helperData = $helperData;
    }

    /**
     * @param OrderCollection $collection
     * @param false $printQuery
     * @param false $logQuery
     * @return array|false[]
     */
    public function beforeLoad(
        OrderCollection $collection,
        $printQuery = false,
        $logQuery = false
    ): array {
        if (!$collection->isLoaded()) {
            $this->addPaypalOrderFilter($collection);
        }
        return [$printQuery, $logQuery];
    }

    /**
     * @param OrderCollection $collection
     */
    private function addPaypalOrderFilter(OrderCollection $collection): void
    {
        if (!$this->helperData->isHideMainOrder()) {
            return;
        }
        if (!$collection->hasFlag(self::HAS_PAYPAL_ORDER_FILTER)) {
            $collection->getSelect()->where('pp_is_main_order != ?', 1);
            $collection->setFlag(self::HAS_PAYPAL_ORDER_FILTER, true);
        }
    }
}
