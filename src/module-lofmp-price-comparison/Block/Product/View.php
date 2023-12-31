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
use Lof\MarketPlace\Model\ResourceModel\SellerProduct\CollectionFactory;

class View extends \Magento\Framework\View\Element\Template
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
     * @var \Lofmp\PriceComparison\Helper\Data
     */
    protected $_assignHelper;
     /**
      * @var \Lofmp\PriceComparison\Helper\Data
      */
    protected $marketHelper;

    /**
     * @var ProductCollection
     */
    protected $_productCollection;

    /**
     * @var CollectionFactory
     */
    protected $_mpProductCollection;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    protected $_productStatus;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_productVisibility;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $_productList;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lofmp\PriceComparison\Helper\Data $helper
     * @param ProductCollection $productCollectionFactory
     * @param CollectionFactory $mpProductCollectionFactory
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus
     * @param \Magento\Catalog\Model\Product\Visibility $productVisibility
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lofmp\PriceComparison\Helper\Data $helper,
        \Lof\MarketPlace\Helper\Data $marketHelper,
        ProductCollection $productCollectionFactory,
        CollectionFactory $mpProductCollectionFactory,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        array $data = []
    ) {
        $this->_storeManager = $context->getStoreManager();
        $this->_customerSession = $customerSession;
        $this->_assignHelper = $helper;
        $this->marketHelper = $marketHelper;
        $this->_productCollection = $productCollectionFactory;
        $this->_mpProductCollection = $mpProductCollectionFactory;
        $this->_productStatus = $productStatus;
        $this->_productVisibility = $productVisibility;
        parent::__construct($context, $data);
    }

    /**
     */
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('My Product List'));
    }

    /**
     * @return bool|\Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getAllProducts()
    {
        if (!$this->_productList) {
            $queryString = $this->_assignHelper->getQueryString();
            if ($queryString != '') {
                $seller_id = $this->marketHelper->getSellerId();

                $sellercollection = $this->_mpProductCollection
                                        ->create()
                                        ->addFieldToFilter('seller_id', ['neq' => $seller_id]);
                $products = [];
                
                foreach ($sellercollection as $data) {
                    array_push($products, $data->getProductId());
                }

                $allowedTypes = ['simple', 'virtual'];
                $collection = $this->_productCollection
                                    ->create()
                                    ->addFieldToSelect('*');

                $collection->addAttributeToFilter([
                                ['attribute' => 'sku', 'like' => '%'.$queryString.'%'],
                                ['attribute' => 'name', 'like' => '%'.$queryString.'%']
                            ])
                            ->addAttributeToFilter('type_id', ['in' => $allowedTypes])
                            ->addAttributeToFilter('status', ['in' => $this->_productStatus->getVisibleStatusIds()])
                            ->setVisibility($this->_productVisibility->getVisibleInSiteIds())
                            ->addFieldToFilter('entity_id', ['in' => $products])->setOrder('created_at', 'desc');

            } else {
                $collection = $this->_productCollection
                                    ->create()
                                    ->addFieldToSelect('*')
                                    ->addFieldToFilter('entity_id', 0);
            }
            $this->_productList = $collection;
        }
        return $this->_productList;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getAllProducts()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'lofmpcomparison.product.list.pager'
            )->setCollection(
                $this->getAllProducts()
            );
            $this->setChild('pager', $pager);
            $this->getAllProducts()->load();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
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
