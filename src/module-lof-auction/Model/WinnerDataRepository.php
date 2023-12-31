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

use Lof\Auction\Api\Data\WinnerDataInterfaceFactory as DataInterfaceFactory;
use Lof\Auction\Api\Data\WinnerDataSearchResultsInterfaceFactory as DataSearchResultsInterfaceFactory;
use Lof\Auction\Api\WinnerDataRepositoryInterface;
use Lof\Auction\Model\ResourceModel\WinnerData as ResourceWinnerData;
use Lof\Auction\Model\ResourceModel\WinnerData\CollectionFactory as ModelCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class WinnerDataRepository
 * @package Lof\Auction\Model
 */
class WinnerDataRepository implements WinnerDataRepositoryInterface
{

    /**
     * @var ResourceWinnerData
     */
    protected $resource;

    /**
     * @var WinnerDataFactory
     */
    protected $winnerdataFactory;

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
     * @param ResourceWinnerData $resource
     * @param WinnerDataFactory $winnerdataFactory
     * @param DataInterfaceFactory $dataApiFactory
     * @param ModelCollectionFactory $modelCollectionFactory
     * @param DataSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceWinnerData $resource,
        WinnerDataFactory $winnerdataFactory,
        DataInterfaceFactory $dataApiFactory,
        ModelCollectionFactory $modelCollectionFactory,
        DataSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        ResourceConnection $resourceConnection
    ) {
        $this->resource = $resource;
        $this->winnerdataFactory = $winnerdataFactory;
        $this->dataApiFactory = $dataApiFactory;
        $this->modelCollectionFactory = $modelCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->_resource = $resourceConnection;
    }

    /**
     * {@inheritdoc}
     */
    public function save($winnerdata)
    {
        $dataModel = $this->winnerdataFactory->create();
        if ($dataModel->getEntityId()) {
            $dataModel->load((int)$dataModel->getEntityId());
        }
        try {
            $dataModel->setData((array)$winnerdata);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the auction winner data: %1',
                $exception->getMessage()
            ));
        }
        return $dataModel;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($entityId)
    {
        $dataModel = $this->winnerdataFactory->create();
        $dataModel->load($entityId);
        if (!$dataModel->getId()) {
            throw new NoSuchEntityException(__('Auction winner data with id "%1" does not exist.', $entityId));
        }
        return $dataModel;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->modelCollectionFactory->create();

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
    public function delete($entityId)
    {
        try {
            $dataModel = $this->winnerdataFactory->create();
            $dataModel->load($entityId);
            $dataModel->delete();
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Auction Winner Data: %1',
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
        $dataModel = $this->getById($entityId);
        return $this->delete($dataModel);
    }
}
