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

namespace Lof\MarketPermissions\Api;

use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Interface for retrieving various entity data objects by a given parameters and assigning customers to a seller.
 */
interface SellerManagementInterface
{
    /**
     * Get seller by customer Id.
     *
     * @param int $customerId
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function getByCustomerId($customerId);

    /**
     * Get sales representative (admin user that is responsible for seller) entity data object by a given user id.
     *
     * @param int $userId
     * @return string
     */
    public function getSalesRepresentative($userId);

    /**
     * Get seller admin customer entity data object by a given seller id.
     *
     * @param int $sellerId
     * @return CustomerInterface|null
     */
    public function getAdminBySellerId($sellerId);

    /**
     * Assign customer to seller.
     *
     * @param int $sellerId
     * @param int $customerId
     * @return void
     */
    public function assignCustomer($sellerId, $customerId);
}
