<?php
/**
 * LandOfCoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   LandOfCoder
 * @package    Lofmp_Rma
 * @copyright  Copyright (c) 2020 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\Rma\Model;

use Lofmp\Rma\Api\Repository\CustomerRmaRepositoryInterface;
use Lofmp\Rma\Api\Data\RmaSearchResultsInterfaceFactory;
use Lofmp\Rma\Api\Data\RmaInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Lofmp\Rma\Model\ResourceModel\Rma as ResourceRma;
use Lofmp\Rma\Model\ResourceModel\Rma\CollectionFactory as RmaCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\AddressFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;

class CustomerRmaRepository implements CustomerRmaRepositoryInterface
{

    protected $resource;

    protected $rmaFactory;

    protected $rmaCollectionFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $dataRmaFactory;

    private $storeManager;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * @var \Lofmp\Rma\Helper\Help
     */
    protected $helper;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Lofmp\Rma\Model\ItemFactory
     */
    protected $itemFactory;

    /**
     * @param ResourceRma $resource
     * @param RmaFactory $rmaFactory
     * @param RmaInterfaceFactory $dataRmaFactory
     * @param RmaCollectionFactory $rmaCollectionFactory
     * @param RmaSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CustomerFactory $customerFactory
     * @param AddressFactory $addressFactory
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Lofmp\Rma\Helper\Help $helper
     * @param ProductRepositoryInterface $productRepository
     * @param \Lofmp\Rma\Model\ItemFactory $itemFactory
     */
    public function __construct(
        ResourceRma $resource,
        RmaFactory $rmaFactory,
        RmaInterfaceFactory $dataRmaFactory,
        RmaCollectionFactory $rmaCollectionFactory,
        RmaSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CustomerFactory $customerFactory,
        AddressFactory $addressFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Lofmp\Rma\Helper\Help $helper,
        ProductRepositoryInterface $productRepository,
        \Lofmp\Rma\Model\ItemFactory $itemFactory
    ) {
        $this->resource = $resource;
        $this->rmaFactory = $rmaFactory;
        $this->rmaCollectionFactory = $rmaCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataRmaFactory = $dataRmaFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->addressFactory = $addressFactory;
        $this->customerFactory = $customerFactory;
        $this->orderFactory = $orderFactory;
        $this->helper = $helper;
        $this->productRepository = $productRepository;
        $this->itemFactory = $itemFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save($customerId, \Lofmp\Rma\Api\Data\RmaInterface $rma)
    {
        if (!$customerId) {
            throw new NoSuchEntityException(__('You should login with your account.'));
        }
        if (!$rma->getOrderId()) {
            throw new NoSuchEntityException(__('Missing required field order_id.'));
        }
        if (!$rma->getSellerId()) {
            throw new NoSuchEntityException(__('Missing required field seller_id.'));
        }
        if (!empty($rma->getRmaId())) {
            throw new CouldNotSaveException(__('Please dont use rma_id field when create new RMA.'));
        }

        try {
            $rmaCustomerId = $rma->getCustomerId();
            if ($customerId == $rmaCustomerId) {
                $this->resource->save($rma);
            } else {
                throw new CouldNotSaveException(__(
                    'Could not save the rma, because wrong Customer ID'
                ));
            }
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the rma: %1',
                $exception->getMessage()
            ));
        }
        return $rma;
    }

    /**
     * {@inheritdoc}
     */
    public function saveRma($customerId, \Lofmp\Rma\Api\Data\RmaFrontendInterface $rma)
    {
        if (!$customerId) {
            throw new NoSuchEntityException(__('You should login with your account.'));
        }
        if (!$rma->getOrderId()) {
            throw new NoSuchEntityException(__('Missing required field order_id.'));
        }
        if (!$rma->getSellerId()) {
            throw new NoSuchEntityException(__('Missing required field seller_id.'));
        }
        if (!$rma->getItems()) {
            throw new NoSuchEntityException(__('Missing required field items.'));
        }

        $customer = $this->customerFactory->create()->load((int)$customerId);
        if (!$customer || ($customer && !$customer->getId()) ) {
            throw new CouldNotSaveException(__('Customer account is not exists.'));
        }
        $order = $this->orderFactory->create()->load($rma->getOrderId());
        if ($order->getCustomerId() != $customerId) {
            throw new CouldNotSaveException(__(
                'Could not save the RMA request because current customer is different than order customer.'
            ));
        }

        $rmaModel = $this->rmaFactory->create();

        try {

            if (!empty($rma->getRmaId())) {
                $this->resource->load($rmaModel, (int)$rma->getRmaId());
                if ($rmaModel->getId() && $rmaModel->getCustomerId() != $customerId) {
                    throw new NoSuchEntityException(__('The rma ID %1 is not available for this customer.', $rma->getRmaId()));
                }
            }
            $rmaModel->setCustomerId($customerId);
            $rmaModel->setStoreId($order->getStoreId());
            $rmaModel->setStatusId($this->helper->getConfig($order->getStoreId(), 'rma/general/default_status'));
            $rmaModel->setParentRmaId(0);

            $this->resource->save($rmaModel);

            $parentRmaId = $rmaModel->getId();
            // save rma items
            $this->saveRmaItem($parentRmaId, $order, $rma);

        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the rma: %1',
                $exception->getMessage()
            ));
        }
        return $rmaModel;
    }

    /**
     * {@inheritdoc}
     */
    public function saveBundle($customerId, \Lofmp\Rma\Api\Data\RmaInterface $rma)
    {
        if(!$customerId){
            throw new NoSuchEntityException(__('You should login with your account.'));
        }
        if (!$rma->getOrderId()) {
            throw new NoSuchEntityException(__('Missing required field order_id.'));
        }
        if (!$rma->getSellerId()) {
            throw new NoSuchEntityException(__('Missing required field seller_id.'));
        }
        if (!empty($rma->getRmaId())) {
            throw new CouldNotSaveException(__('Please dont use rma_id field when create new RMA.'));
        }
        $order = $this->orderFactory->create()->load($rma->getOrderId());
        if ($order->getCustomerId() != $customerId) {
            throw new CouldNotSaveException(__(
                'Could not save the RMA request because current customer is different than order customer.'
            ));
        }
        try {
            $rmaCustomerId = $rma->getCustomerId();
            if ($customerId == $rmaCustomerId) {
                $rma->getResource()->save($rma);
            } else {
                throw new CouldNotSaveException(__(
                    'Could not save the bundle rma, because wrong Customer ID'
                ));
            }
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the bundle rma: %1',
                $exception->getMessage()
            ));
        }
        return $rma;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($customerId, $rmaId)
    {
        if (!$customerId) {
            throw new NoSuchEntityException(__('You should login with your account.'));
        }
        $rma = $this->rmaFactory->create();
        $rma->getResource()->load($rma, $rmaId);

        if (!$rma->getId()) {
            throw new NoSuchEntityException(__('rma with id "%1" does not exist.', $rmaId));
        }
        $rmaCustomerId = $rma->getCustomerId();
        if ($customerId != $rmaCustomerId) {
            throw new NoSuchEntityException(__('rma with id "%1" does not exist for this Customer.', $rmaId));
        }
        return $rma;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        $customerId,
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        if (!$customerId) {
            throw new NoSuchEntityException(__('You should login with your account.'));
        }
        $collection = $this->rmaCollectionFactory->create();

        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue(), false);
                    continue;
                }
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        //Add filter for this customer ID
        $collection->addFieldToFilter("main_table.customer_id", $customerId);

        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setItems($collection->getItems());
        return $searchResults;
    }

    /**
     * save rma item
     *
     * @param int $parentRmaId
     * @param mixed $order
     * @param \Lofmp\Rma\Api\Data\RmaFrontendInterface $rma
     */
    protected function saveRmaItem($parentRmaId, $order, $rma)
    {
        $itemCollection = $order->getItemsCollection();
        $itemdatas = [];

        foreach ($rma->getItems() as $item) {
            $dataItem = [
                'qty_requested' => $item->getQtyRequested(),
                'reason_id' => $item->getReasonId(),
                'resolution_id' => $item->getResolutionId(),
                'condition_id' => $item->getConditionId(),
                'order_item_id' => $item->getOrderItemId(),
                'order_id' => $item->getOrderId(),
                'rma_id' => $parentRmaId
            ];
            if (empty($item->getReasonId())) {
                $dataItem['qty_requested'] = 0;
            }
            if (empty($dataItem['resolution_id'])) {
                unset($dataItem['resolution_id']);
            }
            if (empty($dataItem['condition_id'])) {
                unset($dataItem['condition_id']);
            }
            if (!empty($item->getItemId())) {
                $dataItem['item_id'] = $item->getItemId();
            }
            $orderItem = $itemCollection->getItemById($item->getOrderItemId());
            if ($orderItem && $orderItem->getProductId()) {
                $productId = $orderItem->getProductId();
                if (!$productId) {
                    $product   = $this->productRepository->get($orderItem->getSku());
                    $productId = $product->getId();
                }
                $dataItem['product_id'] = $productId;
            }
            $itemdatas[] = $dataItem;
        }

        foreach ($itemdatas as $item) {
            try {
                $itemModel = $this->itemFactory->create();
                if (isset($item['item_id']) && !empty($item['item_id'])) {
                    $itemModel->load((int) $item['item_id']);
                }
                unset($item['item_id']);
                $items->addData($item)
                        ->setRmaId($item['rma_id'])
                        ->save();
            } catch (\Exception $exception) {
                throw new CouldNotSaveException(__(
                    'Could not save the RMA Item because issue: %1',
                    $exception->getMessage()
                ));
            }
        }
    }
}
