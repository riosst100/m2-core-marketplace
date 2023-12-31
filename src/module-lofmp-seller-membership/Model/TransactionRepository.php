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
 * @package    Lofmp_SellerMembership
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerMembership\Model;

use Lofmp\SellerMembership\Api\TransactionRepositoryInterface;
use Lofmp\SellerMembership\Api\Data\TransactionSearchResultsInterfaceFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Lofmp\SellerMembership\Helper\Data as HelperData;

class TransactionRepository implements TransactionRepositoryInterface
{
    /**
     * @var \Lofmp\SellerMembership\Model\TransactionFactory
     */
    protected $_transactionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $_collectionProcessor;

    /**
     * @var TransactionSearchResultsInterfaceFactory
     */
    protected $_searchResultsFactory;

    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * TransactionRepository constructor.
     * @param \Lofmp\SellerMembership\Model\TransactionFactory $transactionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param TransactionSearchResultsInterfaceFactory $searchResultsFactory
     * @param HelperData $helperData
     */
    public function __construct(
        TransactionFactory $transactionFactory,
        CollectionProcessorInterface $collectionProcessor,
        TransactionSearchResultsInterfaceFactory $searchResultsFactory,
        HelperData $helperData
    ) {
        $this->_transactionFactory = $transactionFactory;
        $this->_collectionProcessor = $collectionProcessor;
        $this->_searchResultsFactory = $searchResultsFactory;
        $this->_helperData = $helperData;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Lofmp\SellerMembership\Api\Data\TransactionInterface $transaction_data)
    {
        try {
            if ($transaction_data->getId()) {
                $transaction_model = $this->_transactionFactory->create()->load($transaction_data->getId());
                $transaction_model->addData($transaction_data->getData());
            } else {
                $transaction_model = $this->_transactionFactory->create();
                $transaction_model->addData($transaction_data->getData());
            }

            $transaction_model->save();
            $transaction_id = $transaction_model->getId();
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Could not save the transaction: %1', $exception->getMessage()));
        }

        return $this->_transactionFactory->create()->load($transaction_id);
    }

    /**
     * {@inheritdoc}
     */
    public function getById($transactionId)
    {
        $transaction_model = $this->_transactionFactory->create();
        $transaction_model->load($transactionId);

        if (!$transaction_model->getId()) {
            throw new NoSuchEntityException(__('Transaction with id "%1" does not exist.', $transactionId));
        } else {
//            $this->_helperData->setTransactionMoreData($transaction_model);
        }

        return $transaction_model;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $transaction_collection = $this->_transactionFactory->create()->getCollection();
        $this->_collectionProcessor->process($searchCriteria, $transaction_collection);
        $searchResults = $this->_searchResultsFactory->create();

        $searchResults->setSearchCriteria($searchCriteria);

        $transaction_array = [];
        foreach ($transaction_collection as $transaction_model) {
//            $this->_helperData->setTransactionMoreData($transaction_model);
            $transaction_array[] = $transaction_model;
        }
        $searchResults->setItems($transaction_array);

        $searchResults->setTotalCount($transaction_collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(\Lofmp\SellerMembership\Api\Data\TransactionInterface $transaction_data)
    {
        try {
            $transaction_resource = $this->_transactionFactory->create()->getResource();
            $transaction_resource->delete($transaction_data);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__('Could not delete the transaction : %1', $exception->getMessage()));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($transactionId)
    {
        try {
            $transaction_model = $this->_transactionFactory->create();

            $transaction_model->load($transactionId);

            if (!$transaction_model->getId()) {
                throw new NoSuchEntityException(__('Transaction with id "%1" does not exist.', $transactionId));
            }

            $transaction_model->delete();
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__('Could not delete the transaction: %1', $exception->getMessage()));
        }
        return true;
    }
}
