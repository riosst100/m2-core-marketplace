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
 * @package    Lof_Quickrfq
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
declare(strict_types=1);

namespace Lof\Quickrfq\Model;

use Lof\Quickrfq\Api\Data\QuickrfqInterface;
use Lof\Quickrfq\Api\Data\QuickrfqInterfaceFactory;
use Lof\Quickrfq\Api\Data\QuickrfqSearchResultsInterfaceFactory;
use Lof\Quickrfq\Api\QuickrfqRepositoryInterface;
use Lof\Quickrfq\Model\ResourceModel\Quickrfq as ResourceQuickrfq;
use Lof\Quickrfq\Model\ResourceModel\Quickrfq\CollectionFactory as QuickrfqCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class QuickrfqRepository
 * @package Lof\Quickrfq\Model
 */
class QuickrfqRepository implements QuickrfqRepositoryInterface
{
    /**
     * @var ResourceQuickrfq
     */
    protected $resource;

    /**
     * @var QuickrfqInterfaceFactory
     */
    protected $dataQuickrfqFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var ExtensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;
    /**
     * @var QuickrfqCollectionFactory
     */
    protected $quickrfqCollectionFactory;

    /**
     * @var QuickrfqFactory
     */
    protected $quickrfqFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var QuickrfqSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;


    /**
     * @param ResourceQuickrfq $resource
     * @param QuickrfqFactory $quickrfqFactory
     * @param QuickrfqInterfaceFactory $dataQuickrfqFactory
     * @param QuickrfqCollectionFactory $quickrfqCollectionFactory
     * @param QuickrfqSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceQuickrfq $resource,
        QuickrfqFactory $quickrfqFactory,
        QuickrfqInterfaceFactory $dataQuickrfqFactory,
        QuickrfqCollectionFactory $quickrfqCollectionFactory,
        QuickrfqSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->quickrfqFactory = $quickrfqFactory;
        $this->quickrfqCollectionFactory = $quickrfqCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataQuickrfqFactory = $dataQuickrfqFactory;
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
        QuickrfqInterface $quickrfq
    ) {
        $quickrfqData = $this->extensibleDataObjectConverter->toNestedArray(
            $quickrfq,
            [],
            QuickrfqInterface::class
        );

        $quickrfqModel = $this->quickrfqFactory->create()->setData($quickrfqData);

        try {
            $this->resource->save($quickrfqModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the quickrfq: %1',
                $exception->getMessage()
            ));
        }
        return $quickrfqModel;
    }
    /**
     * {@inheritdoc}
     */
    public function update(
        $quickrfq_id,
        QuickrfqInterface $quickrfq
    ) {
        $quickrfqData = $this->extensibleDataObjectConverter->toNestedArray(
            $quickrfq,
            [],
            QuickrfqInterface::class
        );
        $quickrfqModel = $this->quickrfqFactory->create();
        if ($quickrfq_id) {
            $quickrfqModel->load($quickrfq_id);
            $quickrfqData['quickrfq_id'] = $quickrfq_id;
            $quickrfqModel->setData($quickrfqData);
            try {
                $this->resource->save($quickrfqModel);
            } catch (\Exception $exception) {
                throw new CouldNotSaveException(__(
                    'Could not save the quickrfq: %1',
                    $exception->getMessage()
                ));
            }
            return $quickrfqModel;
        } else {
            return 'Column quickrfq_id not found to update';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get($quickrfqId)
    {
        $quickrfq = $this->quickrfqFactory->create();
        $this->resource->load($quickrfq, $quickrfqId);
        if (!$quickrfq->getId()) {
            throw new NoSuchEntityException(__('Quickrfq with id "%1" does not exist.', $quickrfqId));
        }
        return $quickrfq;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->quickrfqCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            QuickrfqInterface::class
        );

        $this->collectionProcessor->process($criteria, $collection);

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
     * {@inheritdoc}
     */
    public function delete(
        QuickrfqInterface $quickrfq
    ) {
        try {
            $quickrfqModel = $this->quickrfqFactory->create();
            $this->resource->load($quickrfqModel, $quickrfq->getQuickrfqId());
            $this->resource->delete($quickrfqModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Quickrfq: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($quickrfqId)
    {
        try {
            $quickrfqModel = $this->quickrfqFactory->create();
            $this->resource->load($quickrfqModel, $quickrfqId);
            $this->resource->delete($quickrfqModel);
        } catch (\Exception $exception) {
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Quickrfq: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }
}
