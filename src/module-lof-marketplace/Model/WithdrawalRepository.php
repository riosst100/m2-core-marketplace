<?php
/**
 * Copyright © teads All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\MarketPlace\Model;

use Lof\MarketPlace\Api\Data\WithdrawalInterface;
use Lof\MarketPlace\Api\Data\WithdrawalInterfaceFactory;
use Lof\MarketPlace\Api\Data\WithdrawalSearchResultsInterfaceFactory;
use Lof\MarketPlace\Api\WithdrawalRepositoryInterface;
use Lof\MarketPlace\Api\PaymentRepositoryInterface;
use Lof\MarketPlace\Model\ResourceModel\Withdrawal as ResourceWithdrawal;
use Lof\MarketPlace\Model\ResourceModel\Withdrawal\CollectionFactory as WithdrawalCollectionFactory;
use Lof\MarketPlace\Model\ResourceModel\Amount\CollectionFactory as AmountCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class WithdrawalRepository implements WithdrawalRepositoryInterface
{

    /**
     * @var ResourceWithdrawal
     */
    protected $resource;

    /**
     * @var WithdrawalCollectionFactory
     */
    protected $withdrawalCollectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var WithdrawalInterfaceFactory
     */
    protected $withdrawalFactory;

    /**
     * @var Withdrawal
     */
    protected $searchResultsFactory;

    /**
     * @var SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var PaymentRepositoryInterface
     */
    protected $paymentRepository;

    /**
     * @var AmountCollectionFactory
     */
    protected $sellerAmountFactory;

    /**
     * @param ResourceWithdrawal $resource
     * @param WithdrawalInterfaceFactory $withdrawalFactory
     * @param WithdrawalCollectionFactory $withdrawalCollectionFactory
     * @param WithdrawalSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param SellerFactory $sellerFactory
     * @param PaymentRepositoryInterface $paymentRepository
     * @param AmountCollectionFactory $sellerAmountFactory
     */
    public function __construct(
        ResourceWithdrawal $resource,
        WithdrawalInterfaceFactory $withdrawalFactory,
        WithdrawalCollectionFactory $withdrawalCollectionFactory,
        WithdrawalSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor,
        SellerFactory $sellerFactory,
        PaymentRepositoryInterface $paymentRepository,
        AmountCollectionFactory $sellerAmountFactory
    ) {
        $this->resource = $resource;
        $this->withdrawalFactory = $withdrawalFactory;
        $this->withdrawalCollectionFactory = $withdrawalCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->sellerFactory = $sellerFactory;
        $this->paymentRepository = $paymentRepository;
        $this->sellerAmountFactory = $sellerAmountFactory;
    }

    /**
     * @inheritDoc
     */
    public function save(WithdrawalInterface $withdrawal)
    {
        if (!$withdrawal->getSellerId() || !$withdrawal->getPaymentId() || !$withdrawal->getAmount()) {
            throw new CouldNotSaveException(__(
                'Could not save the withdrawal: missing one of fields seller_id, payment_id, amount'
            ));
        }
        try {
            $sellerId = $withdrawal->getSellerId();
            $sellerAmount = $this->sellerAmountFactory->create()
                                ->addFieldToFilter("seller_id", $sellerId)
                                ->getFirstItem();
            $sellerBalance = $sellerAmount && $sellerAmount->getAmountId() ? (float)$sellerAmount->getAmount() : 0;

            $payment = $this->paymentRepository->get($withdrawal->getPaymentId());
            if (!$payment->getPaymentId()) {
                throw new NoSuchEntityException(__('Payment with id %1 is not exists.', $withdrawal->getPaymentId()));
            }

            $amount = (float)$withdrawal->getAmount();
            $fee = (float)$payment->getFee();
            $minAmount = (float)$payment->getMinAmount();
            $maxAmount = (float)$payment->getMaxAmount();
            $comment = $withdrawal->getComment();
            $comment = $comment ? strip_tags($comment) : "";
            $fee_by = $payment->getFeeBy();
            $fee_percent = (float)$payment->getFeePercent();

            if ($fee_by == 'all') {
                $fee = (float)$fee + (float)$amount * (float)$fee_percent / 100;
            } elseif ($fee_by == 'by_fixed') {
                $fee = (float)$payment->getFee();
            } else {
                $fee = (float)$amount * (float)$fee_percent / 100;
            }
            $netAmount = (float)$amount - (float)$fee;

            if (($minAmount <= $amount && $amount <= $maxAmount)
                    && ($amount <= $sellerBalance)
                ) {
                $withdrawal->setSellerId($sellerId);
                $withdrawal->setFee($fee);
                $withdrawal->setNetAmount($netAmount);
                $withdrawal->setStatus(Withdrawal::STATUS_PENDING);

                $this->resource->save($withdrawal);
            } else {
                throw new CouldNotSaveException(__(
                    'Could not submit request the withdrawal, your balanace is not available!'
                ));
            }
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not submit request the withdrawal: %1',
                $exception->getMessage()
            ));
        }
        return $withdrawal;
    }

    /**
     * @inheritDoc
     */
    public function requestWithdrawal(int $customerId, WithdrawalInterface $withdrawal)
    {
        $seller = $this->getSellerByCustomer($customerId);

        if ($seller && $seller->getId()) {
            $withdrawal->setSellerId($seller->getId());
            return $this->save($withdrawal);
        } else {
            throw new NoSuchEntityException(__('Seller with customerId "%1" does not exist.', $customerId));
        }
    }

    /**
     * @inheritDoc
     */
    public function get($withdrawalId)
    {
        $withdrawal = $this->withdrawalFactory->create();
        $this->resource->load($withdrawal, $withdrawalId);
        if (!$withdrawal->getId()) {
            throw new NoSuchEntityException(__('Withdrawal with id "%1" does not exist.', $withdrawalId));
        }
        return $withdrawal;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->withdrawalCollectionFactory->create();

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
     * @inheritDoc
     */
    public function getMyList(
        int $customerId,
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $seller = $this->getSellerByCustomer($customerId);

        if ($seller && $seller->getId()) {
            $collection = $this->withdrawalCollectionFactory->create();

            $this->collectionProcessor->process($criteria, $collection);

            $collection->addFieldToFilter("seller_id", $seller->getId());

            $searchResults = $this->searchResultsFactory->create();
            $searchResults->setSearchCriteria($criteria);

            $items = [];
            foreach ($collection as $model) {
                $items[] = $model;
            }

            $searchResults->setItems($items);
            $searchResults->setTotalCount($collection->getSize());
            return $searchResults;
        } else {
            throw new NoSuchEntityException(__('Seller with customerId "%1" does not exist.', $customerId));
        }
    }

    /**
     * @inheritDoc
     */
    public function delete(WithdrawalInterface $withdrawal)
    {
        try {
            $withdrawalModel = $this->withdrawalFactory->create();
            $this->resource->load($withdrawalModel, $withdrawal->getWithdrawalId());
            $this->resource->delete($withdrawalModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Withdrawal: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($withdrawalId)
    {
        return $this->delete($this->get($withdrawalId));
    }

    /**
     * get seller by customer id
     *
     * @param int $customerId
     * @return Seller
     */
    protected function getSellerByCustomer(int $customerId)
    {
        $seller = $this->sellerFactory->create()->getCollection()
                ->addFieldToFilter("customer_id", $customerId)
                ->addFieldToFilter("status", Seller::STATUS_ENABLED)
                ->getFirstItem();
        return $seller;
    }
}

