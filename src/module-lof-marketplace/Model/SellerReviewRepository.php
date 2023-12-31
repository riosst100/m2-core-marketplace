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

use Lof\MarketPlace\Api\Data\ReviewInterfaceFactory;
use Lof\MarketPlace\Api\Data\ReviewSearchResultsInterfaceFactory;
use Lof\MarketPlace\Api\SellerReviewRepositoryInterface;
use Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory as SellerCollectionFactory;
use Lof\MarketPlace\Model\ResourceModel\Review as ResourceModelReview;
use Lof\MarketPlace\Model\ResourceModel\Review\CollectionFactory as ReviewCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SellerReviewRepository implements SellerReviewRepositoryInterface
{
    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;

    /**
     * @var ReviewInterfaceFactory
     */
    protected $dataFactory;

    /**
     * @var ReviewSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var ExtensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;

    /**
     * @var ResourceModelReview
     */
    protected $resource;

    /**
     * @var ReviewCollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ReviewFactory
     */
    protected $modelFactory;

    /**
     * @var SellerCollectionFactory
     */
    protected $sellerCollectionFactory;

    /**
     * SellerReviewRepository constructor.
     * @param ResourceModelReview $resource
     * @param ReviewFactory $modelFactory
     * @param ReviewInterfaceFactory $dataFactory
     * @param ReviewCollectionFactory $collectionFactory
     * @param ReviewSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param SellerCollectionFactory $sellerCollectionFactory
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ResourceModelReview $resource,
        ReviewFactory $modelFactory,
        ReviewInterfaceFactory $dataFactory,
        ReviewCollectionFactory $collectionFactory,
        ReviewSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        SellerCollectionFactory $sellerCollectionFactory
    ) {
        $this->resource = $resource;
        $this->modelFactory = $modelFactory;
        $this->dataFactory = $dataFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->sellerCollectionFactory = $sellerCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getListByUrl(
        string $sellerUrl,
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->sellerCollectionFactory->create();
        $seller = $collection->addFieldToFilter("url_key", $sellerUrl)
                    ->addFieldToFilter("status", Seller::STATUS_ENABLED)
                    ->getFirstItem();

        if ($seller && $seller->getId()) {
            return $this->getList((int)$seller->getId(), $criteria);
        } else {
            throw new NoSuchEntityException(__('Seller with seller Url "%1" does not exist.', $sellerUrl));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        int $sellerId,
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($criteria, $collection);

        $collection->addFieldToFilter('seller_id', $sellerId);
        $collection->addFieldToFilter('status', Review::STATUS_ENABLED);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function getListAll(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function get(int $entityId)
    {
        $model = $this->modelFactory->create();
        $this->resource->load($model, $entityId);
        if (!$model->getId()) {
            throw new NoSuchEntityException(__('Seller Review with id "%1" does not exist.', $entityId));
        }
        return $model->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Lof\MarketPlace\Api\Data\ReviewInterface $review
    ) {
        try {
            $reviewModel = $this->modelFactory->create();
            $this->resource->load($reviewModel, $review->getId());
            $this->resource->delete($reviewModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Seller Review: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($entityId)
    {
        return $this->delete($this->get($entityId));
    }
}
