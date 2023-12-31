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
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Block\Product;

use Magento\Catalog\Model\Product;

class Add extends \Magento\Framework\View\Element\Template
{
    /**
     * Group Collection
     */
    protected $_sellerCollection;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Catalog\Helper\Category
     */
    protected $_sellerHelper;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     *
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection
     */
    protected $attributeSet;

    /**
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Product
     */
    protected $product;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Lof\MarketPlace\Helper\Data $sellerHelper
     * @param \Lof\MarketPlace\Model\Seller $sellerCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Lof\MarketPlace\Helper\Data $sellerHelper,
        \Lof\MarketPlace\Model\Seller $sellerCollection,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $attributeSet,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        Product $product,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->_sellerCollection = $sellerCollection;
        $this->_sellerHelper = $sellerHelper;
        $this->_coreRegistry = $registry;
        $this->_resource = $resource;
        $this->storeManager = $context->getStoreManager();
        $this->attributeSet = $attributeSet;
        $this->productFactory = $productFactory;
        $this->product = $product;
    }

    /**
     * Retrieve current product model
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->_coreRegistry->registry('current_product');
    }

    /**
     * @return int
     */
    public function getSellerCollection()
    {
        $product = $this->getProduct();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $sellerProduct = $objectManager->get(\Lof\MarketPlace\Model\SellerProduct::class)
            ->load($product->getId(), 'product_id');
        return $sellerProduct->getData() ? $sellerProduct->getSellerId() : 0;
    }

    /**
     * Get base currency symbol
     *
     * @return string
     */
    public function getBaseCurrency()
    {
        return $this->storeManager->getStore()->getBaseCurrencyCode();
    }

    /**
     * Get Attribute set datas
     *
     * @return array
     */
    public function getAttributeSet()
    {
        return $this->attributeSet->toOptionArray();
    }

    /**
     * @param $productId
     * @return Product
     */
    public function getProductData($productId)
    {
        return $this->productFactory->create()->load($productId);
    }

    /**
     * @return string|void
     */
    public function _toHtml()
    {
        if (!$this->_sellerHelper->getConfig('product_view_page/enable_seller_info')) {
            return;
        }

        return parent::_toHtml();
    }

    /**
     * Get Default Attribute Set Id
     *
     * @return int
     */
    public function getDefaultAttributeSetId()
    {
        return $this->product->getDefaultAttributeSetId();
    }

    /**
     * Prepare layout for change buyer
     *
     * @return Object
     */
    public function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Create New Product'));
        return parent::_prepareLayout();
    }
}
