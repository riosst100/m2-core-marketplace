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

namespace Lof\MarketPermissions\Model\Webapi;

use Magento\Authorization\Model\UserContextInterface;
use Lof\MarketPermissions\Api\Data\SellerCustomerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Webapi\Rest\Request\ParamOverriderInterface;

/**
 * Enforces current seller ID.
 */
class ParamOverriderSellerId implements ParamOverriderInterface
{
    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepo;

    /**
     * @param UserContextInterface $userContext
     * @param CustomerRepositoryInterface $customerRepo
     */
    public function __construct(UserContextInterface $userContext, CustomerRepositoryInterface $customerRepo)
    {
        $this->userContext = $userContext;
        $this->customerRepo = $customerRepo;
    }

    /**
     * @inheritDoc
     */
    public function getOverriddenValue()
    {
        if ($this->userContext->getUserType() === UserContextInterface::USER_TYPE_CUSTOMER
            && $this->userContext->getUserId()
        ) {
            $customer = $this->customerRepo->getById($this->userContext->getUserId());
            if ($customer->getExtensionAttributes() && $customer->getExtensionAttributes()->getSellerAttributes()) {
                /** @var SellerCustomerInterface $sellerAttributes */
                $sellerAttributes = $customer->getExtensionAttributes()->getSellerAttributes();
                return $sellerAttributes->getSellerId();
            }
        }

        return null;
    }
}
