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

namespace Lof\MarketPlace\Model;

use Lof\MarketPlace\Helper\Data;
use Lof\MarketPlace\Helper\Seller as HelperSeller;
use Lof\MarketPlace\Api\Data\SellerInterface;
use Lof\MarketPlace\Api\Data\SellersSearchResultsInterfaceFactory;
use Lof\MarketPlace\Api\SellersManagementInterface;
use Lof\MarketPlace\Api\SellerRatingsRepositoryInterfaceFactory;
use Lof\MarketPlace\Api\SellerReviewRepositoryInterfaceFactory;
use Lof\MarketPlace\Api\SellersFrontendProductRepositoryInterfaceFactory;
use Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteriaInterfaceFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\AddressFactory;
use Magento\Customer\Model\CustomerFactory;
use Lof\MarketPlace\Helper\WebsiteStore;
use Magento\Authorization\Model\CompositeUserContext;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SellersManagement implements SellersManagementInterface
{
    /**
     * @var Seller
     */
    protected $_seller;

    /**
     * @var SellersSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;

    /**
     * @var ResourceModel\Seller\CollectionFactory
     */
    protected $_collection;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var SearchCriteriaInterfaceFactory
     */
    protected $searchCriteriaInterface;


    /**
     * @var SellerFactory
     */
    protected $sellerFactory;


    /**
     * @var Data
     */
    protected $helperData;


    /**
     * @var HelperSeller
     */
    protected $heplerSeller;

    /**
     * @var Sender
     */
    protected $sender;

    /**
     * SellersFrontendRepository constructor.
     *
     * @param Seller $seller
     * @param SellerFactory $sellerFactory
     * @param CollectionFactory $collection
     * @param SellersSearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param SearchCriteriaInterfaceFactory $searchCriteriaInterface
     * @param Data $helperData
     * @param HellperSeller $heplerSeller
     * @param Sender $sender
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Seller $seller,
        SellerFactory $sellerFactory,
        CollectionFactory $collection,
        SellersSearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor,
        SearchCriteriaInterfaceFactory $searchCriteriaInterface,
        Data $helperData,
        HelperSeller $heplerSeller,
        Sender $sender
    ) {
        $this->_seller = $seller;
        $this->_collection = $collection;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->searchCriteriaInterface = $searchCriteriaInterface;
        $this->sellerFactory = $sellerFactory;
        $this->helperData = $helperData;
        $this->heplerSeller = $heplerSeller;
        $this->sender = $sender;
    }

    /**
     * {@inheritdoc}
     */
    public function get($sellerId)
    {
        $seller = $this->sellerFactory->create()->load($sellerId);
        if (!$seller->getId()) {
            throw new NoSuchEntityException(__('Seller with id "%1" does not exist.', $sellerId));
        }
        return $seller->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function updateSellerStatus($sellerId, $status)
    {
        $seller = $this->sellerFactory->create()->load($sellerId);
        if (!$seller->getId()) {
            throw new NoSuchEntityException(__('Seller with id "%1" does not exist.', $sellerId));
        }
        try {
            if ($status == 1) {
                $seller->setStatus(1);
                $seller->save();
            } else if ($status == 0) {
                $seller->setStatus(0);
                $seller->save();
            } else {
                throw new NoSuchEntityException(__('Status Id "%1" is not exists', $status));
            }
            $data = $seller->getData();
            $data['url'] = $seller->getUrl();

            if ($this->helperData->getConfig('email_settings/enable_send_email')) {
                $this->sender->approveSeller($data);
            }
            return $this->get($sellerId);
        }  catch (\Exception $exception) {
            // phpcs:disable Magento2.Exceptions.DirectThrow.FoundDirectThrow
            throw new \Exception($exception->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function verify($sellerId, $status)
    {
        $seller = $this->sellerFactory->create()->load($sellerId);
        if (!$seller->getId()) {
            throw new NoSuchEntityException(__('Seller with id "%1" does not exist.', $sellerId));
        }
        try {
            if ($status) {
                $seller->setVerifyStatus(1);
                $seller->save();
            } else {
                $seller->setVerifyStatus(0);
                $seller->save();
            }
            return $this->get($sellerId);
        }  catch (\Exception $exception) {
            // phpcs:disable Magento2.Exceptions.DirectThrow.FoundDirectThrow
            throw new \Exception($exception->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->_collection->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            SellerInterface::class
        );

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model->getDataModel();
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

}
