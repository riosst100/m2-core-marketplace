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

namespace Lof\MarketPlace\Api\Data;

interface SalesInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const SELLER_ID = 'seller_id';
    const ORDER_ID = 'order_id';

    /**
     * Get seller_id
     * @return string|null
     */
    public function getSellerId();

    /**
     * Set seller_id
     * @param $sellerId
     * @return SellerInterface
     */
    public function setSellerId($sellerId);

    /**
     * get order_id
     * @return string|null
     */
    public function getOrderId();

    /**
     * Set order_id
     * @param string $order_id
     * @return string|null
     */
    public function setOrderId($order_id);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Lof\MarketPlace\Api\Data\SalesExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Lof\MarketPlace\Api\Data\SalesExtensionInterface $extensionAttributes
     * @return \Lof\MarketPlace\Api\Data\SalesInterface
     */
    public function setExtensionAttributes(
        \Lof\MarketPlace\Api\Data\SalesExtensionInterface $extensionAttributes
    );
}
