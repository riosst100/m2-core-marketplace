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
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Helper;

use Lof\MarketPlace\Model\SellerFactory;
use Lof\MarketPlace\Model\SellerProductFactory;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Helper\Context;

class Report extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var SellerProductFactory
     */
    protected $sellerProductFactory;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var Data
     */
    protected $_sellerHelper;

    /**
     * @var \Lof\MarketPlace\Model\Order
     */
    protected $_marketOrder;

    /**
     * Seller constructor.
     * @param Context $context
     * @param SellerFactory $sellerFactory
     * @param SellerProductFactory $sellerProductFactory
     * @param CustomerFactory $customerFactory
     * @param Data $sellerHelper
     * @param Session $customerSession
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        Context $context,
        SellerFactory $sellerFactory,
        SellerProductFactory $sellerProductFactory,
        CustomerFactory $customerFactory,
        Data $sellerHelper,
        Session $customerSession,
        \Lof\MarketPlace\Model\Order $marketOrder
    ) {
        parent::__construct($context);
        $this->sellerFactory = $sellerFactory;
        $this->sellerProductFactory = $sellerProductFactory;
        $this->customerFactory = $customerFactory;
        $this->customerSession = $customerSession;
        $this->_marketOrder = $marketOrder;
    }

    /**
     * @param $productId
     * @return mixed
     */
    public function getSellerIdByProduct($productId)
    {
        $seller = $this->sellerProductFactory->create()->load($productId, 'product_id');
        return $seller->getSellerId();
    }

    /**
     * @param $productId
     * @return \Lof\MarketPlace\Model\SellerProduct
     */
    public function getSellerByProduct($productId)
    {
        return $this->sellerProductFactory->create()->load($productId, 'product_id');
    }

    /**
     * @return array|mixed|null
     */
    public function getSellerByCustomer()
    {
        $seller = $this->sellerFactory->create()->load($this->getCustomerId(), 'customer_id');
        return $seller->getData();
    }

    /**
     * @param $sellerId
     * @return Customer
     */
    public function getCustomerBySeller($sellerId)
    {
        $seller = $this->sellerFactory->create()->load($sellerId, 'seller_id');
        return $this->customerFactory->create()->load($seller->getCustomerId());
    }

    /**
     * @return mixed
     */
    public function getCustomerId()
    {
        $customer = $this->customerSession->getCustomer();

        return $customer->getId();
    }

    /**
     * @param $country
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function checkCountry($country)
    {
        $availableCountries = $this->_sellerHelper->getConfig('available_countries/available_countries');
        $enableAvailableCountries = $this->_sellerHelper->getConfig('available_countries/enable_available_countries');
        if ($enableAvailableCountries == '1' && $availableCountries) {
            $availableCountries = explode(',', $availableCountries);
            if (!in_array($country, $availableCountries)) {
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

    /**
     * @param $sellerGroup
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function checkSellerGroup($sellerGroup)
    {
        $enableSellerGroup = $this->_sellerHelper->getConfig('group_seller/enable_group_seller');
        $availableSellerGroup = $this->_sellerHelper->getConfig('group_seller/group_seller');
        if ($enableSellerGroup == '1' && $availableSellerGroup) {
            $availableSellerGroup = explode(',', $availableSellerGroup);
            if (!in_array($sellerGroup, $availableSellerGroup)) {
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

    /**
     * @param $sellerUrl
     * @return bool
     */
    public function checkSellerUrl($sellerUrl)
    {
        $collection = $this->sellerFactory->create()->getCollection();
        $collection->addFieldToFilter('url_key', $sellerUrl);
        if ($collection->getData()) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param $customerId
     * @return bool
     */
    public function checkSellerExist($customerId)
    {
        $collection = $this->sellerFactory->create()->getCollection();
        $collection->addFieldToFilter('customer_id', $customerId);
        if ($collection->getData()) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @return int
     */
    public function getTotalOrders($sellerId)
    {
        $total = $this->_marketOrder->getCollection()
            ->addFieldToFilter('seller_id', $sellerId)
            ->getSize();

        return $total ?: 0;
    }

    /**
     * @return int
     */
    public function getTotalCompletedOrder($sellerId)
    {
        $total = $this->_marketOrder->getCollection()
            ->addFieldToFilter('seller_id', $sellerId)
            ->addFieldToFilter('status', 'complete')
            ->getSize();

        return $total ?: 0;
    }

    /**
     * @param $sellerId
     * @return int
     */
    public function getTotalCompletedOrders($sellerId)
    {
        return $this->getTotalCompletedOrder($sellerId);
    }

    /**
     * @return int
     */
    public function getTotalProduct($sellerId)
    {
        $total = $this->sellerProductFactory->create()->getCollection()
            ->addFieldToFilter('seller_id', $sellerId)
            ->getSize();

        return $total ?: 0;
    }
}
