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

use Lofmp\SellerBadge\Api\Data\SellerBadgeManagerInterfaceFactory;
use Lofmp\SellerBadge\Api\Data\SellerBadgeManagerSearchResultsInterfaceFactory;
use Lofmp\SellerBadge\Api\SellerBadgeManagerRepositoryInterface;
use Lofmp\SellerBadge\Model\ResourceModel\SellerBadgeManager as ResourceSellerBadgeManager;
use Lofmp\SellerBadge\Model\ResourceModel\SellerBadgeManager\CollectionFactory as SellerBadgeManagerCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

class SellerBadgeManagerRepository implements SellerBadgeManagerRepositoryInterface
{
    /**
     * @var ExtensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;

    /**
     * @var ResourceSellerBadgeManager
     */
    protected $resource;

    /**
     * @var SellerBadgeManagerCollectionFactory
     */
    protected $sellerBadgeManagerCollectionFactory;

    /**
     * @var SellerBadgeManagerSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var SellerBadgeManagerInterfaceFactory
     */
    protected $dataSellerBadgeManagerFactory;

    /**
     * @var SellerBadgeManagerFactory
     */
    protected $sellerBadgeManagerFactory;

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
     * @param ResourceSellerBadgeManager $resource
     * @param SellerBadgeManagerFactory $sellerBadgeManagerFactory
     * @param SellerBadgeManagerInterfaceFactory $dataSellerBadgeManagerFactory
     * @param SellerBadgeManagerCollectionFactory $sellerBadgeManagerCollectionFactory
     * @param SellerBadgeManagerSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceSellerBadgeManager $resource,
        SellerBadgeManagerFactory $sellerBadgeManagerFactory,
        SellerBadgeManagerInterfaceFactory $dataSellerBadgeManagerFactory,
        SellerBadgeManagerCollectionFactory $sellerBadgeManagerCollectionFactory,
        SellerBadgeManagerSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->sellerBadgeManagerFactory = $sellerBadgeManagerFactory;
        $this->sellerBadgeManagerCollectionFactory = $sellerBadgeManagerCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataSellerBadgeManagerFactory = $dataSellerBadgeManagerFactory;
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
        \Lofmp\SellerBadge\Api\Data\SellerBadgeManagerInterface $sellerBadgeManager
    ) {
        /* if (empty($sellerBadgeManager->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $sellerBadgeManager->setStoreId($storeId);
        } */

        $sellerBadgeManagerData = $this->extensibleDataObjectConverter->toNestedArray(
            $sellerBadgeManager,
            [],
            \Lofmp\SellerBadge\Api\Data\SellerBadgeManagerInterface::class
        );

        $sellerBadgeManagerModel = $this->sellerBadgeManagerFactory->create()->setData($sellerBadgeManagerData);

        try {
            $this->resource->save($sellerBadgeManagerModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the sellerBadgeManager: %1',
                $exception->getMessage()
            ));
        }
        return $sellerBadgeManagerModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($managerId)
    {
        $sellerBadgeManager = $this->sellerBadgeManagerFactory->create();
        $this->resource->load($sellerBadgeManager, $managerId);
        if (!$sellerBadgeManager->getId()) {
            throw new NoSuchEntityException(__('SellerBadgeManager with id "%1" does not exist.', $managerId));
        }
        return $sellerBadgeManager->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->sellerBadgeManagerCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Lofmp\SellerBadge\Api\Data\SellerBadgeManagerInterface::class
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
        \Lofmp\SellerBadge\Api\Data\SellerBadgeManagerInterface $sellerBadgeManager
    ) {
        try {
            $sellerBadgeManagerModel = $this->sellerBadgeManagerFactory->create();
            $this->resource->load($sellerBadgeManagerModel, $sellerBadgeManager->getManagerId());
            $this->resource->delete($sellerBadgeManagerModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the SellerBadgeManager: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($managerId)
    {
        return $this->delete($this->get($managerId));
    }
}
