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
 * @package    Lofmp_SellerBadge
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerBadge\Model;

use Lofmp\SellerBadge\Api\Data\SellerBadgeInterfaceFactory;
use Lofmp\SellerBadge\Api\Data\SellerBadgeSearchResultsInterfaceFactory;
use Lofmp\SellerBadge\Api\SellerBadgeRepositoryInterface;
use Lofmp\SellerBadge\Model\ResourceModel\SellerBadge as ResourceSellerBadge;
use Lofmp\SellerBadge\Model\ResourceModel\SellerBadge\CollectionFactory as SellerBadgeCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

class SellerBadgeRepository implements SellerBadgeRepositoryInterface
{

    /**
     * @var ResourceSellerBadge
     */
    protected $resource;

    /**
     * @var ExtensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;

    /**
     * @var SellerBadgeSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var SellerBadgeFactory
     */
    protected $sellerBadgeFactory;

    /**
     * @var SellerBadgeInterfaceFactory
     */
    protected $dataSellerBadgeFactory;

    /**
     * @var SellerBadgeCollectionFactory
     */
    protected $sellerBadgeCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @param ResourceSellerBadge $resource
     * @param SellerBadgeFactory $sellerBadgeFactory
     * @param SellerBadgeInterfaceFactory $dataSellerBadgeFactory
     * @param SellerBadgeCollectionFactory $sellerBadgeCollectionFactory
     * @param SellerBadgeSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceSellerBadge $resource,
        SellerBadgeFactory $sellerBadgeFactory,
        SellerBadgeInterfaceFactory $dataSellerBadgeFactory,
        SellerBadgeCollectionFactory $sellerBadgeCollectionFactory,
        SellerBadgeSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->sellerBadgeFactory = $sellerBadgeFactory;
        $this->sellerBadgeCollectionFactory = $sellerBadgeCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataSellerBadgeFactory = $dataSellerBadgeFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Lofmp\SellerBadge\Api\Data\SellerBadgeInterface $sellerBadge
    ) {
        /* if (empty($sellerBadge->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $sellerBadge->setStoreId($storeId);
        } */

        $sellerBadgeData = $this->extensibleDataObjectConverter->toNestedArray(
            $sellerBadge,
            [],
            \Lofmp\SellerBadge\Api\Data\SellerBadgeInterface::class
        );

        $sellerBadgeModel = $this->sellerBadgeFactory->create()->setData($sellerBadgeData);

        try {
            $this->resource->save($sellerBadgeModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the sellerBadge: %1',
                $exception->getMessage()
            ));
        }
        return $sellerBadgeModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($badgeId)
    {
        $sellerBadge = $this->sellerBadgeFactory->create();
        $this->resource->load($sellerBadge, $badgeId);
        if (!$sellerBadge->getId()) {
            throw new NoSuchEntityException(__('SellerBadge with id "%1" does not exist.', $badgeId));
        }
        return $sellerBadge->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->sellerBadgeCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Lofmp\SellerBadge\Api\Data\SellerBadgeInterface::class
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

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Lofmp\SellerBadge\Api\Data\SellerBadgeInterface $sellerBadge
    ) {
        try {
            $sellerBadgeModel = $this->sellerBadgeFactory->create();
            $this->resource->load($sellerBadgeModel, $sellerBadge->getBadgeId());
            $this->resource->delete($sellerBadgeModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the SellerBadge: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($badgeId)
    {
        return $this->delete($this->get($badgeId));
    }
}
