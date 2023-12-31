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

use Lof\MarketPermissions\Api\AclInterface;
use Lof\MarketPermissions\Api\SellerManagementInterface;
use Lof\MarketPermissions\Api\SellerRepositoryInterface;
use Magento\User\Api\Data\UserInterfaceFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Lof\MarketPermissions\Model\ResourceModel\Customer as CustomerResource;
use Psr\Log\LoggerInterface as PsrLogger;
use Lof\MarketPermissions\Model\Email\Sender;

/**
 * Handle various customer account actions.
 */
class SellerManagement implements SellerManagementInterface
{
    /**
     * @var \Lof\MarketPermissions\Api\SellerRepositoryInterface
     */
    private $sellerRepository;

    /**
     * User model factory.
     *
     * @var \Magento\User\Api\Data\UserInterfaceFactory
     */
    private $userFactory;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Lof\MarketPermissions\Model\ResourceModel\Customer
     */
    private $customerResource;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Lof\MarketPermissions\Model\Email\Sender
     */
    private $sellerEmailSender;

    /**
     * @var AclInterface
     */
    private $userRoleManagement;

    /**
     * @param SellerRepositoryInterface $sellerRepository
     * @param UserInterfaceFactory $userFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerResource $customerResource
     * @param PsrLogger $logger
     * @param Sender $sellerEmailSender
     * @param AclInterface $userRoleManagement
     */
    public function __construct(
        SellerRepositoryInterface $sellerRepository,
        UserInterfaceFactory $userFactory,
        CustomerRepositoryInterface $customerRepository,
        CustomerResource $customerResource,
        PsrLogger $logger,
        Sender $sellerEmailSender,
        AclInterface $userRoleManagement
    ) {
        $this->sellerRepository = $sellerRepository;
        $this->userFactory = $userFactory;
        $this->customerRepository = $customerRepository;
        $this->customerResource = $customerResource;
        $this->logger = $logger;
        $this->sellerEmailSender = $sellerEmailSender;
        $this->userRoleManagement = $userRoleManagement;
    }

    /**
     * @inheritdoc
     */
    public function getSalesRepresentative($userId)
    {
        $salesRepresentative = '';
        if ($userId) {
            /** @var \Magento\User\Model\User $model */
            $model = $this->userFactory->create();
            $model->load($userId);
            $salesRepresentative = trim($model->getFirstName() . ' ' . $model->getLastName());
        }
        return $salesRepresentative;
    }

    /**
     * @inheritdoc
     */
    public function getByCustomerId($customerId)
    {
        $seller = null;
        $customer = $this->customerRepository->getById($customerId);
        if ($customer->getExtensionAttributes() !== null
            && $customer->getExtensionAttributes()->getSellerAttributes() !== null
            && $customer->getExtensionAttributes()->getSellerAttributes()->getSellerId()
        ) {
            $sellerAttributes = $customer->getExtensionAttributes()->getSellerAttributes();
            try {
                $seller = $this->sellerRepository->get($sellerAttributes->getSellerId());
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                //If seller is not found - just return null
            }
        }
        return $seller;
    }

    /**
     * @inheritdoc
     */
    public function getAdminBySellerId($sellerId)
    {
        $sellerAdmin = null;

        try {
            $seller = $this->sellerRepository->get($sellerId);
            $sellerAdmin = $this->customerRepository->getById($seller->getCustomerId());
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->logger->critical($e);
        }

        return $sellerAdmin;
    }

    /**
     * {@inheritdoc}
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function assignCustomer($sellerId, $customerId)
    {
        $customer = $this->customerRepository->getById($customerId);
        if ($customer->getExtensionAttributes() !== null
            && $customer->getExtensionAttributes()->getSellerAttributes() !== null) {
            $sellerAttributes = $customer->getExtensionAttributes()->getSellerAttributes();
            $sellerAttributes->setCustomerId($customerId);
            $sellerAttributes->setSellerId($sellerId);
            $this->customerResource->saveAdvancedCustomAttributes($sellerAttributes);
            $seller = $this->sellerRepository->get($sellerId);
            if ($customer->getId() != $seller->getCustomerId()) {
                $this->userRoleManagement->assignUserDefaultRole($customerId, $sellerId);
                $this->sellerEmailSender->sendCustomerSellerAssignNotificationEmail($customer, $sellerId);
            }
        }
    }
}
