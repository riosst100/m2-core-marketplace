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
 * @package    Lofmp_SplitCart
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
declare(strict_types=1);

namespace Lofmp\SplitCart\Model;

use Lofmp\SplitCart\Api\Data\QuoteInterfaceFactory;
use Lofmp\SplitCart\Api\Data\QuoteSearchResultsInterfaceFactory;
use Lofmp\SplitCart\Api\QuoteRepositoryInterface;
use Lofmp\SplitCart\Model\ResourceModel\Quote as ResourceQuote;
use Lofmp\SplitCart\Model\ResourceModel\Quote\CollectionFactory as QuoteCollectionFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Quote\Model\QuoteFactory as MageQuoteFactory;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Quote\Api\CartItemRepositoryInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\GuestCartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\Data\CartInterfaceFactory;
use Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory as SellerCollectionFactory;
use Lof\MarketPlace\Model\Seller;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class QuoteRepository implements QuoteRepositoryInterface
{
    /**
     * @var QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var QuoteCollectionFactory
     */
    protected $quoteCollectionFactory;

    /**
     * @var ResourceQuote
     */
    protected $resource;

    /**
     * @var ExtensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;

    /**
     * @var QuoteSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var QuoteInterfaceFactory
     */
    protected $dataQuoteFactory;

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
     * @var SellerCollectionFactory
     */
    protected $sellerCollectionFactory;

    /**
     * @var MageQuoteFactory
     */
    protected $mageQuoteFactory;

    /**
     * @var CartInterfaceFactory
     */
    private $cartFactory;

    /**
     * @var QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @var CartItemRepositoryInterface
     */
    protected $cartItemRepository;

    /**
     * @var int
     */
    protected $sellerId = 0;

    /**
     * @var mixed|array
     */
    protected $currentMageQuote = [];

    /**
     * @param ResourceQuote $resource
     * @param QuoteFactory $quoteFactory
     * @param QuoteInterfaceFactory $dataQuoteFactory
     * @param QuoteCollectionFactory $quoteCollectionFactory
     * @param QuoteSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param SellerCollectionFactory $sellerCollectionFactory
     * @param MageQuoteFactory $mageQuoteFactory
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param CartRepositoryInterface $cartRepository
     * @param CartItemRepositoryInterface $cartItemRepository
     * @param CartInterfaceFactory|null $cartFactory
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ResourceQuote $resource,
        QuoteFactory $quoteFactory,
        QuoteInterfaceFactory $dataQuoteFactory,
        QuoteCollectionFactory $quoteCollectionFactory,
        QuoteSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        SellerCollectionFactory $sellerCollectionFactory,
        MageQuoteFactory $mageQuoteFactory,
        QuoteIdMaskFactory $quoteIdMaskFactory,
        CartRepositoryInterface $cartRepository,
        CartItemRepositoryInterface $cartItemRepository,
        CartInterfaceFactory $cartFactory = null
    ) {
        $this->resource = $resource;
        $this->quoteFactory = $quoteFactory;
        $this->quoteCollectionFactory = $quoteCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataQuoteFactory = $dataQuoteFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->sellerCollectionFactory = $sellerCollectionFactory;
        $this->mageQuoteFactory = $mageQuoteFactory;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->cartRepository = $cartRepository;
        $this->cartItemRepository = $cartItemRepository;
        $this->cartFactory = $cartFactory ?: ObjectManager::getInstance()->get(CartInterfaceFactory::class);
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Lofmp\SplitCart\Api\Data\QuoteInterface $quote
    ) {
        /* if (empty($quote->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $quote->setStoreId($storeId);
        } */

        $quoteData = $this->extensibleDataObjectConverter->toNestedArray(
            $quote,
            [],
            \Lofmp\SplitCart\Api\Data\QuoteInterface::class
        );

        $quoteModel = $this->quoteFactory->create()->setData($quoteData);

        try {
            $this->resource->save($quoteModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the quote: %1',
                $exception->getMessage()
            ));
        }
        return $quoteModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($entityId)
    {
        $quote = $this->quoteFactory->create();
        $this->resource->load($quote, $entityId);
        if (!$quote->getId()) {
            throw new NoSuchEntityException(__('Entity with id "%1" does not exist.', $entityId));
        }
        return $quote->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->quoteCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Lofmp\SplitCart\Api\Data\QuoteInterface::class
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
    public function getSplitCart($cartId)
    {
        $quote = $this->quoteCollectionFactory->create()
            ->addFieldToFilter('parent_id', $cartId)
            ->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('is_ordered', 0)
            ->getFirstItem();

        if (!$quote->getId()) {
            throw new NoSuchEntityException(__('No split cart found!'));
        }
        return $quote->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getSplitCartForGuest($cartId, $sellerUrl)
    {
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        if ($quoteIdMask->getQuoteId()) {
            return $this->getSplitCartForCustomer($quoteIdMask->getQuoteId(), $sellerUrl);
        } else {
            return $this->dataQuoteFactory->create();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getSplitCartForCustomer($cartId, $sellerUrl)
    {
        $seller = $this->getSellerByUrl($sellerUrl);
        if ($seller && $seller->getId()) {
            $quote = $this->getSplitCartData($cartId, $seller->getId());

            if (!$quote->getId()) {
                return $this->dataQuoteFactory->create();
            }
            return $quote->getDataModel();
        } else {
            return $this->dataQuoteFactory->create();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Lofmp\SplitCart\Api\Data\QuoteInterface $quote
    ) {
        try {
            $quoteModel = $this->quoteFactory->create();
            $this->resource->load($quoteModel, $quote->getQuoteId());
            $this->resource->delete($quoteModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Quote: %1',
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
        return $this->delete($this->get($entityId));
    }

    /**
     * @inheritdoc
     */
    public function initSplitOrderGuest(
        $cartId,
        $sellerUrl
    ) {
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        if ($quoteIdMask->getQuoteId()) {
            return $this->initSplitOrder($quoteIdMask->getQuoteId(), $sellerUrl);
        } else {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function initSplitOrder(
        $cartId,
        $sellerUrl
    ) {
        $sellerId = $this->getSellerId();
        if (!$sellerId) {
            $seller = $this->getSellerByUrl($sellerUrl);
            $sellerId = $seller && $seller->getId() ? $seller->getId() : 0;
        }
        if (!$sellerId) {
            throw new NoSuchEntityException(
                __('Seller with URL %1 is not exists'),
                $sellerUrl
            );
        }
        if (!$this->isValidSellerId($cartId, $sellerId)) {
            throw new NoSuchEntityException(
                __('Seller with URL %1 is not available in current cart.'),
                $sellerUrl
            );
        }
        $splitQuoteCollection = $this->quoteCollectionFactory->create()
                                ->addFieldToFilter('parent_id', $cartId)
                                ->addFieldToFilter('is_ordered', 0);

        if ($splitQuoteCollection && $splitQuoteCollection->getSize() > 0) {
            foreach ($splitQuoteCollection as $splitQuoteItem) {
                $this->updateSplitCartQuote($splitQuoteItem->getId(), $splitQuoteItem->getQuoteId(), 0 );
            }

            $splitQuote = $this->quoteCollectionFactory->create()
                            ->addFieldToFilter('parent_id', $cartId)
                            ->addFieldToFilter('is_ordered', 0)
                            ->addFieldToFilter('seller_id', $sellerId)
                            ->getFirstItem();

            if ($splitQuote && $splitQuote->getData()) {
                $this->updateSplitCartQuote($splitQuote->getId(), $splitQuote->getQuoteId(), 1 );
            } else {
                $this->createNewQuote($cartId, $sellerId);
            }
        } else {
            $this->createNewQuote($cartId, $sellerId);
        }

        $quote = $this->getSplitCartData($cartId, $sellerId);
        return $quote->getDataModel();
    }

    /**
     * @inheritdoc
     */
    public function removeSplitCartGuest($cartId)
    {
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        if ($quoteIdMask->getQuoteId()) {
            return $this->removeSplitCart($quoteIdMask->getQuoteId());
        } else {
            return false;
        }
    }
    /**
     * @inheritdoc
     */
    public function removeSplitCart($cartId)
    {
        $flag = false;
        $parentQuote = $this->getQuote($cartId);
        $parentQuoteId = $parentQuote->getId();
        if (!$parentQuote || !$parentQuote->hasItems()) {
            return $flag;
        }
        $splitQuoteCollection = $this->quoteCollectionFactory->create()
                ->addFieldToFilter('parent_id', $parentQuoteId)
                ->addFieldToFilter('is_ordered', 0);
        if (!$splitQuoteCollection || $splitQuoteCollection->getSize() == 0) {
            $foundSplitQuote = $this->quoteCollectionFactory->create()
                ->addFieldToFilter('quote_id', $parentQuoteId)
                ->addFieldToFilter('is_ordered', 0)
                ->getFirstItem();
            $parentQuoteId = $foundSplitQuote && $foundSplitQuote->getParentId() ? $foundSplitQuote->getParentId() : 0;
            if ($parentQuoteId) {
                $splitQuoteCollection = $this->quoteCollectionFactory->create()
                    ->addFieldToFilter('parent_id', $parentQuoteId)
                    ->addFieldToFilter('is_ordered', 0);
            }
        }

        if (!$splitQuoteCollection || $splitQuoteCollection->getSize() == 0) {
            return $flag;
        }

        foreach ($splitQuoteCollection as $splitQuote) {
            $this->mageQuoteFactory->create()
                    ->load($splitQuote->getQuoteId())
                    ->delete();
        }
        $flag = true;
        return $flag;
    }

    /**
     * @inheritdoc
     */
    public function updateSplitCart($cartId)
    {
        $flag = false;

        $splitQuoteCollection = $this->quoteCollectionFactory->create()
                ->addFieldToFilter('quote_id', $cartId)
                ->addFieldToFilter('is_ordered', 0)
                ->addFieldToFilter('is_active', 1);

        if ($splitQuoteCollection && $splitQuoteCollection->getSize() > 0) {
            foreach ($splitQuoteCollection as $splitQuote) {
                $tmpSplitQuoteModel = $this->quoteFactory->create();
                $this->resource->load($tmpSplitQuoteModel, $splitQuote->getId());
                $tmpSplitQuoteModel->setIsActive(0)
                                ->setIsOrdered(1);
                $this->resource->save($tmpSplitQuoteModel);
            }
        }

        $splitQuoteModel = $this->mageQuoteFactory->create()->load($cartId);
        $orderedItems = $splitQuoteModel->getAllItems();
        $parentQuoteId = $splitQuoteModel->getParentId();
        if ($orderedItems && $parentQuoteId) {
            foreach ($orderedItems as $item) {
                if ($item->getParentId()) {
                    $this->cartItemRepository->deleteById($cartId, $item->getParentId());
                }
            }
            $flag = true;
        }
        return $flag;
    }

    /**
     * @inheritdoc
     */
    public function getCartForCustomer($customerId, $sellerUrl)
    {
        $seller = $this->getSellerByUrl($sellerUrl);
        $sellerId = $seller && $seller->getId() ? $seller->getId() : 0;
        if (!$sellerId) {
            throw new NoSuchEntityException(
                __('Seller with URL %1 is not exists'),
                $sellerUrl
            );
        }
        /** 1. Get Parent Cart */
        $quote = $this->cartRepository->getActiveForCustomer($customerId);
        if (!$quote->getIsActive()) {
            throw NoSuchEntityException::singleField('customerId', $customerId);
        }
        /** 2. Get split cart data */
        $splitQuote = $this->getSplitCartData($quote->getId(), $sellerId);
        if (!$splitQuote->getId()) {
            throw new NoSuchEntityException(__('No split cart found for the seller Url %1!', $sellerUrl));
        }
        /** 3. Get Child Cart of seller */
        $newQuote = $this->getQuote($splitQuote->getQuoteId());
        return $newQuote;
    }

    /**
     * @inheritdoc
     */
    public function getCartForGuest($cartId, $sellerUrl)
    {
        $seller = $this->getSellerByUrl($sellerUrl);
        $sellerId = $seller && $seller->getId() ? $seller->getId() : 0;
        if (!$sellerId) {
            throw new NoSuchEntityException(
                __('Seller with URL %1 is not exists'),
                $sellerUrl
            );
        }
        /** 1. Get Parent Cart */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        if (!$quoteIdMask->getQuoteId()) {
            throw NoSuchEntityException::singleField('cartId', $cartId);
        }
        /** 2. Get split cart data */
        $splitQuote = $this->getSplitCartData($quoteIdMask->getQuoteId(), $sellerId);
        if (!$splitQuote->getId()) {
            throw new NoSuchEntityException(__('No split cart found for the seller Url %1!', $sellerUrl));
        }
        /** 3. Get Child Cart of seller */
        $newQuote = $this->getQuote($splitQuote->getQuoteId());
        return $newQuote;
    }

    /**
     * update split cart, magento quote status
     *
     * @param int $entityId
     * @param int $quoteId
     * @param int $status
     * @return void
     */
    protected function updateSplitCartQuote($entityId, $quoteId, $status)
    {
        /**update split quote, set is active = 0 */
        $quote = $this->quoteFactory->create();
        $this->resource->load($quote, $entityId);
        $quote->setIsActive($status);
        $this->resource->save($quote);
        /**update magento quote, set is active = 0 */
        $this->mageQuoteFactory->create()
                ->load($quoteId)
                ->setIsActive($status)
                ->save();
    }

    /**
     * get seller by sellerUrl
     *
     * @param string $sellerUrl
     * @return \Lof\MarketPlace\Model\Seller
     */
    protected function getSellerByUrl($sellerUrl)
    {
        $seller = $this->sellerCollectionFactory->create()
            ->addFieldToFilter('url_key', ['eq' => $sellerUrl])
            ->addFieldToFilter("status", Seller::STATUS_ENABLED)
            ->getFirstItem();
        return $seller;
    }

    /**
     * get seller by customer id
     *
     * @param int $customerId
     * @return \Lof\MarketPlace\Model\Seller
     */
    protected function getSellerByCustomer($customerId)
    {
        $seller = $this->sellerCollectionFactory->create()
            ->addFieldToFilter('customer_id', $customerId)
            ->addFieldToFilter("status", Seller::STATUS_ENABLED)
            ->getFirstItem();
        return $seller;
    }

    /**
     * @param int $cartId
     * @param int $sellerId
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function isValidSellerId($cartId, $sellerId)
    {
        /** @var CartInterface $quote */
        $quote = $this->getQuote($cartId);
        if (!$quote || !$quote->hasItems()) {
            return false;
        }

        $flag = false;
        $items = $quote->getItems();

        if ($items) {
            foreach ($items as $item) {
                $sellerIdItem = $item->getLofSellerId() ? $item->getLofSellerId() : $item->getSellerId();
                if ($sellerIdItem == $sellerId) {
                    $flag = true;
                    break;
                }
            }
        }
        return $flag;
    }

    /**
     * @param int $cartId
     * @param int $sellerId
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function createNewQuote($cartId, $sellerId)
    {
        $orgQuote = $this->getQuote($cartId);
        $quote = $this->mageQuoteFactory->create();
        $quote->setStore($orgQuote->getStore());
        $quote->setCurrency();
        $quote->assignCustomer($orgQuote->getCustomer());

        //add items in quote
        foreach ($orgQuote->getAllItems() as $item) {
            $sellerIdItem = $item->getLofSellerId() ? $item->getLofSellerId() : $item->getProduct()->getSellerId();
            /** @var \Magento\Quote\Model\Quote\Item $item */
            if ($sellerIdItem == $sellerId) {
                $parentId = $item->getId();
                $item->setId(null);

                $options = $item->getOptions();
                $optionsResult = [];
                foreach ($options as $option) {
                    /** @var \Magento\Quote\Model\Quote\Item\Option $option */
                    $option->setId(null);
                    $optionsResult[] = $option;
                }
                $item->setOptions($optionsResult);
                $item->setParentId($parentId);
                $quote->addItem($item);
            }
        }
        $quote->setBillingAddress($orgQuote->getBillingAddress());
        $quote->setShippingAddress($orgQuote->getShippingAddress());

        // Collect Rates and Set Shipping & Payment Method
        $quote->setPaymentMethod($orgQuote->getPaymentMethod());
        $quote->setInventoryProcessed(false);
        $quote->setParentId($orgQuote->getId());
        $quote->save();

        $splitQuote = $this->quoteFactory->create();
        $splitQuote->setParentId($orgQuote->getId())
            ->setQuoteId($quote->getId())
            ->setSellerId($sellerId)
            ->setIsActive(1)
            ->setIsOrdered(0)
            ->save();

        // Collect Totals & Save Quote
        $quote->collectTotals()->save();

        if (!$quote->getCustomerId()) {
            /** @var \Magento\Quote\Model\QuoteIdMask $quoteIdMask */
            $quoteIdMask = $this->quoteIdMaskFactory->create();
            $quoteIdMask->setQuoteId($quote->getId())->save();
        }
        return $quote->getId();
    }

    /**
     * get Magento Quote
     *
     * @param int $cartId
     * @param mixed|array $sharedStoreIds
     * @return CartInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getQuote($cartId, array $sharedStoreIds = [])
    {
        if (!isset($this->currentMageQuote[$cartId])) {
            /** @var CartInterface $quote */
            $quote = $this->cartRepository->getActive($cartId, $sharedStoreIds);
            $this->currentMageQuote[$cartId] = $quote;
        }
        return $this->currentMageQuote[$cartId];
    }

    /**
     * get split quote data
     *
     * @param int $cartId
     * @param int $sellerId
     * @return \Lofmp\SplitCart\Model\Quote
     */
    protected function getSplitCartData($cartId, $sellerId)
    {
        $quote = $this->quoteCollectionFactory->create()
                ->addFieldToFilter('parent_id', $cartId)
                ->addFieldToFilter('seller_id', $sellerId)
                ->addFieldToFilter('is_active', 1)
                ->addFieldToFilter('is_ordered', 0)
                ->getFirstItem();
        return $quote;
    }

    /**
     * set seller id
     *
     * @param int $sellerId
     * @return $this
     */
    public function setSellerId($sellerId)
    {
        $this->sellerId = $sellerId;
        return $this;
    }

    /**
     * get seller id
     *
     * @return int
     */
    public function getSellerId()
    {
        return $this->sellerId;
    }
}
