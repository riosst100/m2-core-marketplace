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

use Lof\MarketPlace\Api\Data\SellersSearchResultsInterfaceFactory;
use Lof\MarketPlace\Api\SellersFrontendRepositoryInterface;
use Lof\MarketPlace\Api\SellerRatingsRepositoryInterfaceFactory;
use Lof\MarketPlace\Api\SellerReviewRepositoryInterfaceFactory;
use Lof\MarketPlace\Api\SellersFrontendProductRepositoryInterfaceFactory;
use Lof\MarketPlace\Api\SellerVacationRepositoryInterface;
use Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory;
use Lof\MarketPlace\Model\ResourceModel\Orderitems\CollectionFactory as OrderitemsCollectionFactory;
use Lof\MarketPlace\Helper\Data;
use Lof\MarketPlace\Model\ResourceModel\Seller as SellerResourceModel;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteriaInterfaceFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SellersFrontendRepository implements SellersFrontendRepositoryInterface
{
    /**
     * @var Seller
     */
    protected $_seller;

    /**
     * @var SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var SellerResourceModel|mixed
     */
    protected $resourceModel;

    /**
     * @var SellersSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var SellerProductFactory
     */
    protected $sellerProductFactory;

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
     * @var SellerRatingsRepositoryInterfaceFactory
     */
    protected $sellerRatingRepository;

    /**
     * @var SellerReviewRepositoryInterfaceFactory
     */
    protected $sellerReviewRepository;

    /**
     * @var SearchCriteriaInterfaceFactory
     */
    protected $searchCriteriaInterface;

    /**
     * @var SellersFrontendProductRepositoryInterfaceFactory
     */
    protected $sellerProductRepository;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var SellerVacationRepositoryInterface
     */
    protected $vacationRepository;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var OrderitemsCollectionFactory
     */
    protected $orderitemsCollectionFactory;

    /**
     * SellersFrontendRepository constructor.
     *
     * @param Seller $seller
     * @param CollectionFactory $collection
     * @param StoreManagerInterface $storeManager
     * @param ObjectManagerInterface $objectManager
     * @param SellersSearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param SellerProductFactory $sellerProductFactory
     * @param SellerRatingsRepositoryInterfaceFactory $sellerRatingRepository
     * @param SellerReviewRepositoryInterfaceFactory $sellerReviewRepository
     * @param SellersFrontendProductRepositoryInterfaceFactory $sellerProductRepository
     * @param SearchCriteriaInterfaceFactory $searchCriteriaInterface
     * @param SellerFactory $sellerFactory
     * @param SellerVacationRepositoryInterface $vacationRepository
     * @param ProductRepositoryInterface $productRepository
     * @param OrderitemsCollectionFactory $orderitemsCollectionFactory
     * @param Data $helperData
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Seller $seller,
        CollectionFactory $collection,
        StoreManagerInterface $storeManager,
        ObjectManagerInterface $objectManager,
        SellersSearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor,
        SellerProductFactory $sellerProductFactory,
        SellerRatingsRepositoryInterfaceFactory $sellerRatingRepository,
        SellerReviewRepositoryInterfaceFactory $sellerReviewRepository,
        SellersFrontendProductRepositoryInterfaceFactory $sellerProductRepository,
        SearchCriteriaInterfaceFactory $searchCriteriaInterface,
        SellerFactory $sellerFactory,
        SellerVacationRepositoryInterface $vacationRepository,
        ProductRepositoryInterface $productRepository,
        OrderitemsCollectionFactory $orderitemsCollectionFactory,
        Data $helperData
    ) {
        $this->_seller = $seller;
        $this->_collection = $collection;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->_storeManager = $storeManager;
        $this->_objectManager = $objectManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->sellerProductFactory = $sellerProductFactory;
        $this->sellerRatingRepository = $sellerRatingRepository;
        $this->sellerReviewRepository = $sellerReviewRepository;
        $this->sellerProductRepository = $sellerProductRepository;
        $this->searchCriteriaInterface = $searchCriteriaInterface;
        $this->sellerFactory = $sellerFactory;
        $this->resourceModel = $seller->getResource();
        $this->helperData = $helperData;
        $this->vacationRepository = $vacationRepository;
        $this->productRepository = $productRepository;
        $this->orderitemsCollectionFactory = $orderitemsCollectionFactory;
    }

    /**
     * @inheritDoc
     */
    public function getByUrl($sellerUrl, $showOtherInfo = false, $getProducts = false)
    {
        $collection = $this->_collection->create();
        $seller = $collection->addFieldToFilter("url_key", $sellerUrl)
                    ->addFieldToFilter("status", Seller::STATUS_ENABLED)
                    ->getFirstItem();
        if ($seller && $seller->getId()) {
            return $this->get((int)$seller->getId(), $showOtherInfo, $getProducts);
        } else {
            throw new NoSuchEntityException(__('Seller with seller Url "%1" does not exist.', $sellerUrl));
        }
    }

    /**
     * @inheritDoc
     */
    public function get(int $sellerId, $showOtherInfo = false, $getProducts = false)
    {
        $dataModel = null;
        if ($sellerId) {
            $collection = $this->_collection->create();
            $collection->addFieldToFilter("seller_id", $sellerId);
            $collection->addFieldToFilter("status", Seller::STATUS_ENABLED);
            if ($collection->getSize() > 0) {
                $sellerModel = $this->sellerFactory->create();
                $this->resourceModel->load($sellerModel, $sellerId);
                $data = $sellerModel->getData();
                if (isset($data['image']) && $data['image'] != '') {
                    $data["logo_pic"] = (!isset($data["logo_pic"]) || (isset($data["logo_pic"]) && empty($data["logo_pic"]))) ? $data["thumbnail"] : $data["logo_pic"];
                    $data["banner_pic"] = (!isset($data["banner_pic"]) || (isset($data["banner_pic"]) && empty($data["banner_pic"]))) ? $data["image"] : $data["banner_pic"];
                    $data["image"] = $this->_storeManager->getStore()
                            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $data["image"];

                    $data["thumbnail"] = $this->_storeManager->getStore()
                            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $data["thumbnail"];
                }

                $dataModel = $sellerModel->getDataModel();
                $dataModel->setImage($data["image"]);
                $dataModel->setThumbnail($data["thumbnail"]);
                $sellerRatingRepository = $this->sellerRatingRepository->create();
                $summaryRates = $sellerRatingRepository->getSummaryRatingsBySellerId((int)$sellerId);
                $dataModel->setSummaryRates($summaryRates);
                if (!$this->helperData->getConfig("api_settings/show_email")) {
                    $dataModel->setEmail("");
                }

                if ($showOtherInfo) {
                    /** get seller rates */
                    $sellerRates = $this->getSellersRating((int)$sellerId);

                    /** get seller reviews */
                    $searchCriteria3 = $this->searchCriteriaInterface->create();
                    $searchCriteria3->setCurrentPage(1);
                    $searchCriteria3->setPageSize(0);
                    $sellerReviews = $this->sellerReviewRepository->create()
                        ->getList((int)$sellerId, $searchCriteria3);

                    /** get seller vacation */
                    $vacation = $this->vacationRepository->getSellerVacationById((int)$sellerId);
                    $dataModel->setVacation($vacation);

                    /** get total sales */
                    $totalSales = $this->getTotalSales((int)$sellerId);
                    /** get seller groups */
                    $group = $sellerModel->getSellerGroup();
                    $dataModel->setGroup($group);
                    $dataModel->setSellerRates($sellerRates);
                    $dataModel->setSellerReviews($sellerReviews);
                    $dataModel->setTotalReviews($sellerReviews->getTotalCount());
                    $dataModel->setTotalSales($totalSales);
                }
                /** get list seller products */
                if ($getProducts) {
                    $searchCriteria2 = $this->searchCriteriaInterface->create();
                    $searchCriteria2->setCurrentPage(1);
                    $searchCriteria2->setPageSize(12);
                    $products = $this->sellerProductRepository->create()
                        ->getList($sellerId, $searchCriteria2);
                    $dataModel->setProducts($products);
                    $dataModel->setTotalProducts($products->getTotalCount());
                }
            }
        }

        return $dataModel;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        SearchCriteriaInterface $criteria,
        $showOtherInfo = false,
        $getProducts = false
    ) {
        $collection = $this->_collection->create();
        $this->collectionProcessor->process($criteria, $collection);
        $collection->addFieldToFilter("status", Seller::STATUS_ENABLED);

        $searchResults = $this->searchResultsFactory->create();
        $showEmail = $this->helperData->getConfig("api_settings/show_email");
        $items = [];
        foreach ($collection as $model) {
            $data = $model->getData();
            if (isset($data['image']) && $data['image']) {
                $data["logo_pic"] = (!isset($data["logo_pic"]) || (isset($data["logo_pic"]) && empty($data["logo_pic"]))) ? $data["thumbnail"] : $data["logo_pic"];
                $data["banner_pic"] = (!isset($data["banner_pic"]) || (isset($data["banner_pic"]) && empty($data["banner_pic"]))) ? $data["image"] : $data["banner_pic"];

                $data["image"] = $this->_storeManager->getStore()
                        ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $data["image"];

                $data["thumbnail"] = $this->_storeManager->getStore()
                        ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $data["thumbnail"];
            }
            if (isset($data['store_id']) && $data['store_id']) {
                $data['store_id'] = implode(',', $data['store_id']);
            }
            $dataModel = $model->getDataModel();

            $dataModel->setImage($data["image"]);
            $dataModel->setThumbnail($data["thumbnail"]);
            $dataModel->setStoreId($data['store_id']);
            if (!$showEmail) {
                $dataModel->setEmail("");
            }

            if ($showOtherInfo) {
                $sellerRatingRepository = $this->sellerRatingRepository->create();
                $summaryRates = $sellerRatingRepository->getSummaryRatingsBySellerId((int)$data['seller_id']);
                $dataModel->setSummaryRates($summaryRates);

                /** get seller rates */
                $sellerRates = $this->getSellersRating((int)$data['seller_id']);

                /** get seller reviews */
                $searchCriteria3 = $this->searchCriteriaInterface->create();
                $searchCriteria3->setCurrentPage(1);
                $searchCriteria3->setPageSize(0);
                $sellerReviews = $this->sellerReviewRepository->create()
                    ->getList((int)$data['seller_id'], $searchCriteria3);

                /** get seller vacation */
                $vacation = $this->vacationRepository->getSellerVacationById((int)$data['seller_id']);
                $dataModel->setVacation($vacation);

                /** get seller groups */
                $group = $model->getSellerGroup();

                /** get total sales */
                $totalSales = $this->getTotalSales((int)$data['seller_id']);
                $dataModel->setSellerRates($sellerRates);
                $dataModel->setGroup($group);
                $dataModel->setTotalReviews($sellerReviews->getTotalCount());
                $dataModel->setTotalSales($totalSales);
            }
            /** get seller products */
            if ($getProducts) {
                $searchCriteria2 = $this->searchCriteriaInterface->create();
                $searchCriteria2->setCurrentPage(1);
                $searchCriteria2->setPageSize(12);
                $products = $this->sellerProductRepository->create()->getList($data['seller_id'], $searchCriteria2);
                $dataModel->setProducts($products);
                $dataModel->setTotalProducts($products->getTotalCount());
            }
            $items[] = $dataModel;
        }
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * get total sales of seller
     *
     * @param int $sellerId
     * @return int
     */
    public function getTotalSales($sellerId)
    {
        $total = 0;
        if ((int)$this->helperData->getConfig("general_settings/show_total_sales")) {
            $orderitems = $this->orderitemsCollectionFactory->create()
                ->addFieldToFilter('seller_id', $sellerId)
                ->addFieldToFilter('status', Order::STATE_COMPLETED);

            foreach ($orderitems as $_orderitems) {
                $total = $total + $_orderitems->getProductQty();
            }
        }
        return $total;
    }

    /**
     * @inheritdoc
     */
    public function getSellersRating($sellerId)
    {
        /** get seller rates */
        $sellerRatingRepository = $this->sellerRatingRepository->create();
        $searchCriteria1 = $this->searchCriteriaInterface->create();
        $searchCriteria1->setCurrentPage(1);
        $searchCriteria1->setPageSize(0);
        $sellerRates = $sellerRatingRepository->getList($sellerId, $searchCriteria1);
        return $sellerRates;
    }

    /**
     * @inheritDoc
     */
    public function getSellerByProductId($product_id, $showOtherInfo = false, $getProducts = false)
    {
        $seller = $this->sellerProductFactory->create()->load((int)$product_id, 'product_id');
        if ($seller->getData("seller_id")) {
            return $this->get((int)$seller->getData("seller_id"), $showOtherInfo, $getProducts);
        }
        throw new NoSuchEntityException(__('Seller is not exists for product id "%1".', $seller));
    }

    /**
     * @inheritDoc
     */
    public function getSellerByProductSku($sku, $storeId = null, $showOtherInfo = false, $getProducts = false)
    {
        $product = $this->productRepository->get($sku, false, $storeId);
        $sellerId = $product->getSellerId();
        if ($sellerId) {
            return $this->get((int)$sellerId, $showOtherInfo, $getProducts);
        }
        throw new NoSuchEntityException(__('Seller is not exists for product sku "%1".', $sku));
    }
}
