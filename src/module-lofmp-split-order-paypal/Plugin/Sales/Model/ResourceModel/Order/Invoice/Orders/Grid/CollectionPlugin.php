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

namespace Lofmp\SplitOrderPaypal\Plugin\Sales\Model\ResourceModel\Order\Invoice\Orders\Grid;

use Lofmp\SplitOrderPaypal\Helper\Data;
use Magento\Framework\DB\Select;
use Magento\Sales\Model\ResourceModel\Order\Invoice\Orders\Grid\Collection as InvoiceGridCollection;

class CollectionPlugin
{
    /**
     * Const
     */
    const HAS_PAYPAL_INVOICE_GRID_FILTER = 'has_paypal_invoice_grid_filter';

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
     * @param InvoiceGridCollection $collection
     * @param false $printQuery
     * @param false $logQuery
     * @return array|false[]
     */
    public function beforeLoad(
        InvoiceGridCollection $collection,
        $printQuery = false,
        $logQuery = false
    ): array {
        if (!$collection->isLoaded()) {
            $this->addPaypalInvoiceFilter($collection);
        }
        return [$printQuery, $logQuery];
    }

    /**
     * Get SQL for get record count
     *
     * @return Select
     */
    public function afterGetSelectCountSql(InvoiceGridCollection $collection, Select $countSelect)
    {
        if (!$this->helperData->isHideMainOrder()) {
            return $countSelect;
        }
        $this->addPaypalInvoiceFilter($collection);

        return $countSelect;
    }

    /**
     * @param InvoiceGridCollection $collection
     */
    private function addPaypalInvoiceFilter(InvoiceGridCollection $collection): void
    {
        if (!$this->helperData->isHideMainOrder()) {
            return;
        }
        if (!$collection->hasFlag(self::HAS_PAYPAL_INVOICE_GRID_FILTER)) {
//            $collection->getSelect()->where('pp_is_main_order != ?', 1);
            $collection->setFlag(self::HAS_PAYPAL_INVOICE_GRID_FILTER, true);
        }
    }
}
