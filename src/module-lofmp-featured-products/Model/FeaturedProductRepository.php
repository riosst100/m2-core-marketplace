<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lofmp\FeaturedProducts\Model;

use Lofmp\FeaturedProducts\Api\Data\FeaturedProductInterface;
use Lofmp\FeaturedProducts\Api\Data\FeaturedProductInterfaceFactory;
use Lofmp\FeaturedProducts\Api\Data\FeaturedProductSearchResultsInterfaceFactory;
use Lofmp\FeaturedProducts\Api\FeaturedProductRepositoryInterface;
use Lofmp\FeaturedProducts\Model\ResourceModel\FeaturedProduct as ResourceFeaturedProduct;
use Lofmp\FeaturedProducts\Model\ResourceModel\FeaturedProduct\CollectionFactory as FeaturedProductCollectionFactory;
use Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory as SellerCollectionFactory;
use Lof\MarketPlace\Model\ResourceModel\SellerProduct\CollectionFactory as ProductCollectionFactory;
use Lof\MarketPlace\Model\Seller;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Lof\MarketPlace\Helper\Data;
use Magento\Framework\Exception\LocalizedException;

class FeaturedProductRepository implements FeaturedProductRepositoryInterface
{

    /**
     * @var FeaturedProductInterfaceFactory
     */
    protected $featuredProductFactory;

    /**
     * @var FeaturedProduct
     */
    protected $searchResultsFactory;

    /**
     * @var FeaturedProductCollectionFactory
     */
    protected $featuredProductCollectionFactory;

    /**
     * @var ResourceFeaturedProduct
     */
    protected $resource;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var Data
     */
    private $helper;

    /**
     * @var SellerCollectionFactory
     */
    protected $sellerCollectionFactory;

    /**
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var mixed|array
     */
    protected $_sellers = [];

    /**
     * @param ResourceFeaturedProduct $resource
     * @param FeaturedProductInterfaceFactory $featuredProductFactory
     * @param FeaturedProductCollectionFactory $featuredProductCollectionFactory
     * @param FeaturedProductSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param SellerCollectionFactory $sellerCollectionFactory
     * @param ProductCollectionFactory $productCollectionFactory
     * @param Data $helper
     */
    public function __construct(
        ResourceFeaturedProduct $resource,
        FeaturedProductInterfaceFactory $featuredProductFactory,
        FeaturedProductCollectionFactory $featuredProductCollectionFactory,
        FeaturedProductSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor,
        SellerCollectionFactory $sellerCollectionFactory,
        ProductCollectionFactory $productCollectionFactory,
        Data $helper
    ) {
        $this->resource = $resource;
        $this->featuredProductFactory = $featuredProductFactory;
        $this->featuredProductCollectionFactory = $featuredProductCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->helper = $helper;
        $this->sellerCollectionFactory = $sellerCollectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->productCollectionFactory = $productCollectionFactory;
    }

    /**
     * @inheritDoc
     */
    public function saveFeatureProduct(
        int $customerId,
        FeaturedProductInterface $featuredProduct
    ) {
        if (!$featuredProduct->getProductId()) {
            throw new CouldNotSaveException(__(
                'Can not save featured product, product_id field is missing.'
            ));
        }
        $seller = $this->getSellerByCustomerId($customerId);
        if ($seller && $seller->getId()) {
            try {
                $sellerProductFound = $this->getSellerProductById($featuredProduct->getProductId(), $seller->getId());
                if ($sellerProductFound && $sellerProductFound->getProductId()) {
                    $featureProductModel = $this->featuredProductFactory->create();
                    $featureProductModel->setProductId($featuredProduct->getProductId())
                        ->setSellerId($seller->getId())
                        ->setFeaturedFrom($featuredProduct->getFeaturedFrom())
                        ->setFeaturedTo($featuredProduct->getFeaturedTo())
                        ->setSortOrder($featuredProduct->getSortOrder());
                    $featureProductModel->save();
                } else {
                    throw new CouldNotSaveException(__(
                        'Product %1 is not available for this seller.', $featuredProduct->getProductId()
                    ));
                }
            } catch (LocalizedException $e) {
                throw new Exception($e->getMessage());
            }
            return $featuredProduct;
        } else {
            throw new CouldNotSaveException(__(
                'Seller is not available.'
            ));
        }
    }

    /**
     * @inheritDoc
     */
    public function get(int $customerId, $id)
    {
        $seller = $this->getSellerByCustomerId($customerId);

        if ($seller && $seller->getId()) {
            $featuredProduct = $this->featuredProductFactory->create();
            $this->resource->load($featuredProduct, $id);
            if (!$featuredProduct->getId() || ($featuredProduct->getSellerId() != $seller->getId())) {
                throw new NoSuchEntityException(__('FeaturedProduct with id "%1" does not exist.', $id));
            }
            return $featuredProduct;
        } else {
            throw new NoSuchEntityException(__(
                'Seller is not available.'
            ));
        }
    }

    /**
     * @inheritDoc
     */
    public function getListFeatureProduct(
        int $customerId,
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $seller = $this->getSellerByCustomerId($customerId);
        $collection = $this->featuredProductCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        $collection->addFieldToFilter('seller_id', $seller->getId());

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model;
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(
        FeaturedProductInterface $featuredProduct
    ) {
        try {
            $featuredProductModel = $this->featuredProductFactory->create();
            $this->resource->load($featuredProductModel, $featuredProduct->getId());
            $this->resource->delete($featuredProductModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the FeaturedProduct: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @param int $customerId
     * @param $id
     * @return bool
     * @throws LocalizedException
     */
    public function deleteById(int $customerId, $id)
    {
        return $this->delete($this->get($customerId, $id));
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
     * get seller product by id
     *
     * @param int $productId
     * @param int $sellerId
     * @return mixed
     */
    protected function getSellerProductById($productId, $sellerId)
    {
        $foundItem = $this->productCollectionFactory->create()
                        ->addFieldToFilter("seller_id", $sellerId)
                        ->addFieldToFilter("product_id", $productId)
                        ->getFirstItem();

        return $foundItem;
    }
}

