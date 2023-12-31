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
use Lof\MarketPermissions\Model\ResourceModel\Customer;
use Lof\MarketPermissions\Api\Data\SellerCustomerInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Lof\MarketPermissions\Api\Data\SellerCustomerInterface;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Lof\MarketPermissions\Api\SellerManagementInterface;
use Magento\Framework\Api\ExtensionAttributesFactory;

/**
 * Class for managing customer seller extension attributes.
 */
class SellerAttributes
{
    /**
     * @var \Lof\MarketPermissions\Api\Data\SellerCustomerInterface
     */
    private $sellerAttributes;

    /**
     * @var \Lof\MarketPermissions\Model\ResourceModel\Customer
     */
    private $customerResource;

    /**
     * @var \Lof\MarketPermissions\Api\Data\SellerCustomerInterfaceFactory
     */
    private $sellerCustomerAttributes;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var \Magento\Authorization\Model\UserContextInterface
     */
    private $userContext;

    /**
     * @var \Lof\MarketPermissions\Api\SellerManagementInterface
     */
    private $sellerManagement;

    /**
     * @var \Magento\Framework\Api\ExtensionAttributesFactory
     */
    private $extensionFactory;

    /**
     * @var bool
     */
    private $needAssignCustomer = false;

    /**
     * @var bool|null
     */
    private $sellerChange;

    /**
     * @var int
     */
    private $currentCustomerStatus;

    /**
     * @var AttributesSaver
     */
    private $attributesSaver;

    /**
     * @param Customer $customerResource
     * @param SellerCustomerInterfaceFactory $sellerCustomerAttributes
     * @param DataObjectHelper $dataObjectHelper
     * @param UserContextInterface $userContext
     * @param SellerManagementInterface $sellerManagement
     * @param ExtensionAttributesFactory $extensionFactory
     * @param AttributesSaver $attributesSaver
     */
    public function __construct(
        Customer $customerResource,
        SellerCustomerInterfaceFactory $sellerCustomerAttributes,
        DataObjectHelper $dataObjectHelper,
        UserContextInterface $userContext,
        SellerManagementInterface $sellerManagement,
        ExtensionAttributesFactory $extensionFactory,
        AttributesSaver $attributesSaver
    ) {
        $this->customerResource = $customerResource;
        $this->sellerCustomerAttributes = $sellerCustomerAttributes;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->userContext = $userContext;
        $this->sellerManagement = $sellerManagement;
        $this->extensionFactory = $extensionFactory;
        $this->attributesSaver = $attributesSaver;
    }

    /**
     * Update customer seller attributes.
     *
     * @param CustomerInterface $customer
     * @return $this
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateSellerAttributes(CustomerInterface $customer)
    {
        $this->sellerAttributes = $this->getSellerAttributesByCustomer($customer);
        $this->currentCustomerStatus = $this->getCustomerStatus($customer);
        $sellerId = $this->getSellerIdForCustomerSave($this->sellerAttributes);
        $this->checkSellerId($customer, $sellerId);
        if ($this->currentCustomerStatus !== null && $this->sellerAttributes->getStatus() === null) {
            $this->sellerAttributes->setStatus($this->currentCustomerStatus);
        }
        $this->sellerAttributes->setSellerId($sellerId);
        $this->sellerAttributes->setCustomerId($customer->getId());
        if ($this->isSellerChange(true) && $sellerId) {
            $this->checkForSellerAdmin($sellerId, $customer);
            $this->needAssignCustomer = true;
        }
        return $this;
    }

    /**
     * Checks if seller id is present for existing customer with existing seller.
     *
     * @param CustomerInterface $customer
     * @param int $sellerId
     * @return void
     * @throws CouldNotSaveException
     */
    private function checkSellerId(CustomerInterface $customer, $sellerId)
    {
        if ($customer->getId() && !$sellerId && $this->sellerManagement->getByCustomerId($customer->getId())) {
            throw new CouldNotSaveException(
                __(
                    'You cannot update the requested attribute. Row ID: %fieldName = %fieldValue.',
                    ['fieldName' => 'sellerId', 'fieldValue' => $sellerId]
                )
            );
        }
    }

    /**
     * Get seller id.
     *
     * @return int|null
     */
    public function getSellerId()
    {
        if ($this->sellerAttributes) {
            return $this->sellerAttributes->getSellerId();
        }

        return null;
    }

    /**
     * Retrieves original customer status that was before any changes were made during the script run.
     *
     * @param CustomerInterface $customer
     * @return int|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getCustomerStatus(CustomerInterface $customer)
    {
        if ($customer->getId()) {
            return $this->getOriginalsellerAttributes($customer->getId())->getStatus();
        }

        return null;
    }

    /**
     * Get seller attribute for customer.
     *
     * @param CustomerInterface $customer
     * @return SellerCustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSellerAttributesByCustomer(CustomerInterface $customer)
    {
        if ($customer->getExtensionAttributes() === null) {
            $customerExtension = $this->extensionFactory->create(CustomerInterface::class);
            $customer->setExtensionAttributes($customerExtension);
        }
        if ($customer->getExtensionAttributes()->getSellerAttributes() === null) {
            $sellerAttributes = $this->getOriginalsellerAttributes($customer->getId());
            $customer->getExtensionAttributes()->setSellerAttributes($sellerAttributes);
        }
        return $customer->getExtensionAttributes()->getSellerAttributes();
    }

    /**
     * Retrieves original attributes.
     *
     * @param int $customerId
     * @return SellerCustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getOriginalsellerAttributes($customerId)
    {
        $sellerAttributes = $this->sellerCustomerAttributes->create();
        if ($customerId) {
            $sellerAttributesArray = $this->customerResource->getCustomerExtensionAttributes($customerId);
            $this->dataObjectHelper->populateWithArray(
                $sellerAttributes,
                $sellerAttributesArray,
                SellerCustomerInterface::class
            );
        }

        return $sellerAttributes;
    }

    /**
     * Checks if a customer is a seller admin of the seller with given id.
     *
     * @param int $sellerId
     * @param CustomerInterface $customer
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return void
     */
    private function checkForSellerAdmin($sellerId, CustomerInterface $customer)
    {
        if ($sellerId && $this->sellerAttributes && $customer->getId()) {
            $seller = $this->sellerManagement->getByCustomerId($customer->getId());
            if ($seller && $seller->getCustomerId() == $customer->getId()) {
                if ($this->sellerAttributes->getSellerId() != $seller->getSellerId()) {
                    throw new CouldNotSaveException(
                        __(
                            'Invalid attribute value. Cannot change seller for a seller admin.'
                        )
                    );
                }
                if (!$this->sellerAttributes->getStatus()) {
                    throw new CouldNotSaveException(
                        __(
                            'The user %1 is the seller admin and cannot be set to inactive. '
                            . 'You must set another user as the seller admin first.',
                            $customer->getFirstname() . ' ' . $customer->getLastname()
                        )
                    );
                }
            }
        }
    }

    /**
     * Save attributes for seller.
     *
     * @param CustomerInterface $customer
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return $this
     */
    public function saveSellerAttributes(CustomerInterface $customer)
    {
        $isSellerChange = $this->isSellerChange();
        $sellerId = $this->getSellerIdForCustomerSave($this->sellerAttributes);
        $this->attributesSaver->saveAttributes(
            $customer,
            $this->sellerAttributes,
            $sellerId,
            $isSellerChange,
            $this->currentCustomerStatus
        );

        if ($this->needAssignCustomer && $isSellerChange && $sellerId > 0) {
            $this->sellerManagement->assignCustomer($sellerId, $customer->getId());
        }

        return $this;
    }

    /**
     * Checks if seller change appeared.
     *
     * @param bool $isReset [optional]
     * @return bool|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function isSellerChange($isReset = false)
    {
        if ($this->sellerChange === null || $isReset) {
            $original = $this->getOriginalsellerAttributes($this->sellerAttributes->getCustomerId());
            $this->sellerChange = $this->sellerAttributes->getSellerId() != $original->getSellerId();
        }
        return $this->sellerChange;
    }

    /**
     * Get seller Id for customer from extensionAttributes or context.
     *
     * @param SellerCustomerInterface $extensionAttributes
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getSellerIdForCustomerSave(
        SellerCustomerInterface $extensionAttributes
    ) {
        if ($extensionAttributes->getSellerId()) {
            return $extensionAttributes->getSellerId();
        }

        $contextUserId = $this->userContext->getUserId();
        if ($contextUserId !== null && $this->userContext->getUserType() == UserContextInterface::USER_TYPE_CUSTOMER) {
            $contextUserData = $this->customerResource->getCustomerExtensionAttributes($contextUserId);
            if (isset($contextUserData['seller_id'])) {
                return (int)$contextUserData['seller_id'];
            }
        }
        return 0;
    }

    /**
     * Get seller attributes.
     *
     * @param CustomerInterface $customer
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSellerAttributes(CustomerInterface $customer)
    {
        return $this->customerResource->getCustomerExtensionAttributes($customer->getId());
    }
}
