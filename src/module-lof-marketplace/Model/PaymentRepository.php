<?php
/**
 * Copyright © teads All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\MarketPlace\Model;

use Lof\MarketPlace\Api\Data\PaymentInterface;
use Lof\MarketPlace\Api\Data\PaymentInterfaceFactory;
use Lof\MarketPlace\Api\Data\PaymentSearchResultsInterfaceFactory;
use Lof\MarketPlace\Api\PaymentRepositoryInterface;
use Lof\MarketPlace\Model\ResourceModel\Payment as ResourcePayment;
use Lof\MarketPlace\Model\ResourceModel\Payment\CollectionFactory as PaymentCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class PaymentRepository implements PaymentRepositoryInterface
{

    /**
     * @var PaymentInterfaceFactory
     */
    protected $paymentFactory;

    /**
     * @var Payment
     */
    protected $searchResultsFactory;

    /**
     * @var ResourcePayment
     */
    protected $resource;

    /**
     * @var PaymentCollectionFactory
     */
    protected $paymentCollectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var SellerFactory
     */
    protected $sellerFactory;


    /**
     * @param ResourcePayment $resource
     * @param PaymentInterfaceFactory $paymentFactory
     * @param PaymentCollectionFactory $paymentCollectionFactory
     * @param PaymentSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param SellerFactory $sellerFactory
     */
    public function __construct(
        ResourcePayment $resource,
        PaymentInterfaceFactory $paymentFactory,
        PaymentCollectionFactory $paymentCollectionFactory,
        PaymentSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor,
        SellerFactory $sellerFactory
    ) {
        $this->resource = $resource;
        $this->paymentFactory = $paymentFactory;
        $this->paymentCollectionFactory = $paymentCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->sellerFactory = $sellerFactory;
    }

    /**
     * @inheritDoc
     */
    public function save(PaymentInterface $payment)
    {
        try {
            $this->resource->save($payment);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the payment: %1',
                $exception->getMessage()
            ));
        }
        return $payment;
    }

    /**
     * @inheritDoc
     */
    public function get($paymentId)
    {
        $payment = $this->paymentFactory->create();
        $this->resource->load($payment, $paymentId);
        if (!$payment->getId()) {
            throw new NoSuchEntityException(__('Payment with id "%1" does not exist.', $paymentId));
        }
        return $payment;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->paymentCollectionFactory->create();

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
    public function getPublicList(
        int $customerId,
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $seller = $this->getSellerByCustomer($customerId);

        if ($seller && $seller->getId()) {
            $collection = $this->paymentCollectionFactory->create();

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
        } else {
            throw new NoSuchEntityException(__('Seller with customerId "%1" does not exist.', $customerId));
        }
    }

    /**
     * @inheritDoc
     */
    public function delete(PaymentInterface $payment)
    {
        try {
            $paymentModel = $this->paymentFactory->create();
            $this->resource->load($paymentModel, $payment->getPaymentId());
            $this->resource->delete($paymentModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Payment: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($paymentId)
    {
        return $this->delete($this->get($paymentId));
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

