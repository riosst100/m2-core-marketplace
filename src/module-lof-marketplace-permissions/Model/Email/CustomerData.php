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

namespace Lof\MarketPermissions\Model\Email;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\CustomerNameGenerationInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Lof\MarketPermissions\Api\SellerRepositoryInterface;
use Magento\User\Api\Data\UserInterfaceFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * Class for getting customer data when sending emails.
 */
class CustomerData
{
    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    private $dataProcessor;

    /**
     * @var \Magento\Customer\Api\CustomerNameGenerationInterface
     */
    private $customerViewHelper;

    /**
     * @var \Lof\MarketPermissions\Api\SellerRepositoryInterface
     */
    private $sellerRepository;

    /**
     * Admin user model factory for creating an admin user model and getting its data through it.
     *
     * @var \Magento\User\Api\Data\UserInterfaceFactory
     */
    private $userFactory;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @param DataObjectProcessor $dataProcessor
     * @param CustomerNameGenerationInterface $customerViewHelper
     * @param SellerRepositoryInterface $sellerRepository
     * @param UserInterfaceFactory $userFactory
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        DataObjectProcessor $dataProcessor,
        CustomerNameGenerationInterface $customerViewHelper,
        SellerRepositoryInterface $sellerRepository,
        UserInterfaceFactory $userFactory,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->dataProcessor = $dataProcessor;
        $this->customerViewHelper = $customerViewHelper;
        $this->sellerRepository = $sellerRepository;
        $this->userFactory = $userFactory;
        $this->customerRepository = $customerRepository;
    }

    /**
     * Create an object with data merged from Customer, CustomerSecure and seller.
     *
     * @param CustomerInterface $customer
     * @param int $sellerId [optional]
     * @return \Magento\Framework\DataObject|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getDataObjectByCustomer(CustomerInterface $customer, $sellerId = null)
    {
        $mergedCustomerData = null;
        $customerData = $this->dataProcessor
            ->buildOutputDataArray($customer, \Magento\Customer\Api\Data\CustomerInterface::class);
        $mergedCustomerData = new \Magento\Framework\DataObject($customerData);
        $mergedCustomerData->setData('name', $this->customerViewHelper->getCustomerName($customer));
        if ($sellerId !== null) {
            $seller = $this->sellerRepository->get((int)$sellerId);
            $mergedCustomerData->setData('sellerName', $seller->getName());
        }
        return $mergedCustomerData;
    }

    /**
     * Gets data object of seller admin.
     *
     * @param int $sellerId
     * @return \Magento\Framework\DataObject|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getDataObjectSuperUser($sellerId)
    {
        $seller = $this->sellerRepository->get($sellerId);
        $sellerAdmin = $this->customerRepository->getById($seller->getCustomerId());
        return $this->getDataObjectByCustomer($sellerAdmin, $sellerId);
    }

    /**
     * Gets data object of seller sales representative.
     *
     * @param int $sellerId
     * @param int $salesRepresentativeId
     * @return \Magento\Framework\DataObject|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getDataObjectSalesRepresentative($sellerId, $salesRepresentativeId)
    {
        $mergedCustomerData = null;
        if ($sellerId && $salesRepresentativeId) {
            $seller = $this->sellerRepository->get((int)$sellerId);
            /** @var \Magento\User\Model\User $user */
            $user = $this->userFactory->create()->load($salesRepresentativeId);

            $customerData = $this->dataProcessor
                ->buildOutputDataArray($user, \Magento\User\Api\Data\UserInterface::class);
            $mergedCustomerData = new \Magento\Framework\DataObject($customerData);
            $mergedCustomerData->setData('name', $user->getName());
            $mergedCustomerData->setData('sellerName', $seller->getName());
        }

        return $mergedCustomerData;
    }
}
