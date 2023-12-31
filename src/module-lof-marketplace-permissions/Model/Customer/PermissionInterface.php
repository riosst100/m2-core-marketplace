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
 * @package    Lof_MarketPermissions
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPermissions\Model\Customer;

use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Interface PermissionInterface
 */
interface PermissionInterface
{
    /**
     * Is checkout allowed.
     *
     * @param CustomerInterface $customer
     * @param bool $isNegotiableQuoteActive
     * @return bool
     */
    public function isCheckoutAllowed(
        CustomerInterface $customer,
        $isNegotiableQuoteActive = false
    );

    /**
     * Is customer seller blocked.
     *
     * @param CustomerInterface $customer
     * @return bool
     */
    public function isSellerBlocked(CustomerInterface $customer);

    /**
     * Is login allowed.
     *
     * @param CustomerInterface $customer
     * @return bool
     */
    public function isLoginAllowed(CustomerInterface $customer);
}
