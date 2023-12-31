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

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * SellerContext pool.
 */
class SellerContext
{
    /**
     * @var \Lof\MarketPermissions\Api\StatusServiceInterface
     */
    private $moduleConfig;

    /**
     * @var \Magento\Authorization\Model\UserContextInterface
     */
    private $userContext;

    /**
     * @var \Lof\MarketPermissions\Api\AuthorizationInterface
     */
    private $authorization;

    /**
     * @var int
     */
    private $customerGroupId;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var CustomerSession
     */
    protected $_customerSession;

    /**
     * @var \Lof\MarketPermissions\Model\SellerUserPermission
     */
    private $sellerUserPermission;

    /**
     * @var \Lof\MarketPlace\Model\SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var \Lof\MarketPermissions\Model\SaveHandlerPool
     */
    private $saveHandlerPool;

    /**
     * @var \Lof\MarketPermissions\Model\SellerAdminPermission
     */
    private $sellerAdminPermission;

    /**
     * SellerContext constructor.
     *
     * @param \Lof\MarketPermissions\Api\StatusServiceInterface $moduleConfig
     * @param \Magento\Authorization\Model\UserContextInterface $userContext
     * @param \Lof\MarketPermissions\Api\AuthorizationInterface $authorization
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Lof\MarketPermissions\Model\SellerUserPermission $sellerUserPermission
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerFactory
     * @param \Lof\MarketPermissions\Model\SaveHandlerPool $saveHandlerPool
     * @param SellerAdminPermission $sellerAdminPermission
     * @param CustomerSession $customerSession
     */
    public function __construct(
        \Lof\MarketPermissions\Api\StatusServiceInterface $moduleConfig,
        \Magento\Authorization\Model\UserContextInterface $userContext,
        \Lof\MarketPermissions\Api\AuthorizationInterface $authorization,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Lof\MarketPermissions\Model\SellerUserPermission $sellerUserPermission,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Lof\MarketPermissions\Model\SaveHandlerPool $saveHandlerPool,
        \Lof\MarketPermissions\Model\SellerAdminPermission $sellerAdminPermission,
        CustomerSession $customerSession
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->userContext = $userContext;
        $this->authorization = $authorization;
        $this->customerRepository = $customerRepository;
        $this->sellerUserPermission = $sellerUserPermission;
        $this->sellerFactory = $sellerFactory;
        $this->_customerSession = $customerSession;
        $this->saveHandlerPool = $saveHandlerPool;
        $this->sellerAdminPermission = $sellerAdminPermission;
    }

    /**
     * Checks if module is active.
     *
     * @return bool
     */
    public function isModuleActive()
    {
        return $this->moduleConfig->isActive();
    }

    /**
     * Checks if seller registration from the storefront is allowed.
     *
     * @return bool
     */
    public function isStorefrontRegistrationAllowed()
    {
        return $this->moduleConfig->isStorefrontRegistrationAllowed();
    }

    /**
     * @return bool
     */
    public function isSellerActive()
    {
        $customerId = $this->getCustomerSession()->getId();
        return (bool)$this->sellerFactory->create()->load($customerId, 'customer_id')->getStatus();
    }

    /**
     * @return CustomerSession
     */
    public function getCustomerSession()
    {
        return $this->_customerSession;
    }

    /**
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createSellerAdmin($customerId)
    {
        $customer = $this->customerRepository->getById($customerId);
        $seller = $this->sellerFactory->create()->load($customerId, 'customer_id');
        $isSellerAdmin = $customer->getId() == $seller->getCustomerId();

        if ($isSellerAdmin) {
            $initialSeller = $this->sellerFactory->create();
            $this->saveHandlerPool->execute($seller, $initialSeller);
        }
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function initSellerAdmin()
    {
        $isCurrentUserSellerAdmin = $this->getSellerAdminPermission()->isCurrentUserSellerAdmin();
        if (!$isCurrentUserSellerAdmin && $this->getCustomerId()) {
            $this->createSellerAdmin($this->getCustomerId());
        }
    }

    /**
     * @return SellerAdminPermission
     */
    public function getSellerAdminPermission()
    {
        return $this->sellerAdminPermission;
    }

    /**
     * Checks if resource is allowed.
     *
     * @param string $resource
     * @return bool
     */
    public function isResourceAllowed($resource)
    {
        return $this->authorization->isAllowed($resource);
    }

    /**
     * Returns customer id.
     *
     * @return int|null
     */
    public function getCustomerId()
    {
        return $this->userContext->getUserId();
    }

    /**
     * Is current user seller user.
     *
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isCurrentUserSellerUser()
    {
        return $this->getCustomerSession()->isLoggedIn() && $this->sellerUserPermission->isCurrentUserSellerUser();
    }
}
