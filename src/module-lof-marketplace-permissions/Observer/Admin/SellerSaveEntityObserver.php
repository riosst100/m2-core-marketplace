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

namespace Lof\MarketPermissions\Observer\Admin;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SellerSaveEntityObserver implements ObserverInterface
{

    /**
     * @var \Lof\MarketPlace\Model\Seller
     */
    protected $sellerModel;

    /**
     * @var \Lof\MarketPermissions\Model\SellerContext
     */
    private $sellerContext;

    /**
     * @var \Lof\MarketPermissions\Model\ResourceModel\Customer\CollectionFactory
     */
    private $customerCollectionFactory;

    /**
     * @var \Lof\MarketPermissions\Model\ResourceModel\Customer
     */
    protected $resourceModel;

    /**
     * @var \Lof\MarketPermissions\Model\ResourceModel\CustomerFactory
     */
    protected $customerFactory;

    /**
     * SellerRegisterSuccessObserver constructor.
     * @param \Lof\MarketPlace\Model\Seller $sellerModel
     * @param \Lof\MarketPermissions\Model\SellerContext $sellerContext
     * @param \Lof\MarketPermissions\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory
     * @param \Lof\MarketPermissions\Model\ResourceModel\CustomerFactory $customerFactory
     * @param \Lof\MarketPermissions\Model\ResourceModel\Customer $resourceModel
     */
    public function __construct(
        \Lof\MarketPlace\Model\Seller $sellerModel,
        \Lof\MarketPermissions\Model\SellerContext $sellerContext,
        \Lof\MarketPermissions\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory
    ) {
        $this->sellerModel = $sellerModel;
        $this->sellerContext = $sellerContext;
        $this->customerCollectionFactory = $customerCollectionFactory;
    }

    /**
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        if (!$this->sellerContext->isModuleActive()) {
            return;
        }
        $seller = $observer->getSeller();
        //$model = $observer->getEvent()->getDataObject();
        if ($seller instanceof \Lof\MarketPlace\Model\Seller) {
            $isCurrentUserSellerAdmin = $this->isCurrentUserSellerAdmin($seller->getCustomerId(), $seller->getId());
            if (!$isCurrentUserSellerAdmin) {
                $this->sellerContext->createSellerAdmin($seller->getCustomerId());
            }
        }
    }

    /**
     * check is current user seller have admin account or not
     *
     * @param int $customerId
     * @param int $sellerId
     * @return bool
     */
    protected function isCurrentUserSellerAdmin($customerId, $sellerId)
    {
        $flag = true;
        $collection = $this->customerCollectionFactory->create()->addFieldToFilter("customer_id", $customerId);
        $foundItem = $collection->getFirstItem();
        if ($foundItem && $foundItem->getSellerId() == 0 && $sellerId) {
            $flag = false;
        }
        return $flag;
    }
}
