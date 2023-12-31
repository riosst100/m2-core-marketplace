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
 * @package    Lof_Auction
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\Auction\Setup;

use Exception;
use Lof\Auction\Model\MixAmountFactory;
use Lof\Auction\Model\ResourceModel\Amount\CollectionFactory;
use Lof\Auction\Model\ResourceModel\AutoAuction\CollectionFactory as AutoCollectionFactory;
use Lof\Auction\Model\ResourceModel\SubscriberAddress\CollectionFactory as SubscriberCollectionFactory;
use Lof\Auction\Model\ResourceModel\MixAmount\CollectionFactory as MixAmountCollectionFactory;
use Lof\Auction\Model\SubscriberAddressFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

/**
 * Class UpgradeData
 *
 * @package Lof\Auction\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var CollectionFactory
     */
    private $amountCollection;
    /**
     * @var SubscriberAddressFactory
     */
    private $subscriberFactory;
    /**
     * @var CustomerFactory
     */
    private $customerFactory;
    /**
     * @var AutoCollectionFactory
     */
    private $autoCollectionFactory;
    /**
     * @var SubscriberCollectionFactory
     */
    private $subscriberCollectionFactory;
    /**
     * @var MixAmountCollectionFactory
     */
    private $mixAmountCollectionFactory;
    /**
     * @var MixAmountFactory
     */
    private $mixAmountFactory;

    /**
     * UpgradeData constructor.
     * @param CollectionFactory $collectionFactory
     * @param SubscriberAddressFactory $subscriberAddressFactory
     * @param CustomerFactory $customerFactory
     * @param AutoCollectionFactory $autoCollectionFactory
     * @param SubscriberCollectionFactory $subscriberCollectionFactory
     * @param MixAmountCollectionFactory $mixAmountCollectionFactory
     * @param MixAmountFactory $mixAmountFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        SubscriberAddressFactory $subscriberAddressFactory,
        CustomerFactory $customerFactory,
        AutoCollectionFactory $autoCollectionFactory,
        SubscriberCollectionFactory $subscriberCollectionFactory,
        MixAmountCollectionFactory $mixAmountCollectionFactory,
        MixAmountFactory $mixAmountFactory
    ) {
        $this->amountCollection = $collectionFactory;
        $this->subscriberFactory = $subscriberAddressFactory;
        $this->customerFactory = $customerFactory;
        $this->autoCollectionFactory = $autoCollectionFactory;
        $this->subscriberCollectionFactory = $subscriberCollectionFactory;
        $this->mixAmountCollectionFactory = $mixAmountCollectionFactory;
        $this->mixAmountFactory = $mixAmountFactory;
    }


    /**
     * Upgrade data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     * @throws Exception
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $this->updateData();
        }
    }

    /**
     * @throws Exception
     */
    public function updateData()
    {
        $amountCollection = $this->amountCollection->create();
        $autoCollection = $this->autoCollectionFactory->create();
        $this->setSubscriberData($amountCollection);
        $this->setSubscriberData($autoCollection);
    }

    /**
     * @param $collection
     * @throws Exception
     */
    public function setSubscriberData($collection)
    {
        foreach ($collection as $item) {
            $data['auction_id'] = $item->getData('auction_id');
            $customerId = $item->getData('customer_id');
            $data['customer_id'] = $customerId;
            $customer = $this->customerFactory->create()->load($customerId);
            $data['customer_email'] = $customer->getEmail();
            $subscriber = $this->subscriberCollectionFactory->create();
            $subscriber->addFieldToFilter('auction_id', $data['auction_id'])
                ->addFieldToFilter('customer_id', $data['customer_id']);
            if (!$subscriber->getData()) {
                $this->subscriberFactory->create()->setData($data)->save();
            }
            $data['product_id'] = $item->getProductId();
            $data['created_at'] = $item->getCreatedAt();
            $data['bid_id'] = $item->getEntityId();
            $mixCollection = $this->mixAmountCollectionFactory->create();
            $mixCollection->addFieldToFilter('auction_id', $data['auction_id'])
                ->addFieldToFilter('customer_id', $data['customer_id']);
            $mixAmount = $mixCollection->getFirstItem();
            if ($item->getAuctionAmount()) {
                $data['amount'] = $item->getAuctionAmount();
                $data['is_auto'] = 0;
            } else {
                $data['amount'] = $item->getAmount();
                $data['is_auto'] = 1;
            }
            if (!$mixAmount->getData()) {
                $mixAmount = $this->mixAmountFactory->create();
            } elseif ($mixAmount->getAmount() >= $data['amount']) {
                return;
            }
            $mixAmount->setData($data);
            $mixAmount->save();
        }
    }
}
