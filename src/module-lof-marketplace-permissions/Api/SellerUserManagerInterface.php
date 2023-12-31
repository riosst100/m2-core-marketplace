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

declare(strict_types=1);

namespace Lof\MarketPermissions\Api;

use Lof\MarketPermissions\Api\Data\SellerCustomerInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Manage customers assigned to sellers.
 */
interface SellerUserManagerInterface
{
    /**
     * Accept invitation to a seller.
     *
     * @param string $invitationCode
     * @param SellerCustomerInterface $customer
     * @param string|null $roleId If not explicit role provided a default one will be assigned.
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     * @return void
     */
    public function acceptInvitation(string $invitationCode, SellerCustomerInterface $customer, ?string $roleId): void;

    /**
     * Send invitation to existing customer to join a seller.
     *
     * @param SellerCustomerInterface $forCustomer
     * @param string|null $roleId If not explicit role provided a default one will be assigned.
     * @throws LocalizedException
     * @return void
     */
    public function sendInvitation(SellerCustomerInterface $forCustomer, ?string $roleId): void;
}
