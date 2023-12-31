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

use Lof\MarketPermissions\Api\Data\SellerInterfaceFactory;
use Magento\Customer\Api\Data\CustomerInterface;
use Lof\MarketPermissions\Api\Data\SellerCustomerInterface;
use Lof\MarketPlace\Helper\Data;

/**
 * Class for creating new seller for customer.
 */
class Seller
{
    /**
     * @var SellerInterfaceFactory
     */
    private $sellerFactory;

    /**
     * @var SellerCustomerInterface
     */
    private $customerAttributes;

    /**
     * @var Data
     */
    protected $_dataHelper;

    /**
     * @param SellerInterfaceFactory $sellerFactory
     * @param SellerCustomerInterface $customerAttributes
     */
    public function __construct(
        SellerInterfaceFactory $sellerFactory,
        SellerCustomerInterface $customerAttributes,
        Data $dataHelper
    ) {

        $this->sellerFactory = $sellerFactory;
        $this->customerAttributes = $customerAttributes;
        $this->_dataHelper = $dataHelper;
    }

    /**
     * Create seller.
     * @param CustomerInterface $customer
     * @param array $sellerData
     * @param null $jobTitle
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     */
    public function createSeller(CustomerInterface $customer, array $sellerData, $jobTitle = null)
    {
        $sellerDataObject = $this->sellerFactory->create(['data' => $sellerData]);
        $sellerDataObject->setCustomerId($customer->getId());
        if (isset($sellerData['group']) && $sellerData['group']) {
            $group = (int)$sellerData['group'];
        } else {
            $group = (int)$this->_dataHelper->getConfig('seller_settings/default_seller_group');
        }
        $sellerDataObject->setGroupId($group);

        $this->customerAttributes
            ->setSellerId($sellerDataObject->getSellerId())
            ->setCustomerId($customer->getId());
        if ($jobTitle) {
            $this->customerAttributes->setJobTitle($jobTitle);
        }
        return $sellerDataObject;
    }
}
