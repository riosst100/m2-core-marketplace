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

namespace Lof\MarketPermissions\Model\Action\Customer;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class for populating customer object.
 */
class Populator
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var CustomerInterfaceFactory
     */
    private $customerFactory;

    /**
     * @var DataObjectHelper
     */
    private $objectHelper;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerInterfaceFactory $customerFactory
     * @param DataObjectHelper $objectHelper
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        CustomerInterfaceFactory $customerFactory,
        DataObjectHelper $objectHelper,
        StoreManagerInterface $storeManager
    ) {
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
        $this->objectHelper = $objectHelper;
        $this->storeManager = $storeManager;
    }

    /**
     * Populate customer.
     *
     * @param array $data
     * @param CustomerInterface $customer [optional]
     * @return CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function populate(array $data, CustomerInterface $customer = null)
    {
        if ($customer === null) {
            $customer = $this->customerFactory->create();
            $actionId = 'customer_account_edit-';
        } else {
            $actionId = 'customer_account_create-';
        }

        $customerId = $customer->getId();
        $data = $this->populateDateAttributeDataKey($actionId, $data);
        $this->objectHelper->populateWithArray(
            $customer,
            $data,
            \Magento\Customer\Api\Data\CustomerInterface::class
        );
        $customer->setWebsiteId($this->storeManager->getWebsite()->getId());
        $customer->setStoreId($this->storeManager->getStore()->getId());
        $customer->setId($customerId);

        return $customer;
    }

    /**
     * Populate date attribute data key
     *
     * @param string $actionId
     * @param array $data
     * @return array
     */
    private function populateDateAttributeDataKey(string $actionId, array $data): array
    {
        $dataKeys = preg_grep('/' . $actionId . '/', array_keys($data));
        if ($dataKeys) {
            foreach ($dataKeys as $key) {
                if (!empty($data[$key])) {
                    $dataStringArr = explode($actionId, $key);
                    $customAttributeKey = $dataStringArr[count($dataStringArr) - 1];
                    $data[$customAttributeKey] = $data[$key];
                    unset($data[$key]);
                }
            }
        }
        return $data;
    }
}
