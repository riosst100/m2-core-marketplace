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

namespace Lof\MarketPermissions\Model;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * Class for getting seller id from current seller user.
 */
class SellerUser
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param UserContextInterface $userContext
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        UserContextInterface $userContext
    ) {
        $this->customerRepository = $customerRepository;
        $this->userContext = $userContext;
    }

    /**
     * Get current seller id.
     *
     * @return int|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCurrentSellerId()
    {
        $customer = $this->customerRepository->getById($this->userContext->getUserId());
        return $customer->getExtensionAttributes() && $customer->getExtensionAttributes()->getSellerAttributes()
            ? $customer->getExtensionAttributes()->getSellerAttributes()->getSellerId()
            : null;
    }
}
