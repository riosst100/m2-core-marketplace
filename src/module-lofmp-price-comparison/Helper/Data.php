<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_PriceComparison
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\PriceComparison\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\UrlInterface;
use Lof\MarketPlace\Model\ResourceModel\SellerProduct\CollectionFactory;
use Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory as SellerCollection;
use Lofmp\PriceComparison\Model\ResourceModel\Product\CollectionFactory as ItemsCollection;
use Lofmp\PriceComparison\Model\ResourceModel\Quote\CollectionFactory as QuoteCollection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollection;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    protected $_formKey;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $_currency;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $_fileUploader;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_product;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $_cart;

    /**
     * @var \Lof\MarketPlace\Model\ProductFactory
     */
    protected $_mpProduct;

    /**
     * @var \Lofmp\PriceComparison\Model\ProductFactory
     */
    protected $_items;

    /**
     * @var CollectionFactory
     */
    protected $_mpProductCollection;

    /**
     * @var SellerCollection
     */
    protected $_sellerCollection;

    /**
     * @var ItemsCollection
     */
    protected $_itemsCollection;

    /**
     * @var QuoteCollection
     */
    protected $_quoteCollection;

    /**
     * @var ProductCollection
     */
    protected $_productCollection;
     /**
      * @var Review
      */
    protected $review;
     /**
      * @var Rating
      */
    protected $rating;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Data\Form\FormKey $formKey
     * @param \Magento\Framework\Pricing\Helper\Data $currency
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Lofmp\MarketPlace\Model\ProductFactory $mpProductFactory
     * @param \Lofmp\PriceComparison\Model\ProductFactory $itemsFactory
     * @param CollectionFactory $mpProductCollectionFactory
     * @param SellerCollection $sellerCollectionFactory
     * @param ItemsCollection $itemsCollectionFactory
     * @param QuoteCollection $quoteCollectionFactory
     * @param ProductCollection $productCollectionFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Framework\Pricing\Helper\Data $currency,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Checkout\Model\Cart $cart,
        \Lof\MarketPlace\Model\SellerProductFactory $mpProductFactory,
        \Lof\MarketPlace\Model\Review $review,
        \Lof\MarketPlace\Model\Rating $rating,
        \Lofmp\PriceComparison\Model\ProductFactory $itemsFactory,
        CollectionFactory $mpProductCollectionFactory,
        SellerCollection $sellerCollectionFactory,
        ItemsCollection $itemsCollectionFactory,
        QuoteCollection $quoteCollectionFactory,
        ProductCollection $productCollectionFactory
    ) {
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_request = $context->getRequest();
        $this->review = $review;
        $this->rating = $rating;
        $this->_storeManager = $storeManager;
        $this->_messageManager = $messageManager;
        $this->_customerSession = $customerSession;
        $this->_filesystem = $filesystem;
        $this->_formKey = $formKey;
        $this->_currency = $currency;
        $this->_resource = $resource;
        $this->_fileUploader = $fileUploaderFactory;
        $this->_product = $productFactory;
        $this->_cart = $cart;
        $this->_mpProduct = $mpProductFactory;
        $this->_items = $itemsFactory;
        $this->_mpProductCollection = $mpProductCollectionFactory;
        $this->_sellerCollection = $sellerCollectionFactory;
        $this->_itemsCollection = $itemsCollectionFactory;
        $this->_quoteCollection = $quoteCollectionFactory;
        $this->_productCollection = $productCollectionFactory;
        $this->_moduleManager = $context->getModuleManager();
        parent::__construct($context);
    }

    /**
     * @param $module
     * @return bool
     */
    public function isEnableModule($module)
    {
        return $this->_moduleManager->isEnabled($module);
    }
     /**
      * Return brand config value by key and store
      *
      * @param string $key
      * @param \Magento\Store\Model\Store|int|string $store
      * @return string|null
      */
    public function getConfig($key, $store = null)
    {
        $store = $this->_storeManager->getStore($store);
        $websiteId = $store->getWebsiteId();
        $result = $this->scopeConfig->getValue(
            'lofmppricecomparison/'.$key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        return $result;
    }

    /**
     * Get module enabled/disabled
     *
     * @return bool|int
     */
    public function isEnabled()
    {
        return (int) $this->getConfig("general_settings/enable_pricecomparison");
    }
     /**
      * Get getReviewTotal
      *
      * @return bool
      */
    public function getReviewTotal($seller_id)
    {
        return $this->review->getCollection()->addFieldToFilter('seller_id', $seller_id);
    }
    /**
     * Get getRating
     *
     * @return bool
     */
    public function getRating($seller_id)
    {
        return $this->rating->getCollection()->addFieldToFilter('seller_id', $seller_id);
    }
    /**
     * Get Show Lower Price Setting Config
     *
     * @return bool
     */
    public function showMinimumPrice()
    {
        $config = 'general_settings/minimun';
        $showMinimum = $this->getConfig($config);
        if ($showMinimum == '') {
            return false;
        }
        return $showMinimum;
    }

    /**
     * Get Assign Type Setting Config
     *
     * @return bool
     */
    public function getAssignType()
    {
        $config = 'general_settings/assign';
        return $this->getConfig($config);
    }

    /**
     * Get Add Approve Product Setting Config
     *
     * @return bool
     */
    public function isAddApprovalRequired()
    {
        $config = 'general_settings/add_product';
        return $this->getConfig($config);
    }
      /**
       * Get Add Approve Product Setting Config
       *
       * @return bool
       */
    public function isEnablePriceComparison()
    {
        $config = 'general_settings/enable_pricecomparison';
        return $this->getConfig($config);
    }
    /**
     * Get Edit Approve Product Setting Config
     *
     * @return bool
     */
    public function isEditApprovalRequired()
    {
        $config = 'general_settings/edit_product';
        return $this->getConfig($config);
    }

    /**
     * Get Current Customer Id
     *
     * @return int
     */
    public function getCustomerId()
    {
        $customerId = 0;
        if ($this->_customerSession->isLoggedIn()) {
            $customerId = (int) $this->_customerSession->getCustomerId();
        }
        return $customerId;
    }

    /**
     * Check Customer is Logged In or Not
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        if ($this->_customerSession->isLoggedIn()) {
            return true;
        }
        return false;
    }

    /**
     * Get Mediad Path
     *
     * @return string
     */
    public function getMediaPath()
    {
        return $this->_filesystem
                    ->getDirectoryRead(DirectoryList::MEDIA)
                    ->getAbsolutePath();
    }

    /**
     * Get Form Key
     *
     * @return string
     */
    public function getFormKey()
    {
        return $this->_formKey->getFormKey();
    }
    /**
     * Get Assign Product by AssignId
     *
     * @param int $assignId
     *
     * @return object
     */
    public function getAssignProduct($assignId)
    {
        $assignProduct = $this->_items->create()->load($assignId);
        return $assignProduct;
    }

    /**
     * Get Assign Product Collection
     *
     * @return collection object
     */
    public function getCollection()
    {
        $collection = $this->_itemsCollection->create();
        return $collection;
    }

    /**
     * Get Assign Product Quote Items Collection
     *
     * @return collection object
     */
    public function getQuoteCollection()
    {
        $collection = $this->_quoteCollection->create();
        return $collection;
    }

    /**
     * Get Product Collection
     *
     * @return collection object
     */
    public function getProductCollection()
    {
        $collection = $this->_productCollection->create();
        return $collection;
    }

    /**
     * Get Marketplace Product Collection
     *
     * @return collection object
     */
    public function getMpProductCollection()
    {
        $collection = $this->_mpProductCollection->create();
        return $collection;
    }

    /**
     * Get Cart
     *
     * @return object
     */
    public function getCart()
    {
        $cartModel = $this->_cart;
        return $cartModel;
    }

    /**
     * Get Current Product Id
     *
     * @return int
     */
    public function getProductId()
    {
        $id = (int) $this->_request->getParam('id');
        return $id;
    }

    /**
     * Get Product
     *
     * @param int $productId [optional]
     *
     * @return object
     */
    public function getProduct($productId = 0)
    {
        if (!$productId) {
            $productId = $this->getProductId();
        }
        $product = $this->_product->create()->load($productId);
        return $product;
    }

    /**
     * Get Searched Query String
     *
     * @return string
     */
    public function getQueryString()
    {
        $queryString = $this->_request->getParam('key_search');
        $queryString = trim($queryString);
        return $queryString;
    }

    /**
     * Check Whether Product Is Valid Or Not.
     *
     * @param int $isAdd [optional]
     *
     * @return bool
     */
    public function checkProduct($isAdd = 0)
    {
        $result = [];
        $result = ['msg' => '', 'error' => 0];
        $assignId = (int) $this->_request->getParam('id');
        if ($assignId == 0) {
            $result['error'] = 1;
            $result['msg'] = 'Invalid request.';
            return $result;
        }
        if ($isAdd == 1) {
            $productId = (int)$assignId;
        } else {
            $assignData = $this->getAssignDataByAssignId($assignId);
            $productId = $assignData->getProductId();
        }
        $product = $this->getProduct($productId);
        if ($product->getId() <= 0) {
            $result['error'] = 1;
            $result['msg'] = 'Product does not exist.';
            return $result;
        }
        $productType = $product->getTypeId();
        $allowedProductTypes = ['simple', 'virtual'];
        if (!in_array($productType, $allowedProductTypes)) {
            $result['error'] = 1;
            $result['msg'] = 'Product type not allowed.';
            return $result;
        }
        $sellerId = $this->getSellerIdByProductId($productId);

        if ($sellerId == 0) {
            $result['error'] = 1;
            $result['msg'] = 'Product is not assigned to seller.';
            return $result;
        }
        $customerId = $this->getCustomerId();
        if ($sellerId == $customerId) {
            $result['error'] = 1;
            $result['msg'] = 'Product is your own product.';
            return $result;
        }
        if ($isAdd == 1) {
            $assignIdCheck = $this->getAssignId($productId, $customerId);
            if ($assignIdCheck > 0) {
                $result['error'] = 1;
                $result['msg'] = 'Already assigned to you.';
                return $result;
            }
        }
        return $result;
    }

    /**
     * Return Assign Id by Product Id
     *
     * @param int $productId
     * @param int $sellerId
     *
     * @return int
     */
    public function getAssignId($productId, $sellerId)
    {
        $assignId = 0;
        $collection = $this->getCollection()
                            ->addFieldToFilter('product_id', $productId)
                            ->addFieldToFilter('seller_id', $sellerId);
        foreach ($collection as $item) {
            $assignId = $item->getId();
        }
        return $assignId;
    }

    /**
     * Return Seller Id by Product Id
     *
     * @param int $productId
     *
     * @return int
     */
    public function getSellerIdByProductId($productId)
    {
        $sellerId = 0;
        $collection = $this->getMpProductCollection()
                        ->addFieldToFilter('product_id', $productId);
        foreach ($collection as $item) {
            $sellerId = $item->getSellerId();
        }
        return $sellerId;
    }

    /**
     * Return Seller Id by Assign Id
     *
     * @param int $assignId
     *
     * @return int
     */
    public function getAssignSellerIdByAssignId($assignId)
    {
        $sellerId = 0;
        $assignProduct = $this->getAssignProduct($assignId);
        if ($assignProduct->getId() > 0) {
            $sellerId = $assignProduct->getSellerId();
        }
        return $sellerId;
    }

    /**
     * Get Product by Assign Id
     *
     * @param int $assignId
     *
     * @return object
     */
    public function getProductByAssignId($assignId)
    {
        $assignData = $this->getAssignDataByAssignId($assignId);
        $product = $this->getProduct($assignData->getProductId());
        return $product;
    }

    /**
     * Get Assign Data by Assign Id
     *
     * @param int $assignId
     *
     * @return object
     */
    public function getAssignDataByAssignId($assignId)
    {
        $assignProduct = $this->getAssignProduct($assignId);
        return $assignProduct;
    }

    /**
     * Check Whether Assign Product is Valid or Not
     *
     * @param int $assignId
     *
     * @return bool
     */
    public function isValidAssignProduct($assignId)
    {
        $customerId = $this->getCustomerId();
        $collection = $this->getCollection()
                            ->addFieldToFilter('id', $assignId)
                            ->addFieldToFilter('seller_id', $customerId);
        foreach ($collection as $item) {
            if ($item->getId() > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Update Stock Data of Product
     *
     * @param int $productId
     * @param int $qty
     * @param int $flag [optional]
     * @param int $oldQty [optional]
     */
    public function updateStockData($productId, $qty, $flag = 0, $oldQty = 0)
    {
        $product = $this->getProduct($productId);
        $collection = $this->_productCollection->create();
        $alias = 'qty';
        $table = 'cataloginventory_stock_item';
        $field = 'qty';
        $bind = 'product_id = entity_id';
        $cond = '{{table}}.stock_id = 1';
        $joinType = 'left';
        $collection->joinField($alias, $table, $field, $bind, $cond, $joinType);
        $collection->addFieldToFilter('entity_id', $productId);
        foreach ($collection as $item) {
            if ($flag == 1) {
                $qty = $qty + $item->getQty() - $oldQty;
            } elseif ($flag == 2) {
                $qty = $item->getQty() - $qty;
            } elseif ($flag == 3) {
                $qty = $qty;
            } else {
                $qty = $qty + $item->getQty();
            }
        }
        $stockData = [];
        $stockData['quantity_and_stock_status'] = ['qty' => $qty];
        $product->addData($stockData);
        $product->setId($productId)->save();
    }

    /**
     * Update Price of Product
     *
     * @param int $productId
     * @param float $price
     */
    public function updatePrice($productId, $price)
    {
        $product = $this->getProduct($productId);
        $product->addData(['price' => $price]);
        $product->setId($productId)->save();
    }

    /**
     * Get Original Quantity of Product
     *
     * @param int $productId
     *
     * @return int
     */
    public function getOriginalQty($productId)
    {
        $totalQty = 0;
        $collection = $this->_productCollection->create();
        $alias = 'qty';
        $table = 'cataloginventory_stock_item';
        $field = 'qty';
        $bind = 'product_id = entity_id';
        $cond = '{{table}}.stock_id = 1';
        $joinType = 'left';
        $collection->joinField($alias, $table, $field, $bind, $cond, $joinType);
        $collection->addFieldToFilter('entity_id', $productId);
        foreach ($collection as $product) {
            $totalQty = $product->getQty();
        }
        $assignProducts = $this->getAllAssignedProducts($productId);
        foreach ($assignProducts as $assignProduct) {
            $totalQty -= $assignProduct['qty'];
        }
        return $totalQty;
    }

    /**
     * Update Stock Data of Product by Assign Id
     *
     * @param int $assignId
     */
    public function updateStockDataByAssignId($assignId)
    {
        $assignData = $this->getAssignDataByAssignId($assignId);
        $productId = $assignData->getProductId();
        $qty = $assignData->getQty();
        $this->updateStockData($productId, $qty, 2);
    }

    /**
     * Update Assign Product Quote by Assign Id
     *
     * @param int $assignId
     */
    public function updateQuote($assignId)
    {
        $itemIds = [];
        $collection = $this->getQuoteCollection()
                            ->addFieldToFilter('assign_id', $assignId);
        foreach ($collection as $item) {
            $itemIds[] = $item->getItemId();
            $item->delete();
        }
        $this->updateCart($itemIds);
    }

    /**
     * Update Cart
     *
     * @param int|array $itemIds
     */
    public function updateCart($itemIds)
    {
        $cartModel = $this->getCart();
        $quote = $cartModel->getQuote();
        foreach ($quote->getAllVisibleItems() as $item) {
            $id = $item->getId();
            if (in_array($id, $itemIds)) {
                $cartModel->removeItem($id)->save();
            }
        }
        $cartModel->save();
    }

    /**
     * Check Product Quantities are Available from Seller on Cart
     */
    public function checkStatus()
    {
        $cartModel = $this->getCart();
        $quote = $cartModel->getQuote();
        $preOrderModuleEnabled = $this->isEnableModule('Lof_PreOrder');
        foreach ($quote->getAllVisibleItems() as $item) {
            $productId = $item->getProductId();
            $product = $this->getProduct($productId);
            $productType = $product->getTypeId();
            $allowedProductTypes = ['simple', 'virtual'];
            if (in_array($productType, $allowedProductTypes)) {
                $itemId = $item->getId();
                $requestedQty = $item->getQty();
                $assignData = $this->getAssignDataByItemId($itemId);
                if ($assignData['assign_id'] > 0) {
                    $assignId = $assignData['assign_id'];
                    $assignData = $this->getAssignDataByAssignId($assignId);
                    $qty = $assignData->getQty();
                } else {
                    $qty = $this->getOriginalQty($productId);
                }
                if (!$preOrderModuleEnabled) {
                    if ($requestedQty > $qty) {
                        $item->setQty($qty);
                        $this->_messageManager
                            ->addError('Quantities are not available from seller.');
                    }
                    if ($qty <= 0) {
                        $cartModel->removeItem($itemId)->save();
                    }
                }
            }
        }
        $cartModel->save();
    }

    /**
     * Set Updated Price of Product
     */
    public function checkCartPrice()
    {
        $cartModel = $this->getCart();
        $quote = $cartModel->getQuote();
        foreach ($quote->getAllVisibleItems() as $item) {
            $itemId = $item->getId();
            $productId = $item->getProductId();
            $requestedQty = $item->getQty();
            $assignData = $this->getAssignDataByItemId($itemId);
            if ($assignData['assign_id'] > 0) {
                $assignId = $assignData['assign_id'];
                $price = $this->getAssignProductPrice($assignId);
                $item->setCustomPrice($price);
                $item->setOriginalCustomPrice($price);
                $item->setRowTotal($item->getQty()*$price);
                $item->getProduct()->setIsSuperMode(true);
            }
        }
        $cartModel->getQuote()->collectTotals()->save();
        /*TODO for Price Updation*/
        /*$cartModel->save();*/
    }

    /**
     * Get Seller Details by Seller Id
     *
     * @param int $sellerId
     *
     * @return object
     */
    public function getSellerDetails($sellerId)
    {
        $seller = "";
        $collection = $this->_sellerCollection
                            ->create()
                            ->addFieldToFilter('seller_id', ['eq' => $sellerId]);
        foreach ($collection as $seller) {
            return $seller;
        }
        return $seller;
    }

    /**
     * Assign Product to Seller
     *
     * @param array $data
     * @param int $flag [optional]
     *
     * @return array
     */
    public function assignProduct($data, $flag = 0)
    {
        $result = [
                    'assign_id' => 0,
                    'product_id' => 0,
                    'error' => 0,
                    'msg' => '',
                    'qty' => 0,
                    'flag' => 0,
                    'status' => 1,
                ];
        $productId = (int) $data['product_id'];
        $condition = (int) $data['product_condition'];
        $qty = (int) $data['qty'];
        $price = (float) $data['price'];
        $description = $data['description'];
        if (isset($data['image'])) {
            $image = $data['image'];
        } else {
            $image = '';
        }
        $ownerId = $this->getSellerIdByProductId($productId);
        $sellerId = $this->getSellerId();
        $product = $this->getProduct($productId);
        $type = $product->getTypeId();
        $date = date('Y-m-d');
        if ($qty < 0) {
            $qty = 0;
        }
        $assignProductData = [
                                'product_id' => $productId,
                                'owner_id' => $ownerId,
                                'seller_id' => $sellerId,
                                'qty' => $qty,
                                'price' => $price,
                                'description' => $description,
                                'condition' => $condition,
                                'type' => $type,
                                'created_at' => $date,
                                'image' => $image,
                                'status' => 1,
                            ];
        if ($image == '') {
            unset($assignProductData['image']);
        }
        /*if ($data['del'] == 1) {
            unset($assignProductData['image']);
        }*/
        $model = $this->_items->create();
        if ($flag == 1) {
            $assignId = $data['assign_id'];
            $assignData = $this->getAssignDataByAssignId($assignId);
            if ($assignData->getId() > 0) {
                $oldQty = $assignData->getQty();
                $status = $assignData->getStatus();
                $result['old_qty'] = $oldQty;
                $result['prev_status'] = $status;
                $result['flag'] = 1;
                unset($assignProductData['created_at']);
                if ($this->isEditApprovalRequired()) {
                    $result['status'] = 0;
                    $assignProductData['status'] = 0;
                }
            } else {
                return $result;
            }
            $model->addData($assignProductData)->setId($assignId)->save();
        } else {
            if ($this->isAddApprovalRequired()) {
                $result['status'] = 0;
                $assignProductData['status'] = 0;
            }
            $model->setData($assignProductData)->save();
        }
        if ($model->getId() > 0) {
            $result['product_id'] = $productId;
            $result['qty'] = $qty;
            $result['assign_id'] = $model->getId();
        }
        return $result;
    }

    /**
     * Dissapprove Assign Product
     *
     * @param int $assignId
     * @param int $status [optional]
     * @param int $flag [optional]
     * @param int $qty [optional]
     */
    public function disApproveProduct($assignId, $status = 0, $flag = 0, $qty = 0)
    {
        $assignProduct = $this->getAssignProduct($assignId);
        if ($assignProduct->getId() > 0) {
            if ($status == 1) {
                $productId = $assignProduct->getProductId();
                $assignProduct->setData(['status' => 0])->setId($assignId)->save();
                if ($flag == 1) {
                    $qty = $assignProduct->getQty();
                }
                $this->updateStockData($productId, $qty, 2);
            }
        }
    }

    /**
     * Approve Assign Product
     *
     * @param int $assignId
     */
    public function approveProduct($assignId)
    {
        $assignProduct = $this->getAssignProduct($assignId);
        if ($assignProduct->getId() > 0) {
            $status = $assignProduct->getStatus();
            if ($status == 0) {
                $productId = $assignProduct->getProductId();
                $qty = $assignProduct->getQty();
                $assignProduct->setData(['status' => 1])->setId($assignId)->save();
                $this->updateStockData($productId, $qty);
            }
        }
    }

    /**
     * Get Assign Products
     *
     * @param int $productId
     * @param string $sort [optional]
     * @param string $order [optional]
     *
     * @return collection object
     */
    public function getAssignProducts($productId, $sort = '', $order = 'ASC')
    {
        $collection = $this->getCollection()->addFieldToFilter('product_id', $productId)->addFieldToFilter('qty', ['gt'=>0])->addFieldToFilter('status', ['eq'=>1]);
        if ($sort != '') {
            $collection->setOrder($sort, $order);
        }
        return $collection;
    }

    /**
     * Get All Product Details Including Assign Products
     *
     * @param int $productId
     * @param int $mode [optional]
     * @param string $sort [optional]
     * @param string $order [optional]
     *
     * @return array
     */
    public function getTotalProducts($productId, $mode = 0, $sort = '', $order = 'ASC')
    {
        $totalProducts = [];
        $collection = $this->getAssignProducts($productId, $sort, $order);
        foreach ($collection as $assignProduct) {
            $productData = [];
            $productData['description'] = $assignProduct->getDescription();
            $productData['price'] = (float) $assignProduct->getPrice();
            $productData['qty'] = (int) $assignProduct->getQty();
            $productData['assign_id'] = $assignProduct->getId();
            $productData['seller_id'] = $assignProduct->getSellerId();
            $productData['image'] = $assignProduct->getImage();
            $productData['condition'] = $assignProduct->getCondition();
            $totalProducts[] = $productData;
        }
        if ($mode == 0) {
            $product = $this->getProduct($productId);
            if ($product->getId()) {
                $sellerId = $this->getSellerIdByProductId($productId);
                $productData = [];
                $productData['description'] = $product->getDescription();
                $productData['price'] = (float) $product->getFinalPrice();
                $productData['qty'] = (int) $product->getQty();
                $productData['assign_id'] = 0;
                $productData['seller_id'] = $sellerId;
                $totalProducts[] = $productData;
            }
        }
        return $totalProducts;
    }

    /**
     * Get All Assign Product Details Excluding Main Product
     *
     * @param int $productId
     *
     * @return array
     */
    public function getAllAssignedProducts($productId)
    {
        $totalProducts = $this->getTotalProducts($productId, 1, 'price');
        return $totalProducts;
    }

    /**
     * Get Minimum Price with Currency
     *
     * @param int $productId
     *
     * @return string
     */
    public function getMinimumPriceHtml($productId)
    {
        $totalProducts = $this->getTotalProducts($productId);
        $prices = [];
        foreach ($totalProducts as $key => $product) {
            $prices[$key] = $product['price'];
        }
        sort($prices);
        $price = $prices[0];
        return $this->_currency->currency($price, true, false);
    }

    /**
     * Check Whether Product is Assigned to Seller or Not
     *
     * @param int $productId
     *
     * @return bool
     */
    public function productHasSeller($productId)
    {
        $flag = 0;
        $collection = $this->getMpProductCollection()
                            ->addFieldToFilter('mageproduct_id', $productId);
        foreach ($collection as $sellerProduct) {
            if ($sellerProduct->getId()) {
                $flag = 1;
            }
        }
        if ($flag == 1) {
            return true;
        }
        return false;
    }

    public function getSortingOrderInfo()
    {
        $assignType = $this->getAssignType();
        if ($assignType == 1) {
            $result = ['sort_by' => 'price', 'order_type' => 'DESC'];
        } elseif ($assignType == 2) {
            $result = ['sort_by' => 'qty', 'order_type' => 'ASC'];
        } elseif ($assignType == 3) {
            $result = ['sort_by' => 'qty', 'order_type' => 'DESC'];
        } else {
            $result = ['sort_by' => 'price', 'order_type' => 'ASC'];
        }
        return $result;
    }

    /**
     * Assign Product to Seller By Product Id
     *
     * @param int $productId
     */
    public function assignSeller($productId)
    {
        if ($this->hasAssignedProducts($productId)) {
            $price = 0;
            $totalQty = 0;
            $assignId = 0;
            $sellerId = 0;
            $sortingInfo = $this->getSortingOrderInfo();
            $sortBy = $sortingInfo['sort_by'];
            $orderType = $sortingInfo['order_type'];
            $assignProducts = $this->getTotalProducts($productId, 1, $sortBy, $orderType);
            foreach ($assignProducts as $key => $product) {
                $totalQty += $product['qty'];
            }
            foreach ($assignProducts as $key => $product) {
                $assignId = $product['assign_id'];
                $sellerId = $product['seller_id'];
                $price = $product['price'];
                break;
            }
            $this->updateStockData($productId, $totalQty, 3);
            $this->updatePrice($productId, $price);
            $collection = $this->getMpProductCollection();
            $sellerProduct = $this->getDataByField($productId, 'mageproduct_id', $collection);
            if ($sellerProduct) {
                if ($sellerId > 0) {
                    $sellerProduct->addData(['seller_id' => $sellerId])
                                ->setId($sellerProduct->getId())
                                ->save();
                }
            }

            $assignProduct = $this->getAssignProduct($assignId);
            $assignProduct->delete();
            if ($sellerId > 0) {
                $collection = $this->getCollection()->addFieldToFilter('product_id', $productId);
                foreach ($collection as $assignProduct) {
                    $this->updateAssignProductOwner($assignProduct, $sellerId);
                }
            }
        }
        $this->removeAssignProducts($productId);
    }

    public function updateAssignProductOwner($assignProduct, $sellerId)
    {
        $assignProduct->addData(['owner_id' => $sellerId])
                    ->setId($assignProduct->getId())
                    ->save();
    }
    /**
     * Remove All Pending Assign Product If Main Product Does Not Exist
     *
     * @param int $productId
     */
    public function removeAssignProducts($productId)
    {
        $product = $this->getProduct($productId);
        if (!$product->getId()) {
            $assignId = 0;
            $collection = $this->getCollection()
                                ->addFieldToFilter('product_id', $productId);
            foreach ($collection as $item) {
                $this->removeAssignProduct($item);
            }
        }
    }

    /**
     * Remove Assign Product If Main Product Does Not Exist
     *
     * @param int $productId
     */
    public function removeAssignProduct($product)
    {
        $product->delete();
    }

    /**
     * Check Whether Product Has Assigned Product Or Not
     *
     * @param int $productId [optional]
     *
     * @return bool
     */
    public function hasAssignedProducts($productId = 0)
    {
        $assignProductCollection = $this->getAssignProducts($productId);
        if ($assignProductCollection->getSize()) {
            return true;
        }

        return false;
    }

    /**
     * Check Whether Added Product to Cart is New or Not
     *
     * @return bool
     */
    public function isNewProduct($productId = 0, $assignId = 0)
    {
        if ($productId == 0) {
            $productId = (int) $this->_request->getParam('product');
        }
        if ($assignId == 0) {
            $assignId = (int) $this->_request->getParam('mpassignproduct_id');
        }
        $cartModel = $this->getCart();
        $quoteId = $cartModel->getQuote()->getId();
        $collection = $this->getQuoteCollection()
                            ->addFieldToFilter('product_id', $productId)
                            ->addFieldToFilter('assign_id', $assignId)
                            ->addFieldToFilter('quote_id', $quoteId);
        foreach ($collection as $item) {
            if ($item->getId() > 0) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get Quote Item Id to Add Quantity to Existing Item In Cart According to Seller
     *
     * @param int $assignId
     * @param int $productId
     * @param int $quoteId
     *
     * @return int
     */
    public function getRequestedItemId($assignId, $productId, $quoteId)
    {
        $itemId = 0;
        $collection = $this->getQuoteCollection()
                            ->addFieldToFilter('assign_id', $assignId)
                            ->addFieldToFilter('product_id', $productId)
                            ->addFieldToFilter('quote_id', $quoteId);
        foreach ($collection as $item) {
            $itemId = $item->getItemId();
            break;
        }
        return $itemId;
    }

    /**
     * Get Price of Assign Product by Assign Id
     *
     * @param int $assignId
     *
     * @return float
     */
    public function getAssignProductPrice($assignId)
    {
        $price = 0;
        $assignProduct = $this->getAssignProduct($assignId);
        if ($assignProduct->getId() > 0) {
            $price = $assignProduct->getPrice();
        }
        return $price;
    }

    public function getSellerId()
    {
        $objectManager       = \Magento\Framework\App\ObjectManager::getInstance();
        $seller = $objectManager->create('Lof\MarketPlace\Model\Seller')->load($this->getCustomerId(), 'customer_id');

        return $seller->getData('seller_id');
    }
    /**
     * Get Assign Data by Quote Item Id
     *
     * @param int $itemId
     *
     * @return array
     */
    public function getAssignDataByItemId($itemId)
    {
        $assignData = ['assign_id' => 0];
        $collection = $this->getQuoteCollection()
                            ->addFieldToFilter('item_id', $itemId);
        foreach ($collection as $item) {
            $assignData['seller_id'] = $item->getSellerId();
            $assignData['assign_id'] = $item->getAssignId();
            break;
        }
        return $assignData;
    }

    /**
     * Check Whether Quantity is Allowed from Seller or Not
     *
     * @param int $qty
     * @param int $productId
     * @param int $assignId
     *
     * @return bool
     */
    public function isQtyAllowed($qty, $productId, $assignId)
    {
        $product = $this->getProduct($productId);
        $productType = $product->getTypeId();
        $allowedProductTypes = ['simple', 'virtual'];
        if (!in_array($productType, $allowedProductTypes)) {
            return true;
        }
        $totalQty = 0;
        if ($assignId == 0) {
            $collection = $this->getProductCollection();
            $alias = 'qty';
            $table = 'cataloginventory_stock_item';
            $field = 'qty';
            $bind = 'product_id = entity_id';
            $cond = '{{table}}.stock_id = 1';
            $joinType = 'left';
            $collection->joinField($alias, $table, $field, $bind, $cond, $joinType);
            $collection->addFieldToFilter('entity_id', $productId);
            foreach ($collection as $item) {
                $totalQty = $item->getQty();
            }
            $collection = $this->getCollection()
                            ->addFieldToFilter('product_id', $productId);
            foreach ($collection as $item) {
                $totalQty = $totalQty - (int) $item->getQty();
            }
        } else {
            $assignProduct = $this->getAssignProduct($assignId);
            if ($assignProduct->getId() > 0) {
                $totalQty = (int) $assignProduct->getQty();
            }
        }
        $inCartQty = $this->inCartQty($productId, $assignId);
        $totalQty = $totalQty - $inCartQty;
        if ($totalQty >= $qty) {
            return true;
        }
        return false;
    }

    /**
     * Get Quantity Present in Cart
     *
     * @param int $productId
     * @param int $assignId
     *
     * @return int
     */
    public function inCartQty($productId, $assignId)
    {
        $qty = 0;
        $cartModel = $this->getCart();
        $quoteId = $cartModel->getQuote()->getId();
        $collection = $this->getQuoteCollection()
                            ->addFieldToFilter('product_id', $productId)
                            ->addFieldToFilter('assign_id', $assignId)
                            ->addFieldToFilter('quote_id', $quoteId);
        foreach ($collection as $item) {
            $qty = $item->getQty();
        }
        return $qty;
    }

    /**
     * Upload Image of Assign Product
     *
     * @return bool
     */
    public function uploadImage()
    {
        $fileId = "image";
        $uploadPath = $this->_filesystem
                            ->getDirectoryRead(DirectoryList::MEDIA)
                            ->getAbsolutePath('marketplace/assignproduct/product/');
        $allowedExtensions = ['png', 'jpg', 'jpeg', 'gif'];
        try {
            $uploader = $this->_fileUploader->create(['fileId' => $fileId]);
            $uploader->setAllowedExtensions($allowedExtensions);
            $imageData = $uploader->validateFile();
            $name = $imageData['name'];
            $ext = explode('.', $name);
            $ext = strtolower(end($ext));
            $imageName = 'File-'.time().'.'.$ext;
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(false);
            $uploader->save($uploadPath, $imageName);
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * Get Assign Product Total Quantity by Product Id
     *
     * @param int $productId
     *
     * @return int
     */
    public function getAssignProductQty($productId)
    {
        $totalQty = 0;
        $assignProducts = $this->getAllAssignedProducts($productId);
        foreach ($assignProducts as $assignProduct) {
            $totalQty += $assignProduct['qty'];
        }
        return $totalQty;
    }

    /**
     * Get Full Action Name
     *
     * @return string
     */
    public function getFullActionName()
    {
        return $this->_request->getFullActionName();
    }

    /**
     * Check Whether Customer Is Seller Or Not
     *
     * @param int $sellerId [Optional]
     *
     * @return bool
     */
    public function isSeller($sellerId = '')
    {
        if ($sellerId == '') {
            $sellerId = $this->getCustomerId();
        }
        $seller = $this->getSellerDetails($sellerId);
        if (!is_object($seller)) {
            return false;
        }
        $isSeller = $seller->getIsSeller();
        if ($isSeller == 1) {
            return true;
        }
        return false;
    }

    /**
     * Get Image Url for Assign Product
     *
     * @param string $image
     *
     * @return string
     */
    public function getImageUrl($image)
    {
        $currentStore = $this->_storeManager->getStore();
        $mediaUrl = $currentStore->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        $imageUrl = $mediaUrl.''.$image;
        return $imageUrl;
    }

    /**
     * Get First Object From Collection
     *
     * @param array | int | string $value
     * @param array | string $field
     * @param object $collection
     *
     * @return $object
     */
    public function getDataByField($values, $fields, $collection)
    {
        $item = false;
        if (is_array($values)) {
            foreach ($values as $key => $value) {
                $field = $fields[$key];
                $collection = $collection->addFieldToFilter($field, $value);
            }
        } else {
            $collection = $collection->addFieldToFilter($fields, $values);
        }
        foreach ($collection as $item) {
            return $item;
        }
        return $item;
    }

    public function processProductStatus($result)
    {
        if ($result['flag'] == 0) {
            if ($result['status'] == 1) {
                $this->updateStockData($result['product_id'], $result['qty']);
            }
        } else {
            if ($result['status'] == 1) {
                $this->updateStockData($result['product_id'], $result['qty'], 1, $result['old_qty']);
            } else {
                $this->disApproveProduct($result['assign_id'], $result['prev_status'], 0, $result['old_qty']);
            }
        }
    }

    /**
     * Get offer product
     *
     * @param int $productId
     * @param int $itemId
     * @return mixed|object|null
     */
    public function getOfferProduct($productId, $itemId)
    {
        $quote = $this->getQuoteCollection()
            ->addFieldToFilter('product_id', $productId)
            ->addFieldToFilter('item_id', $itemId)
            ->getLastItem();
        return $quote && $quote->getId() ? $quote : null;
    }
}
