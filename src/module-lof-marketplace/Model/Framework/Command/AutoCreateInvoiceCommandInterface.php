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

/**
 * AutoCreateInvoiceCommandInterface
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
interface AutoCreateInvoiceCommandInterface
{
    /**
     * Execute auto create invoice
     *
     * @param mixed|object $orderData
     * @param int $orderId
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function execute($orderData, $orderId = 0): int;

    /**
     * is allow auto generate invoice
     *
     * @param mixed $orderItems
     * @param mixed $orderData
     * @return bool
     */
    public function isAllowAutoGenerateInvoice($orderItems, $orderData): bool;

    /**
     * set Flag allow auto generate invoice
     *
     * @param bool $flag
     * @return $this
     */
    public function forceEnableAutoInvoice($flag = true);

    /**
     * get Flag
     *
     * @return bool
     */
    public function getFlag(): bool;
}
