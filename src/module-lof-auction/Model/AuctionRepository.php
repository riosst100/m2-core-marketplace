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
namespace Lof\Auction\Model;

use Lof\Auction\Api\Data\EndAuctionInterfaceFactory;
use Lof\Auction\Api\Data\WattingUserInterfaceFactory;
use Lof\Auction\Api\Data\WinnerInterfaceFactory;
use Lof\Auction\Api\AuctionRepositoryInterface;
use Lof\Auction\Api\Data\ProductSearchResultsInterfaceFactory as DataSearchResultsInterfaceFactory;
use Lof\Auction\Api\Data\ProductInterfaceFactory as DataInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Lof\Auction\Model\ResourceModel\Product as ResourceProduct;
use Lof\Auction\Model\ResourceModel\Product\CollectionFactory as ModelCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Lof\Auction\Model\ResourceModel\Product\Source\AuctionStatus;
use Magento\Catalog\Api\ProductRepositoryInterface as CoreProductRepositoryInterface;
use Lof\Auction\Model\AmountFactory;
use Lof\Auction\Model\AutoAuctionFactory;
use Lof\Auction\Helper\Data as AuctionData;

/**
 * Class AuctionRepository
 * @package Lof\Auction\Model
 */
class AuctionRepository implements AuctionRepositoryInterface
{

    /**
     * @var ResourceProduct
     */
    protected $resource;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var ModelCollectionFactory
     */
    protected $modelCollectionFactory;

    /**
     * @var DataSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var DataInterfaceFactory
     */
    protected $dataApiFactory;

    /**
     * @var
     */
    protected $extensionAttributesJoinProcessor;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var ResourceConnection
     */
    protected $_resource;

    /**
     * @var CoreProductRepositoryInterface
     */
    protected $coreProductRepository;

    /**
     * @var AmountFactory
     */
    protected $amountFactory;

    /**
     * @var AutoAuctionFactory
     */
    protected $autoAuctionFactory;

    /**
     * @var AuctionData
     */
    protected $helperData;

    /**
     * @var EndAuctionInterfaceFactory
     */
    protected $endAuctionFactory;

    /**
     * @var WattingUserInterfaceFactory
     */
    protected $wattingUserFactory;

    /**
     * @var WinnerInterfaceFactory
     */
    protected $winnerFactory;

    /**
     * @var mixed
     */
    protected $auctionsBySku = [];

    /**
     * @param ResourceProduct $resource
     * @param ProductFactory $productFactory
     * @param DataInterfaceFactory $dataApiFactory
     * @param ModelCollectionFactory $modelCollectionFactory
     * @param DataSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param ResourceConnection $resourceConnection
     * @param CoreProductRepositoryInterface $coreProductRepository
     * @param AmountFactory $amountFactory
     * @param AutoAuctionFactory $autoAuctionFactory
     * @param AuctionData $helperData
     * @param EndAuctionInterfaceFactory $endAuctionFactory
     * @param WattingUserInterfaceFactory $wattingUserFactory
     * @param WinnerInterfaceFactory $winnerFactory
     */
    public function __construct(
        ResourceProduct $resource,
        ProductFactory $productFactory,
        DataInterfaceFactory $dataApiFactory,
        ModelCollectionFactory $modelCollectionFactory,
        DataSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        ResourceConnection $resourceConnection,
        CoreProductRepositoryInterface $coreProductRepository,
        AmountFactory $amountFactory,
        AutoAuctionFactory $autoAuctionFactory,
        AuctionData $helperData,
        EndAuctionInterfaceFactory $endAuctionFactory,
        WattingUserInterfaceFactory $wattingUserFactory,
        WinnerInterfaceFactory $winnerFactory
    ) {
        $this->resource = $resource;
        $this->productFactory = $productFactory;
        $this->dataApiFactory = $dataApiFactory;
        $this->modelCollectionFactory = $modelCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->_resource = $resourceConnection;
        $this->coreProductRepository = $coreProductRepository;
        $this->amountFactory = $amountFactory;
        $this->autoAuctionFactory = $autoAuctionFactory;
        $this->helperData = $helperData;
        $this->endAuctionFactory = $endAuctionFactory;
        $this->wattingUserFactory = $wattingUserFactory;
        $this->winnerFactory = $winnerFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getMinAuctionAmount($entityId, $storeId = null)
    {
        if (!$entityId) {
            return 0;
        }
        $auctionConfig = $this->helperData->getAuctionConfiguration();
        return $this->helperData->getMinAmount($entityId, $auctionConfig);
    }

    /**
     * {@inheritdoc}
     */
    public function getAuctionDetailAfterEnd($customerId, $entityId, $productId, $storeId = null)
    {
        $today = $this->helperData->getTimezoneDateTime();
        $currentTimeStamp = strtotime($today);
        $auctionData = $this->getAuctionData($entityId);

        $winnerCustomerId = 0;
        $shop = '';
        $price = 0;
        $currentUserWinner = null;
        $currentUserWaitingList = null;

        if (!empty($auctionData)) {

            $winnerBidDetail = $this->helperData->getWinnerBidDetail($entityId);

            if ($winnerBidDetail) {
                $winnerCustomerId = $winnerBidDetail->getCustomerId();
                $shop = $winnerBidDetail->getShop();
                $price = $winnerBidDetail->getBidType() == 'normal' ? $winnerBidDetail->getAuctionAmount() :
                    $winnerBidDetail->getWinningPrice();
            }

            $waittingList = $this->amountFactory->create()->getCollection()
                ->addFieldToFilter('product_id', ['eq' => $productId])
                ->addFieldToFilter('auction_id', ['eq' => $entityId])
                ->addFieldToFilter('winning_status', ['neq' => 1] )
                ->addFieldToFilter('status', ['eq' => AuctionStatus::STATUS_TIME_END ]);

            $autoWaittingList = $this->autoAuctionFactory->create()->getCollection()
                ->addFieldToFilter('auction_id', ['eq' => $entityId])
                ->addFieldToFilter('flag', ['neq' => 1])
                ->addFieldToFilter('status', ['eq' => AuctionStatus::STATUS_TIME_END ]);

            $custList = [];

            //get watting winner list from auction amount
            foreach ($waittingList as $waitAuc) {
                if ($waitAuc->getCustomerId() != $winnerCustomerId && !in_array($waitAuc->getCustomerId(), $custList)) {
                    array_push($custList, $waitAuc->getCustomerId());
                }
            }
            //get watting winner list from auto auction
            foreach ($autoWaittingList as $autoWaitAuc) {
                if ($autoWaitAuc->getBuyerId() != $winnerCustomerId && !in_array($autoWaitAuc->getBuyerId(), $custList)) {
                    array_push($custList, $autoWaitAuc->getBuyerId());
                }
            }

            if ($customerId == $winnerCustomerId) {
                $day = strtotime($auctionData['stop_auction_time']. ' + '.$auctionData['days'] . ' days');
                $difference = $day - $currentTimeStamp;
                //$winnerMsg = $this->helperData->getConfig('general_settings/show_winner_msg');
                $currentUserWinner = $this->winnerFactory->create();
                $currentUserWinner->setShop($shop);
                $currentUserWinner->setPrice($price);
                $currentUserWinner->setTimeForBuy($difference);
                //$currentUserWinner->setWinnerMsg($winnerMsg);
            }

            if (in_array($customerId, $custList)) {

                $currentUserWaitingList = $this->wattingUserFactory->create();
                $currentUserWaitingList->setAucListUrl('auction/account/history/id/'.$entityId);
                $currentUserWaitingList->setAucUrlLable( __('%1 Bids', $waittingList->getSize())->__toString() );
                $currentUserWaitingList->setMsgLable($winnerCustomerId ? __('Bidding has been done for this product.')->__toString() :
                    __('Bidding has been done for this product.')->__toString());
            }
        }

        $endAuction = $this->endAuctionFactory->create();
        $endAuction->setWattingUser($currentUserWaitingList);
        $endAuction->setWinner($currentUserWinner);
        return $endAuction;
    }

    /**
     * get auction data
     *
     * @param int $entityId
     * @return mixed
     */
    protected function getAuctionData($entityId)
    {
        $collection = $this->modelCollectionFactory->create();
        $auctionData = $collection->addFieldToFilter('entity_id', ['eq' => $entityId])
                        ->addFieldToFilter('auction_status',
                        [
                            'in' => [
                                AuctionStatus::STATUS_TIME_END,
                                AuctionStatus::STATUS_PROCESS,
                                AuctionStatus::STATUS_WINNER_NOT_DEFINED
                            ]
                        ])
                        ->setOrder('entity_id')
                        ->getFirstItem()
                        ->getData();
        return $auctionData;
    }
}
