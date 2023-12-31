<?php
/**
 * Copyright © landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\MarketPlace\Model;

use Lof\MarketPlace\Api\AmountTransactionRepositoryInterface;
use Lof\MarketPlace\Api\Data\AmountTransactionInterface;
use Lof\MarketPlace\Api\Data\AmountTransactionInterfaceFactory;
use Lof\MarketPlace\Api\Data\AmountTransactionSearchResultsInterfaceFactory;
use Lof\MarketPlace\Model\ResourceModel\Amounttransaction as ResourceAmountTransaction;
use Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory as SellerCollectionFactory;
use Lof\MarketPlace\Model\ResourceModel\Amounttransaction\CollectionFactory as AmountTransactionCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class AmountTransactionRepository implements AmountTransactionRepositoryInterface
{

    /**
     * @var AmountTransactionInterfaceFactory
     */
    protected $amountTransactionFactory;

    /**
     * @var ResourceAmountTransaction
     */
    protected $resource;

    /**
     * @var Amounttransaction
     */
    protected $searchResultsFactory;

    /**
     * @var AmountTransactionCollectionFactory
     */
    protected $amountTransactionCollectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var SellerCollectionFactory
     */
    protected $sellerCollectionFactory;

    /**
     * @param ResourceAmountTransaction $resource
     * @param AmountTransactionInterfaceFactory $amountTransactionFactory
     * @param AmountTransactionCollectionFactory $amountTransactionCollectionFactory
     * @param AmountTransactionSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param SellerCollectionFactory $sellerCollectionFactory
     */
    public function __construct(
        ResourceAmountTransaction $resource,
        AmountTransactionInterfaceFactory $amountTransactionFactory,
        AmountTransactionCollectionFactory $amountTransactionCollectionFactory,
        AmountTransactionSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor,
        SellerCollectionFactory $sellerCollectionFactory
    ) {
        $this->resource = $resource;
        $this->amountTransactionFactory = $amountTransactionFactory;
        $this->amountTransactionCollectionFactory = $amountTransactionCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->sellerCollectionFactory = $sellerCollectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function getSellerTransactions(int $customerId, \Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        $seller = $this->getSellerByCustomerId($customerId);
        if ($seller && $seller->getId()) {

            $collection = $this->amountTransactionCollectionFactory->create();

            $this->collectionProcessor->process($criteria, $collection);
            $collection->addFieldToFilter("seller_id", $seller->getId());

            $searchResults = $this->searchResultsFactory->create();
            $searchResults->setSearchCriteria($criteria);
            $searchResults->setItems($collection->getItems());
            $searchResults->setTotalCount($collection->getSize());
            return $searchResults;
        } else {
            throw new NoSuchEntityException(__('Seller account is not exists.'));
        }
    }

    /**
     * @inheritDoc
     */
    public function save(
        AmountTransactionInterface $amountTransaction
    ) {
        try {
            $this->resource->save($amountTransaction);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the amountTransaction: %1',
                $exception->getMessage()
            ));
        }
        return $amountTransaction;
    }

    /**
     * @inheritDoc
     */
    public function get($id)
    {
        $amountTransaction = $this->amountTransactionFactory->create();
        $this->resource->load($amountTransaction, $id);
        if (!$amountTransaction->getId()) {
            throw new NoSuchEntityException(__('AmountTransaction with id "%1" does not exist.', $id));
        }
        return $amountTransaction;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->amountTransactionCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(
        AmountTransactionInterface $amountTransaction
    ) {
        try {
            $amountTransactionModel = $this->amountTransactionFactory->create();
            $this->resource->load($amountTransactionModel, $amountTransaction->getAmounttransactionId());
            $this->resource->delete($amountTransactionModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the AmountTransaction: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($id)
    {
        return $this->delete($this->get($id));
    }

    /**
     * get seller by customer id
     *
     * @param int $customerId
     * @return \Lof\MarketPlace\Model\Seller
     */
    protected function getSellerByCustomerId(int $customerId)
    {
        $seller = $this->sellerCollectionFactory->create()
                    ->addFieldToFilter("customer_id", $customerId)
                    ->addFieldToFilter("status", Seller::STATUS_ENABLED)
                    ->getFirstItem();
        return $seller;
    }
}

