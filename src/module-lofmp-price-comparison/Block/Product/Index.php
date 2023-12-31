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


namespace Lofmp\PriceComparison\Block\Product;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollection;
use Lofmp\PriceComparison\Model\ResourceModel\Product\CollectionFactory;

class Index extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var ProductCollection
     */
    protected $_productCollection;

    /**
     * @var CollectionFactory
     */
    protected $_itemsCollection;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $_productList;
     /**
      * @var \Lof\MarketPlace\Helper\Data
      */
    protected $marketHelper;
    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param ProductCollection $productCollectionFactory
     * @param CollectionFactory $itemsCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Helper\Data $marketHelper,
        ProductCollection $productCollectionFactory,
        CollectionFactory $itemsCollectionFactory,
        array $data = []
    ) {
        $this->_storeManager = $context->getStoreManager();
        $this->_customerSession = $customerSession;
        $this->_productCollection = $productCollectionFactory;
        $this->_itemsCollection = $itemsCollectionFactory;
        $this->marketHelper = $marketHelper;
        parent::__construct($context, $data);
    }

    /**
     */
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('My Assigned Product List'));
    }

    /**
     * @return bool|\Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getAllProducts()
    {
        if (!$this->_productList) {
               $seller_id = $this->marketHelper->getSellerId();
            $sellercollection = $this->_itemsCollection
                                ->create()
                                ->addFieldToFilter('seller_id', $seller_id);

            $collection = $this->_productCollection
                                ->create()
                                ->addFieldToSelect('*');
            $sellercollection->setOrder('created_at', 'desc');
            $this->_productList = $sellercollection;
        }
        return $this->_productList;
    }
    /**
     * Get Current Currency Symbol
     *
     * @return string
     */
    public function getCurrencySymbol()
    {
        $symbol = $this->_storeManager->getStore()->getBaseCurrencyCode();
        return $symbol;
    }
}
