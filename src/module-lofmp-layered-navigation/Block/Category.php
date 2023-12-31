<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://www.landofcoder.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lofmp_LayeredNavigation
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\LayeredNavigation\Block;

class Category extends \Magento\Framework\View\Element\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $_sellerHelper;

    protected $_categoryCollectionFactory;

    protected $_productRepository;

    /**
     * @var \Lofmp\LayeredNavigation\Helper\Data
     */
    protected $helperData;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context      
     * @param \Magento\Framework\Registry $registry     
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Lof\MarketPlace\Helper\Data $sellerHelper     
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Lofmp\LayeredNavigation\Helper\Data $helperData
     * @param array                                            $data         
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Lof\MarketPlace\Helper\Data $sellerHelper,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Lofmp\LayeredNavigation\Helper\Data $helperData,
        array $data = []
        ) {
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->_productRepository = $productRepository;
        $this->_coreRegistry = $registry;
        $this->_sellerHelper = $sellerHelper;
        $this->helperData = $helperData;
        parent::__construct($context, $data);
    }

    /**
     * Get current seller
     * @return Object|null
     */
    public function getCurrentSeller()
    {
        $seller = $this->_coreRegistry->registry('current_seller');
        if ($seller) {
            $this->setData('current_seller', $seller);
        }
        return $seller;
    }
     /**
     * Get category collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Category\Collection or array
     */
    public function getCategoryCollection()
    {
        $category_id = $this->getCategoryId();
        $collection = $this->_categoryCollectionFactory->create();
        $collection->addAttributeToSelect('*')->addAttributeToFilter('entity_id', array('in' => $category_id));      
        return $collection;
    }
    /**
     * Get product by id
     * @param int $id
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductById($id)
    {        
        return $this->_productRepository->getById($id);
    }
    
    /**
     * Get category ids
     * @return int[]|null
     */
    public function getCategoryId() {
        $category = array();
        foreach ($this->getCurrentSeller()->getData('products') as $key => $product) {
          $categoryIds = $this->getProductById($product['product_id'])->getCategoryIds();
          $category =  array_unique(array_merge($category,$categoryIds));         
        }
        return $category;
    }

    /**
     * Get group list
     */
    public function getGroupList(){
        $collection = $this->_group->getCollection()
        ->addFieldToFilter('status',1)
        ->addFieldToFilter('shown_in_sidebar',1)
        ->setOrder('position','ASC');
        return $collection;
    }
}