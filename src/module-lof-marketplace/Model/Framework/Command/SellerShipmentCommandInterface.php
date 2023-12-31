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
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Model\Framework\Command;

use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Magento\Framework\Message\ManagerInterface;
use Lof\MarketPlace\Model\Orderitems;
use Lof\MarketPlace\Model\ResourceModel\Orderitems as SellerOrderItemsResource;
use Lof\MarketPlace\Model\ResourceModel\Order as SellerOrderResource;
use Lof\MarketPlace\Model\ResourceModel\Invoice as SellerInvoiceResource;
use Lof\MarketPlace\Model\ResourceModel\Orderitems\CollectionFactory as CollectionFactory;

/**
 * SellerShipmentCommandInterface
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
interface SellerShipmentCommandInterface
{
    /**
     * execute create seller invoice
     *
     * @param mixed $invoice
     * @param int $sellerId
     * @param mixed|array $items
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function execute($invoice, int $sellerId, $items = []): bool;
}
