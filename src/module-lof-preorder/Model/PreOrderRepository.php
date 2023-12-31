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
 * @package    Lof_PreOrder
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\PreOrder\Model;

use Lof\PreOrder\Api\PreOrderRepositoryInterface;
use Lof\PreOrder\Api\Data\PreOrderSearchResultsInterfaceFactory;
use Lof\PreOrder\Api\Data\PreOrderInterfaceFactory;
use Lof\PreOrder\Helper\Data as PreOrderHelperData;
use Lof\PreOrder\Model\ResourceModel\Complete\CollectionFactory as CompleteCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Lof\PreOrder\Model\ResourceModel\PreOrder as ResourcePreOrder;
use Lof\PreOrder\Model\ResourceModel\PreOrder\CollectionFactory as PreOrderCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as Products;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Lof\PreOrder\Model\ResourceModel\Item\CollectionFactory as Items;
use Magento\Quote\Api\CartRepositoryInterface;

class PreOrderRepository implements PreOrderRepositoryInterface
{

    protected $resource;

    protected $preorderFactory;

    protected $preorderCollectionFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $dataPreorderFactory;

    protected $storeManager;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_config;

    protected $preorderHelperData;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    protected $quoteFactory;

    /**
     * @var \Lof\PreOrder\Model\ItemFactory
     */
    protected $_item;

    /**
     * @var Items
     */
    protected $_itemCollection;

    /**
     * @var CompleteCollectionFactory
     */
    protected $completeCollectionFactory;

    /**
     * @var \Lof\PreOrder\Model\CompleteFactory
     */
    protected $_completeFactory;

    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @param ResourcePreOrder $resource
     * @param PreOrderFactory $preorderFactory
     * @param PreOrderInterfaceFactory $dataPreorderFactory
     * @param PreOrderCollectionFactory $preorderCollectionFactory
     * @param PreOrderSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $config
     * @param PreOrderHelperData $preorderHelperData
     * @param Products $productCollection
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param ProductRepositoryInterface $productRepository
     * @param Items $itemCollection
     * @param \Lof\PreOrder\Model\ItemFactory $item
     * @param CompleteCollectionFactory $completeCollectionFactory
     * @param \Lof\PreOrder\Model\CompleteFactory $complete
     * @param CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        ResourcePreOrder $resource,
        PreOrderFactory $preorderFactory,
        PreOrderInterfaceFactory $dataPreorderFactory,
        PreOrderCollectionFactory $preorderCollectionFactory,
        PreOrderSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $config,
        PreOrderHelperData $preorderHelperData,
        Products $productCollection,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        ProductRepositoryInterface $productRepository,
        Items $itemCollection,
        \Lof\PreOrder\Model\ItemFactory $item,
        CompleteCollectionFactory $completeCollectionFactory,
        \Lof\PreOrder\Model\CompleteFactory $completeFactory,
        CartRepositoryInterface $quoteRepository
    ) {
        $this->resource = $resource;
        $this->preorderFactory = $preorderFactory;
        $this->preorderCollectionFactory = $preorderCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataPreorderFactory = $dataPreorderFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->_config = $config;
        $this->preorderHelperData = $preorderHelperData;
        $this->_productCollection = $productCollection;
        $this->_itemCollection = $itemCollection;
        $this->productRepository = $productRepository;
        $this->quoteFactory = $quoteFactory;
        $this->_item = $item;
        $this->completeCollectionFactory = $completeCollectionFactory;
        $this->_completeFactory = $completeFactory;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsPreorder($productId)
    {
        $isPreOrder = false;
        try {
            if ($productId) {
                $isPreOrder = $this->preorderHelperData->isPreorder($productId);
            } else {
                $isPreOrder = false;
            }
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the staff: %1',
                $exception->getMessage()
            ));
        }
        return $isPreOrder;
    }

    /**
     * {@inheritdoc}
     */
    public function getPreorderNote($productId)
    {
        $isPreOrder = $this->getIsPreorder($productId);
        $preorderNote = "";
        try {
            if ($isPreOrder) {
                $payHtml = $this->preorderHelperData->getPayPreOrderHtml();
                $msg = $this->preorderHelperData->getPreOrderInfoBlock($productId);
                $preorderNote = $payHtml.'<br/>';
                $preorderNote .= $msg;
            }
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the staff: %1',
                $exception->getMessage()
            ));
        }
        return $preorderNote;
    }

    /**
     * {@inheritdoc}
     */
    public function addCustomPrice($quoteId, $quoteItemId, $productId, $storeId)
    {
        try {
            $product = $this->productRepository->getById($productId, false, $storeId);
            $quote = $this->quoteRepository->get($quoteId);
            $quoteItems = $quote->getItems();
            $quoteItem = null;
            if ($quoteItems) {
                foreach ($quoteItems as $item) {
                    if ($item->getId() == $quoteItemId) {
                        $quoteItem = $item;
                        break;
                    }
                }
            }
            $helper = $this->preorderHelperData;
            if ($quoteItem && $product) {
                $productId = $quoteItem->getProductId();
                if ($productId == $product->getId()) {
                    if ($helper->isPreorder($productId)) {
                        $cart_warning_msg = $helper->getMsgWarningQtyInCart($productId, $product->getname(), $quoteItem->getQty());
                        if ($cart_warning_msg) {
                            $quoteItem->setMessage($cart_warning_msg);
                        }
                        $quoteItem = $this->createPreorderPartialPayItem($quoteItem, $product, $productId);
                        $msg = $helper->getPreOrderInfoBlock($productId, $product);
                        if ($msg) {
                            $quoteItem->setPreorderMsg($msg);
                        }
                        $quoteItem->save();
                        return "Updated Custom Price For Quote Item ".$quoteItemId." Successfully!";
                    } else {
                        return "The Product Id ".$productId." is not Preorder Item";
                    }
                } else {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __("The request product is not exist in the cart.")
                    );
                }
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __("The product or quote item wasn't found. Verify the product/quote item and try again.")
                );
            }
        } catch (NoSuchEntityException $e) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __("The product wasn't found. Verify the product and try again."),
                $e
            );
        }
        return "";
    }

    /**
     * {@inheritdoc}
     */
    public function createCompletePreorderQuote($customerId, $productId, $storeId, $itemId, $preProductId, $orderId, $qty)
    {
        try {
            $product = $this->productRepository->getById($productId, false, $storeId);
            $quote = $this->quoteRepository->get($quoteId);
            $quoteItems = $quote->getItems();
            $quoteItem = null;
            if ($quoteItems) {
                foreach ($quoteItems as $item) {
                    if ($item->getId() == $quoteItemId) {
                        $quoteItem = $item;
                        break;
                    }
                }
            }
            $helper = $this->preorderHelperData;
            if ($quoteItem && $product) {
                $productId = $quoteItem->getProductId();
                if ($productId == $product->getId()) {
                    if ($helper->isPreorder($productId)) {
                        $preorderCompleteProductId = $helper->getPreorderCompleteProductId();
                        //create complete preorder
                        $data = ['qty' => $qty,
                                'item_id' => $itemId,
                                'pro_id' => $preProductId,
                                'order_id' => $orderId];

                        $this->createCompletePreorder($customerId, $productId, $preorderCompleteProductId, $quoteItemId, $data);
                        return "Updated Custom Price For Quote Item ".$quoteItemId." and create complete Preorder Successfully!";
                    }
                } else {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __("The request product is not exist in the cart.")
                    );
                }
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __("The product or quote item wasn't found. Verify the product/quote item and try again.")
                );
            }
        } catch (NoSuchEntityException $e) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __("The product wasn't found. Verify the product and try again."),
                $e
            );
        }
        return "";
    }

    /**
     * {@inheritdoc}
     */
    public function completeOrder($orderId)
    {
        $helper = $this->preorderHelperData;
        $order = $helper->getOrder((int)$orderId);
        if ($order && $order->getId()) {
            $orderedItems = $order->getAllItems();
            foreach ($orderedItems as $item) {
                $this->setPreorderData($item, $order);
                $this->setPreorderCompleteData($item);
            }
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(
                __("The order '%1' is not exists.", (int)$orderId)
            );
            return false;
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function notifyPreorder($itemId)
    {
        $preorder = $this->preorderFactory->create();
        $collection = $preorder->getCollection()->addFieldToFilter("item_id", (int)$itemId);
        if (!$collection->count()) {
            throw new NoSuchEntityException(__('Preorder with item id "%1" does not exist.', (int)$itemId));
        }
        $sent_count = 0;
        $message = '';
        try {
            foreach ($collection as $preorder_item) {
                $productId = $preorder_item->getProductId();
                $customerEmail = $preorder_item->getCustomerEmail();
                $stockDetails = $this->preorderHelperData->getStockDetails($productId);
                if ($stockDetails['is_in_stock'] == 1) {
                    $emailIds = [$customerEmail];
                    $this->preorderHelperData->sendNotifyEmail($emailIds, $stockDetails['name']);
                    $sent_count++;
                }
            }
            if ($sent_count > 0) {
                $message = 'Total '.$sent_count.' emails were sent succesfully.';
            } else {
                $message = 'Email was not sent.';
            }
        } catch (NoSuchEntityException $e) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __("We can not send notify preorder email at now. Please try again!"),
                $e
            );
        }
        return $message;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->preorderCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue(), false);
                    continue;
                }
                $fields[] = $filter->getField();
                $condition = $filter->getConditionType() ?: 'eq';
                $conditions[] = [$condition => $filter->getValue()];
            }
            $collection->addFieldToFilter($fields, $conditions);
        }

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
     * {@inheritdoc}
     */
    public function getSetting($path)
    {
        /* @var $store \Magento\Store\Model\Store */
        try {
            if ($path) {
                $value = $this->_config->getValue('lofpreorder/'.$path, ScopeInterface::SCOPE_STORE);
                return $value;
            }
        } catch (NoSuchEntityException $e) {

        }
        return '';
    }


    /**
     * Set Preorder Product Price
     *
     * @param object $quoteItem
     * @param object $product
     * @param int $productId
     *
     * @return object $quoteItem
     */
    public function createPreorderPartialPayItem($quoteItem, $product, $productId)
    {
        $helper = $this->preorderHelperData;
        $preorderPercent = $helper->getPreorderPercent();
        if ($helper->isPartialPreorder($productId)) {
            $id = (int) $quoteItem->getId();
            $quoteItem = ($quoteItem->getParentItem() ? $quoteItem->getParentItem() : $quoteItem);
            $price = $helper->getPreorderPrice($product, $productId);
            $quoteItem->setCustomPrice($price);
            $quoteItem->setOriginalCustomPrice($price);
            $quoteItem->getProduct()->setIsSuperMode(true);
            if ($id > 0) {
                $collection = $this->_itemCollection->create();
                $preorderItem = $helper->getDataByField($id, 'item_id', $collection);
                if ($preorderItem) {
                    $data = [
                                'item_id' => $id,
                                'preorder_percent' => $preorderPercent
                            ];
                    $this->_item->create()
                                ->addData($data)
                                ->setId($preorderItem->getId())
                                ->save();
                }
            }
        }
        return $quoteItem;
    }

    /**
     * Set Preorder Price and Data in Table
     *
     * @param object $item
     * @param object $order
     */
    public function setPreorderData($item, $order)
    {
        try {
            $helper = $this->preorderHelperData;
            $preorderType = $helper->getPreorderType();
            $time = time();
            $customerId = (int) $order->getCustomerId();
            $customerEmail = $order->getCustomerEmail();
            $remainingAmount = 0;
            $preorderPercent = '';
            $parent = ($item->getParentItem() ? $item->getParentItem() : $item);
            $parentId = $parent->getProductId();
            $productId = $item->getProductId();
            $quoteItemId = $item->getQuoteItemId();
            if ($parentId == $productId) {
                $parentId = 0;
            }
            if ($helper->isPreorder($productId)) {
                $orderItemId = $item->getId();
                $qty = $item->getQtyOrdered();
                $price = $parent->getPrice();
                if ($helper->isPartialPreorder($productId)) {
                    $collection = $this->_itemCollection->create();
                    $value = $quoteItemId;
                    $field = 'item_id';
                    $item = $helper->getDataByField($value, $field, $collection);
                    if ($item) {
                        $preorderPercent = $item->getPreorderPercent();
                        $totalPrice = ($price * 100) / $preorderPercent;
                        $remainingAmount = $totalPrice - $price;
                    }
                }
                $preorderItemData = [
                                        'order_id' => $order->getId(),
                                        'item_id' => $orderItemId,
                                        'product_id' => $productId,
                                        'parent_id' => $parentId,
                                        'customer_id' => $customerId,
                                        'customer_email' => $customerEmail,
                                        'preorder_percent' => $preorderPercent,
                                        'paid_amount' => $price,
                                        'remaining_amount' => $remainingAmount,
                                        'qty' => $qty,
                                        'type' => $preorderType,
                                        'status' => 0,
                                        'time' => $time,
                                    ];
                $this->preorderFactory->create()->setData($preorderItemData)->save();
            }
        } catch (NoSuchEntityException $e) {
            throw new NoSuchEntityException(__('Can not save preorder items.', $e));
        }
    }

    /**
     * Set Preorder Complete Price and Data in Table
     *
     * @param object $orderItem
     */
    public function setPreorderCompleteData($orderItem)
    {
        $helper = $this->preorderHelperData;
        $quoteItemId = $orderItem->getQuoteItemId();
        $productId = $orderItem->getProductId();
        $preorderCompleteProductId = $helper->getPreorderCompleteProductId();
        if ($productId == $preorderCompleteProductId) {
            $id = 0;
            $collection = $this->completeCollectionFactory->create();
            $value = $quoteItemId;
            $field = 'quote_item_id';
            $item = $helper->getDataByField($value, $field, $collection);
            if ($item) {
                $itemId = $item->getOrderItemId();
                $collection = $this->preorderCollectionFactory->create();
                $field = 'item_id';
                $item = $helper->getDataByField($itemId, $field, $collection);
                if ($item) {
                    $remainingAmount = $item->getRemainingAmount();
                    $paidAmount = $item->getPaidAmount();
                    $totalAmount = $paidAmount + $remainingAmount;
                    $item->setStatus(1)
                        ->setRemainingAmount(0)
                        ->setPaidAmount($totalAmount)
                        ->setId($item->getId())
                        ->save();
                }
            }
        }
    }

    /**
     * Create Complete PreOrder
     */
    public function createCompletePreorder($customerId, $productId, $preorderCompleteProductId, $itemId = 0, $data = [])
    {
        if ($productId == $preorderCompleteProductId) {
            $qty = 0;
            if (isset($data['qty'])) {
                $qty = (int)$data['qty'];
            }
            $qty = (int)$qty;
            $orderId = isset($data['order_id'])?$data['order_id']:0;
            $orderItemId = isset($data['item_id'])?$data['item_id']:0;
            $preorderProductId = isset($data['pro_id'])?$data['pro_id']:0;
            $completeData = [
                                'order_id' => $orderId,
                                'order_item_id' => $orderItemId,
                                'customer_id' => $customerId,
                                'product_id' => $preorderProductId,
                                'quote_item_id' => $itemId,
                                'qty' => $qty
                            ];
            $this->_completeFactory->create()->addData($completeData)->save();
        }
    }
}
