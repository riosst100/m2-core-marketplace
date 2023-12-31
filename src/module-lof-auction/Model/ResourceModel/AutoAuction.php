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


namespace Lof\Auction\Model\ResourceModel;

use Exception;
use Lof\Auction\Helper\Data;
use Lof\Auction\Model\HistoryFactory;
use Lof\Auction\Model\MixAmountFactory;
use Lof\Auction\Model\ResourceModel\MixAmount\CollectionFactory as MixAmountCollectionFactory;
use Lof\Auction\Model\ResourceModel\SubscriberAddress\CollectionFactory;
use Lof\Auction\Model\SubscriberAddressFactory;
use Lof\Auction\Model\ProductFactory as AuctionFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class AutoAuction
 * @package Lof\Auction\Model\ResourceModel
 */
class AutoAuction extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var SubscriberAddressFactory
     */
    private $subscriberFactory;
    /**
     * @var CustomerFactory
     */
    private $customerFactory;
    /**
     * @var CollectionFactory
     */
    private $collectionSubscriber;
    /**
     * @var MixAmountCollectionFactory
     */
    private $mixAmountCollectionFactory;
    /**
     * @var MixAmountFactory
     */
    private $mixAmountFactory;
    /**
     * @var Data
     */
    private $helper;
    /**
     * @var HistoryFactory
     */
    private $historyFactory;
    /**
     * @var AuctionFactory
     */
    private $auctionFactory;

    /**
     * Construct
     *
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param SubscriberAddressFactory $subscriberAddress
     * @param CustomerFactory $customerFactory
     * @param CollectionFactory $collectionFactory
     * @param MixAmountCollectionFactory $mixAmountCollection
     * @param MixAmountFactory $mixAmountFactory
     * @param HistoryFactory $historyFactory
     * @param AuctionFactory $auctionFactory
     * @param Data $helper
     * @param string $connectionName
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        SubscriberAddressFactory $subscriberAddress,
        CustomerFactory $customerFactory,
        CollectionFactory $collectionFactory,
        MixAmountCollectionFactory $mixAmountCollection,
        MixAmountFactory $mixAmountFactory,
        HistoryFactory $historyFactory,
        AuctionFactory $auctionFactory,
        Data $helper,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->_storeManager = $storeManager;
        $this->subscriberFactory = $subscriberAddress;
        $this->customerFactory = $customerFactory;
        $this->collectionSubscriber = $collectionFactory;
        $this->mixAmountCollectionFactory = $mixAmountCollection;
        $this->mixAmountFactory = $mixAmountFactory;
        $this->historyFactory = $historyFactory;
        $this->auctionFactory = $auctionFactory;
        $this->helper = $helper;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('lof_auto_auction', 'entity_id');
    }

    /**
     * @param AbstractModel $object
     * @return AutoAuction
     * @throws Exception
     */
    public function _afterSave(AbstractModel $object)
    {
        $this->saveMixAmount($object);
        $customer_email = "";
        if($object->getCustomerId()){
            $customer = $this->customerFactory->create()->load((int)$object->getCustomerId());
            if($customer && $customer->getId()){
                $customer_email = $customer->getEmail();
            }
        }
        $this->subscriberFactory->create()->processSubscriber($object, $customer_email);
        return parent::_afterSave($object);
    }


    /**
     * @param AbstractModel $object
     * @return AutoAuction|void
     */
    public function _afterDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        $historyCollection = $this->historyFactory->create()->getCollection();
        $historyCollection->addFieldToFilter('amount_id', $object->getId())
            ->addFieldToFilter('customer_id', $object->getCustomerId())
            ->addFieldToFilter('is_auto', 1);
        $historyCollection->walk('delete');

        $auctionId = $object->getAuctionId();
        $customerId = $object->getCustomerId();
        $mix = $this->mixAmountCollectionFactory->create()
            ->addFieldToFilter('auction_id', $auctionId)
            ->addFieldToFilter('customer_id', $customerId)
            ->getFirstItem();
        $amount = $this->helper->getCurrentAmount($auctionId, $customerId);
        if ($amount) {
            $mix->setAmount($amount);
            $mix->save();
        } else {
            $mix->delete();
        }
        $auction = $this->auctionFactory->create()->load($auctionId);
        if ($auction->getData()) {
            $bestAmount = $this->helper->getBestCurrentBid($auctionId);
            if ($bestAmount->getAuctionAmount()) {
                $auction->setMinAmount($bestAmount->getAuctionAmount());
                $auction->setCustomerId($bestAmount->getCustomerId());
                $auction->save();
            } else {
                $auction->setMinAmount($auction->getStartingPrice())
                    ->setCustomerId(0);
                $auction->save();
            }
        }
    }

    /**
     * @param $object
     * @throws Exception
     */
    public function saveMixAmount($object)
    {
        $data['auction_id'] = $object->getData('auction_id');
        $customerId = $object->getData('customer_id');
        $data['customer_id'] = $customerId;
        $mixCollection = $this->mixAmountCollectionFactory->create();
        $mixCollection->addFieldToFilter('auction_id', $data['auction_id'])
            ->addFieldToFilter('customer_id', $customerId);
        $mixAmount = $mixCollection->getFirstItem();
        $data['amount'] = $object->getAmount();
        $data['bid_id'] = $object->getEntityId();
        $data['product_id'] = $object->getProductId();
        if (!$mixAmount->getData()) {
            $mixAmount = $this->mixAmountFactory->create();
            $mixAmount->setData($data);
            $mixAmount->setIsSpam(0)->save();
        } elseif ($mixAmount->getAmount() < $data['amount']) {
            $mixAmount->setAmount($data['amount'])
                ->setIsAuto(1)
                ->setIsSpam(0)
                ->setBidId($data['bid_id']);
            $mixAmount->save();
        }
    }
}
