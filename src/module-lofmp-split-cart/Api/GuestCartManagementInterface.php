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
 * @package    Lofmp_SplitCart
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
declare(strict_types=1);

namespace Lofmp\SplitCart\Api;

use Magento\Quote\Api\Data\PaymentInterface;

/**
 * Cart Management interface for guest carts.
 * @api
 * @since 100.0.2
 */
interface GuestCartManagementInterface
{
    /**
     * Place an order for a specified cart.
     *
     * @param string $cartId The cart ID.
     * @param string $sellerUrl
     * @param PaymentInterface|null $paymentMethod
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return int Order ID.
     */
    public function placeOrder($cartId, $sellerUrl, PaymentInterface $paymentMethod = null);
}
