<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_Faq
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\Faq\Block\Marketplace\FaqSetting;

class Setting extends \Magento\Framework\View\Element\Template {

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    public $_categoryFactory;

    public $_helper;
    /**
     * @var \Lof\MarketPlace\Model\Data
     */
    public $_productList;

    public $_settings;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resource;

    public $_currentQuestion;

    public $_animate;
    /**
     * @param \Magento\Framework\View\Element\Template\Context
     * @param \Magento\Framework\Registry
     * @param \Lof\MarketPlace\Model\Seller
     * @param \Magento\Framework\App\ResourceConnection
     * @param array
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Lofmp\Faq\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Lof\MarketPlace\Helper\Data $helper,
        \Lofmp\Faq\Model\Setting $settingModel,
        \Lofmp\Faq\Model\Config\Source\AnimationType $animate,
        \Magento\Framework\App\ResourceConnection $resource,
        array $data = []
    ) {
        $this->_coreRegistry  = $registry;
        $this->_resource      = $resource;
        $this->_helper = $helper;
        $this->_prepareData($productCollectionFactory);
        $this->_prepareSettings($settingModel);
        parent::__construct($context);
    }

    /**
     * Prepare layout for change buyer
     *
     * @return Object
     */
    public function _prepareLayout() {
        $this->pageConfig->getTitle ()->set(__('FAQ Configuration'));
        return parent::_prepareLayout ();
    }

    protected function _prepareData($productCollectionFactory){
        $sellerId = $this->_helper->getSellerId();
        $storeId = $this->_helper->getStoreId();

        $collection = $productCollectionFactory->create();
        $collection->addAttributeToSelect('*')
                   ->addAttributeToFilter('seller_id', ['eq' => $sellerId])
                   ->addStoreFilter($storeId)
                   ->getSelect()->joinLeft(
                       ['status_table' => $collection->getTable('lofmp_faq_enable_product')],
                       'e.entity_id = status_table.product_id',
                       ['faq_status' => 'COALESCE(status_table.status, 0)']
                   );
        $this->_productList = $collection->load();
    }

    protected function _prepareSettings($settingModel){
        $sellerId = $this->_helper->getSellerId();
        $this->_settings = $settingModel->load($sellerId);
    }
}