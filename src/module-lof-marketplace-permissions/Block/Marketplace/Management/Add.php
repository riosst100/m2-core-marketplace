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

namespace Lof\MarketPermissions\Block\Marketplace\Management;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\View\Element\Template\Context;

/**
 * Block for add new customer.
 */
class Add extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Authorization\Model\UserContextInterface
     */
    private $userContext;

    /**
     * @var \Lof\MarketPermissions\Api\RoleManagementInterface
     */
    private $roleManagement;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * Add constructor.
     *
     * @param Context $context
     * @param UserContextInterface $userContext
     * @param CustomerRepositoryInterface $customerRepository
     * @param \Lof\MarketPermissions\Api\RoleManagementInterface $roleManagement
     * @param array $data [optional]
     */
    public function __construct(
        Context $context,
        UserContextInterface $userContext,
        CustomerRepositoryInterface $customerRepository,
        \Lof\MarketPermissions\Api\RoleManagementInterface $roleManagement,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->userContext = $userContext;
        $this->roleManagement = $roleManagement;
        $this->customerRepository = $customerRepository;
    }

    /**
     * Get roles.
     *
     * @return \Lof\MarketPermissions\Api\Data\RoleInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getRoles()
    {
        if (!$this->userContext->getUserId()) {
            return [];
        }
        $customer = $this->customerRepository->getById($this->userContext->getUserId());
        $sellerId = $customer->getExtensionAttributes()->getSellerAttributes()->getSellerId();
        return $this->roleManagement->getRolesBySellerId($sellerId);
    }
}
