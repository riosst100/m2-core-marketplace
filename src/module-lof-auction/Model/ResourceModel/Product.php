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

use Lof\Auction\Model\ResourceModel\Amount\CollectionFactory as AmountCollection;
use Lof\Auction\Model\ResourceModel\MixAmount\CollectionFactory as MixAmountCollection;
use Lof\Auction\Model\ResourceModel\AutoAuction\CollectionFactory as AutoCollection;
use Lof\Auction\Model\ResourceModel\WinnerData\CollectionFactory as WinnerCollection;
use Lof\Auction\Model\ResourceModel\History\CollectionFactory as HistoryCollection;
use Lof\Auction\Model\ResourceModel\SubscriberAddress\CollectionFactory as SubscriberCollection;
use Magento\Framework\DataObject;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Product
 * @package Lof\Auction\Model\ResourceModel
 */
class Product extends AbstractDb
{
    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var AmountCollection
     */
    private $amountCollectionFactory;
    /**
     * @var AutoCollection
     */
    private $autoCollectionFactory;
    /**
     * @var MixAmountCollection
     */
    private $mixAmountCollectionFactory;
    /**
     * @var HistoryCollection
     */
    private $historyCollectionFactory;
    /**
     * @var WinerCollection
     */
    private $winnerCollectionFactory;
    /**
     * @var SubscriberCollection
     */
    private $subscriberCollectionFactory;

    /**
     * Construct
     *
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param AmountCollection $amountCollection
     * @param AutoCollection $autoCollection
     * @param HistoryCollection $historyCollection
     * @param WinnerCollection $winnerCollection
     * @param MixAmountCollection $mixAmountCollection
     * @param SubscriberCollection $subscriberCollection
     * @param string $connectionName
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        AmountCollection $amountCollection,
        AutoCollection $autoCollection,
        HistoryCollection $historyCollection,
        WinnerCollection $winnerCollection,
        MixAmountCollection $mixAmountCollection,
        SubscriberCollection $subscriberCollection,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->_storeManager = $storeManager;
        $this->amountCollectionFactory = $amountCollection;
        $this->autoCollectionFactory = $autoCollection;
        $this->historyCollectionFactory = $historyCollection;
        $this->winnerCollectionFactory = $winnerCollection;
        $this->mixAmountCollectionFactory = $mixAmountCollection;
        $this->subscriberCollectionFactory = $subscriberCollection;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('lof_auction_product', 'entity_id');
    }


    /**
     * @param AbstractModel $object
     * @return Product|void
     */
    public function _afterDelete(AbstractModel $object)
    {
        $amountCollection = $this->amountCollectionFactory->create()
            ->addFieldToFilter('auction_id', $object->getId());
        $amountCollection->walk('delete');

        $autoCollection = $this->autoCollectionFactory->create()
            ->addFieldToFilter('auction_id', $object->getId());
        $autoCollection->walk('delete');

        $historyCollection = $this->historyCollectionFactory->create()
            ->addFieldToFilter('auction_id', $object->getId());
        $historyCollection->walk('delete');

        $winnerCollection = $this->winnerCollectionFactory->create()
            ->addFieldToFilter('auction_id', $object->getId());
        $winnerCollection->walk('delete');

        $mixAmountCollection = $this->mixAmountCollectionFactory->create()
            ->addFieldToFilter('auction_id', $object->getId());
        $mixAmountCollection->walk('delete');

        $subscriberCollection = $this->subscriberCollectionFactory->create()
            ->addFieldToFilter('auction_id', $object->getId());
        $subscriberCollection->walk('delete');
    }
}
