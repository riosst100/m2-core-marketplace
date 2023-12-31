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

namespace Lof\MarketPermissions\Plugin\Customer\Api\CustomerRepository;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Lof\MarketPermissions\Api\Data\SellerCustomerInterfaceFactory as SellerCustomerExtension;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\CouldNotDeleteException;
use Lof\MarketPermissions\Model\Customer\sellerAttributes;
use Lof\MarketPermissions\Model\Seller\Structure;
use Lof\MarketPermissions\Api\SellerRepositoryInterface;

/**
 * A plugin for customer delete operation for processing seller routines.
 */
class Delete
{
    /**
     * @var \Lof\MarketPermissions\Api\Data\SellerCustomerInterfaceFactory
     */
    private $sellerCustomerAttributes;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var \Lof\MarketPermissions\Model\Customer\sellerAttributes
     */
    private $customerSaveAttributes;

    /**
     * @var \Lof\MarketPermissions\Model\Seller\Structure
     */
    private $sellerStructure;

    /**
     * @var \Lof\MarketPermissions\Api\SellerRepositoryInterface
     */
    private $sellerRepository;

    /**
     * @param SellerCustomerExtension $sellerCustomerAttributes
     * @param DataObjectHelper $dataObjectHelper
     * @param sellerAttributes $customerSaveAttributes
     * @param Structure $sellerStructure
     * @param SellerRepositoryInterface $sellerRepository
     */
    public function __construct(
        SellerCustomerExtension $sellerCustomerAttributes,
        DataObjectHelper $dataObjectHelper,
        sellerAttributes $customerSaveAttributes,
        Structure $sellerStructure,
        SellerRepositoryInterface $sellerRepository
    ) {
        $this->sellerCustomerAttributes = $sellerCustomerAttributes;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->customerSaveAttributes = $customerSaveAttributes;
        $this->sellerStructure = $sellerStructure;
        $this->sellerRepository = $sellerRepository;
    }

    /**
     * Around delete.
     *
     * @param CustomerRepositoryInterface $subject
     * @param \Closure $proceed
     * @param CustomerInterface $customer
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundDelete(
        CustomerRepositoryInterface $subject,
        \Closure $proceed,
        CustomerInterface $customer
    ) {
        $this->checkIsSuperUser($customer);
        $deleteResult = $proceed($customer);
        $this->rebuildSellerStructure($customer);

        return $deleteResult;
    }

    /**
     * Around delete by customer id.
     *
     * @param CustomerRepositoryInterface $subject
     * @param \Closure $proceed
     * @param int $customerId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws CouldNotDeleteException
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundDeleteById(
        CustomerRepositoryInterface $subject,
        \Closure $proceed,
        $customerId
    ) {
        $customer = $subject->getById($customerId);
        $this->checkIsSuperUser($customer);
        $deleteResult = $proceed($customerId);
        $this->rebuildSellerStructure($customer);

        return $deleteResult;
    }

    /**
     * Rebuild seller structure.
     *
     * @param CustomerInterface $customer
     * @return void
     */
    private function rebuildSellerStructure(CustomerInterface $customer)
    {
        $this->sellerStructure->moveStructureChildrenToParent($customer->getId());
        $this->sellerStructure->removeCustomerNode($customer->getId());
    }

    /**
     * Checks if customer is super user of a seller.
     *
     * @param CustomerInterface $customer
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    private function checkIsSuperUser(CustomerInterface $customer)
    {
        $sellerAttributes = $this->getSellerAttributes($customer);
        if ($sellerAttributes && $sellerAttributes->getSellerId()) {
            $seller = $this->sellerRepository->get($sellerAttributes->getSellerId());
            if ($seller->getCustomerId() == $customer->getId()) {
                throw new \Magento\Framework\Exception\CouldNotDeleteException(
                    __(
                        'Cannot delete the seller admin. Delete operation has been stopped. '
                        . 'Please repeat the action for the other customers.'
                    )
                );
            }
        }
    }

    /**
     * Get seller attributes.
     *
     * @param CustomerInterface $customer
     * @return \Lof\MarketPermissions\Api\Data\SellerCustomerInterface
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    private function getSellerAttributes(CustomerInterface $customer)
    {
        try {
            $sellerAttributesArray = $this->customerSaveAttributes->getSellerAttributes($customer);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotDeleteException(
                __('Something went wrong')
            );
        }
        if (!$sellerAttributesArray) {
            return null;
        }
        $sellerAttributes = $this->sellerCustomerAttributes->create();
        $this->dataObjectHelper->populateWithArray(
            $sellerAttributes,
            $sellerAttributesArray,
            \Lof\MarketPermissions\Api\Data\SellerCustomerInterface::class
        );
        return $sellerAttributes;
    }
}
