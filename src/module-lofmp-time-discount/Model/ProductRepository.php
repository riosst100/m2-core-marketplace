<?php

/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Lofmp\TimeDiscount\Model;

use Lofmp\TimeDiscount\Api\Data\TimeDiscountInterface;
use Lofmp\TimeDiscount\Api\Data\ProductInterface;
use Lofmp\TimeDiscount\Api\Data\ProductDetailInterfaceFactory;
use Lofmp\TimeDiscount\Api\Data\TimeDiscountInterfaceFactory;
use Lofmp\TimeDiscount\Api\Data\ProductInterfaceFactory;
use Lofmp\TimeDiscount\Api\Data\ProductSearchResultsInterfaceFactory;
use Lofmp\TimeDiscount\Api\ProductRepositoryInterface;
use Lofmp\TimeDiscount\Model\ResourceModel\Product as ResourceProduct;
use Lofmp\TimeDiscount\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory as SellerCollectionFactory;
use Magento\Catalog\Api\ProductRepositoryInterface as CatalogProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Lof\MarketPlace\Helper\Data;
use Lof\MarketPlace\Model\Seller;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Exception\LocalizedException;

class ProductRepository implements ProductRepositoryInterface
{
    /**
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var ResourceProduct
     */
    protected $resource;

    /**
     * @var \Lofmp\TimeDiscount\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var Product
     */
    protected $searchResultsFactory;

    protected $_productRepository;

    /**
     * @var Data
     */
    private $helper;

    protected $extensibleDataObjectConverter;

    protected $allowThrowException = true;

    /**
     * @var SellerCollectionFactory
     */
    protected $sellerCollectionFactory;

    /**
     * @var \Lofmp\TimeDiscount\Model\Serializer
     */
    protected $serializer;

    /**
     * @var ProductDetailInterfaceFactory
     */
    protected $productDetailDataFactory;

    /**
     * @var TimeDiscountInterfaceFactory
     */
    protected $timeDiscountDataFactory;

    /**
     * @var CatalogProductRepositoryInterface
     */
    protected $catalogProductRepository;

    /**
     * @param ResourceProduct $resource
     * @param ProductInterfaceFactory $productFactory
     * @param ProductCollectionFactory $productCollectionFactory
     * @param ProductSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param Data $helper
     * @param Serializer $serializer
     * @param SellerCollectionFactory $sellerCollectionFactory
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param ProductDetailInterfaceFactory $productDetailDataFactory
     * @param TimeDiscountInterfaceFactory $timeDiscountDataFactory
     * @param CatalogProductRepositoryInterface $catalogProductRepository
     */
    public function __construct(
        ResourceProduct $resource,
        ProductFactory $productFactory,
        ProductCollectionFactory $productCollectionFactory,
        ProductSearchResultsInterfaceFactory $searchResultsFactory,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        Data $helper,
        Serializer $serializer,
        SellerCollectionFactory $sellerCollectionFactory,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        CollectionProcessorInterface $collectionProcessor,
        ProductDetailInterfaceFactory $productDetailDataFactory,
        TimeDiscountInterfaceFactory $timeDiscountDataFactory,
        CatalogProductRepositoryInterface $catalogProductRepository
    ) {
        $this->resource = $resource;
        $this->productFactory = $productFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->_productRepository = $productRepository;
        $this->helper = $helper;
        $this->serializer = $serializer;
        $this->sellerCollectionFactory = $sellerCollectionFactory;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->collectionProcessor = $collectionProcessor;
        $this->productDetailDataFactory = $productDetailDataFactory;
        $this->timeDiscountDataFactory = $timeDiscountDataFactory;
        $this->catalogProductRepository = $catalogProductRepository;
    }

    /**
     * @inheritDoc
     */
    public function save(ProductInterface $product)
    {
        try {
            $timeDiscountArray = $product->getTimeDiscount();
            $dataJsonArray = [];
            if ($timeDiscountArray) {
                foreach ($timeDiscountArray as $item) {
                    $dataJsonArray[] = [
                        "start" => $item->getStart(),
                        "end" => $item->getEnd(),
                        "type" => $item->getType(),
                        "discount" => $item->getDiscount(),
                        "order" => $item->getOrder(),
                        "delete" => ""
                    ];
                }
            }
            $dataJson = $this->serializer->serialize($dataJsonArray);
            $product->setData("data", $dataJson);
            $this->resource->save($product);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the product: %1',
                $exception->getMessage()
            ));
        }
        return $product;
    }

    /**
     * @inheritDoc
     */
    public function get($id)
    {
        $product = $this->productFactory->create();
        $this->resource->load($product, $id);
        if (!$product->getId()) {
            throw new NoSuchEntityException(__('Product with id "%1" does not exist.', $id));
        }
        $dataArray = $product->getData("data");
        $time_discount = $this->formatTimeDiscountData($dataArray);
        $product->setTimeDiscount($time_discount);
        return $product;
    }

    /**
     * @inheritDoc
     */
    public function getBySku($sku)
    {
        $product = $this->getProductBySku($sku);
        if (!$product->getId()) {
            throw new NoSuchEntityException(__('Product with id "%1" does not exist.', $productId));
        }
        $dataArray = $product->getData("data");
        $time_discount = $this->formatTimeDiscountData($dataArray);
        $product->setTimeDiscount($time_discount);
        return $product;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->productCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $product) {
            $dataArray = $product->getData("data");
            $time_discount = $this->formatTimeDiscountData($dataArray);
            $product->setTimeDiscount($time_discount);
            $items[] = $product;
        }
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(ProductInterface $product)
    {
        try {
            $productModel = $this->productFactory->create();
            $this->resource->load($productModel, $product->getProductId());
            $this->resource->delete($productModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Product: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteBySku($sku)
    {
        return $this->delete($this->getBySku($sku));
    }

    /**
     * @inheritdoc
     */
    public function sellerSaveProduct(
        int $customerId,
        $sku,
        \Lofmp\TimeDiscount\Api\Data\ProductInterface $product
    ){
        $product = $this->getProductBySku($sku);
        if (!$product->getId()) {
            throw new NoSuchEntityException(__('Time Discount Product with sku "%1" does not exist.', $sku));
        }
        $seller = $this->getSellerByCustomerId($customerId);
        if ($seller & $seller->getId()) {
            $data = $product->getData();
            $data['product_id'] = $product->getId();
            $data['seller_id']=$seller->getId();
            if(isset($data['data'])) {
                $data['data'] = $this->serializer->serialize($data['data']);
            }
            try {
                $product->setData($data);
                $this->resource->save($product);

             } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the chat: %1',
                $exception->getMessage()
            ));
        }
        } else {
            throw new CouldNotSaveException(__(
                'Seller is not available.'
            ));
        }
    }

    /**
     * @param int $customerId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lofmp\TimeDiscount\Api\Data\ProductSearchResultsInterface|void
     */
    public function sellerGetList(
        int $customerId,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    ) {
        $seller = $this->getSellerByCustomerId($customerId);
        $collection = $this->productCollectionFactory->create();

        $this->collectionProcessor->process($searchCriteria, $collection);

        $collection->addFieldToFilter('seller_id', $seller->getId());

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        $items = [];
        foreach ($collection as $product) {
            $dataArray = $product->getData("data");
            $time_discount = $this->formatTimeDiscountData($dataArray);
            $product->setTimeDiscount($time_discount);
            $items[] = $product;
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function sellerGet(int $customerId, $sku)
    {
        $seller = $this->getSellerByCustomerId($customerId);
        if ($seller && $seller->getId()) {
            $product = $this->getBySku($sku);
            if ($product->getSellerId() != $seller->getId()) {
                throw new NoSuchEntityException(__('Chat with id "%1" does not exist.', $sku));
            }
            $dataArray = $product->getData("data");
            $time_discount = $this->formatTimeDiscountData($dataArray);
            $product->setTimeDiscount($time_discount);
            return $product;
        } else {
            throw new CouldNotSaveException(__(
                'Seller is not available.'
            ));
        }
    }

    /**
     * @inheritDoc
     */
    public function getProductBySku($sku)
    {
        $product = $this->productFactory->create();
        $catalogProduct = $this->catalogProductRepository->get($sku);
        if ($catalogProduct->getId()) {
            $foundItem = $this->productCollectionFactory->create()
                            ->addFieldToFilter("product_id", $catalogProduct->getId())
                            ->getFirstItem();
            $productId = $foundItem && $foundItem->getId() ? $foundItem->getId() : 0;
            if ($productId) {
                $this->resource->load($product, $productId);
            }
        }
        return $product;
    }

    /**
     * @param int $customerId
     * @param $sku
     * @return bool
     * @throws CouldNotSaveException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function sellerDeleteBysku(int $customerId, $sku)
    {
        return $this->delete($this->sellerGet($customerId, $sku));
    }

    /**
     * @inheritDoc
     */
    public function getDetail($sku)
    {
        $productTimeDiscount = $this->getProductBySku($sku);
        if (!$productTimeDiscount->getId()) {
            throw new NoSuchEntityException(__('Time Discount Product with sku "%1" does not exist.', $sku));
        }
        $productDetail = $this->productDetailDataFactory->create();
        $data = [
            "sort_order" => $productTimeDiscount->getSortOrder(),
            "created_at" => $productTimeDiscount->getCreatedAt(),
            "time_discount" => []
        ];
        $dataArray = $productTimeDiscount->getData("data");
        $data["time_discount"] = $this->formatTimeDiscountData($dataArray);
        $productDetail->setData($data);

        return $productDetail;
    }

    /**
     * get seller by customer id
     *
     * @param int $customerId
     * @return \Lof\MarketPlace\Model\Seller
     */
    protected function getSellerByCustomerId(int $customerId)
    {
        if (!isset($this->_sellers[$customerId])) {
            $sellerCollection = $this->sellerCollectionFactory->create();
            $this->_sellers[$customerId] = $sellerCollection
                ->addFieldToFilter("customer_id", $customerId)
                ->addFieldToFilter("status", Seller::STATUS_ENABLED)
                ->getFirstItem();
        }
        return $this->_sellers[$customerId];
    }

    /**
     * format time discount data
     *
     * @param mixed|array $dataArray
     * @return mixed|array
     */
    protected function formatTimeDiscountData($dataArray)
    {
        $dataTimeDiscount = [];
        if ($dataArray) {
            foreach ($dataArray as $_item) {
                $timeDiscountData = $this->timeDiscountDataFactory->create();
                $timeDiscountData->setStart(isset($_item["start"]) ? $_item["start"] : "" );
                $timeDiscountData->setEnd(isset($_item["end"]) ? $_item["end"] : "" );
                $timeDiscountData->setType(isset($_item["type"]) ? $_item["type"] : "percent" );
                $timeDiscountData->setDiscount(isset($_item["discount"]) ? (float)$_item["discount"] : 0 );
                $timeDiscountData->setOrder(isset($_item["order"]) ? (int)$_item["order"] : 0 );
                $dataTimeDiscount[] = $timeDiscountData;
            }
        }
        return $dataTimeDiscount;
    }
}
