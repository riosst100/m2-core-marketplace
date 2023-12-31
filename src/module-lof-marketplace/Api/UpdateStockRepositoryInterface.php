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

namespace Lof\MarketPlace\Api;

interface UpdateStockRepositoryInterface
{

    /**
     * Update stock qty, require fields: product_id, qty, is_in_stock
     * @param int $customerId
     * @param \Lof\MarketPlace\Api\Data\ProductInterface $product
     * @return \Lof\MarketPlace\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function saveSellerStock(int $customerId, $product);

    /**
     * Update product price, require fields: product_id, price
     * @param int $customerId
     * @param \Lof\MarketPlace\Api\Data\ProductInterface $product
     * @return \Lof\MarketPlace\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function saveSellerProductPrice(int $customerId, $product);
}
