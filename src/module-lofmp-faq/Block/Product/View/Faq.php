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
namespace Lofmp\Faq\Block\Product\View;

class Faq extends \Magento\Framework\View\Element\Template
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Lofmp\Faq\Helper\Data
     */
    protected $_configHelper;

    protected $_marketplaceHelper;

    public $_sellerSettings;

    /**
     * @var \Lofmp\Faq\Model\Question
     */
    protected $_questionFactory;

    /**
     * @var \Lofmp\Faq\Model\Category
     */
    protected $_categoryFactory;

    /**
     * @var \Lofmp\Faq\Model\ResourceModel\Question\Collection
     */
    public $_collection;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resource;

    protected $faqProductFactory;
    protected $faqSellerFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context
     * @param \Magento\Framework\Registry
     * @param \Lofmp\Faq\Model\Question
     * @param \Lofmp\Faq\Model\Category
     * @param \Magento\Framework\App\ResourceConnection
     * @param \Lofmp\Faq\Helper\Data
     * @param \Lofmp\Faq\Model\EnableSellerFactory $faqSellerFactory
     * @param \Lofmp\Faq\Model\EnableProductFactory $faqProductFactory
     * @param array
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Lofmp\Faq\Model\Question $questionFactory,
        \Lofmp\Faq\Model\Category $categoryFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        \Lofmp\Faq\Helper\Data $configHelper,
        \Lofmp\Faq\Model\EnableSellerFactory $faqSellerFactory,
        \Lofmp\Faq\Model\EnableProductFactory $faqProductFactory,
        array $data = []
    ) {
        $this->_configHelper      = $configHelper;
        $this->_coreRegistry      = $registry;
        $this->_questionFactory   = $questionFactory;
        $this->_categoryFactory   = $categoryFactory;
        $this->_resource          = $resource;
        $this->faqSellerFactory = $faqSellerFactory;
        $this->faqProductFactory = $faqProductFactory;
        parent::__construct($context);
    }

    public function getConfig($key)
    {
        $result = $this->_configHelper->getConfig($key);
        return $result;
    }

    public function _construct()
    {
        parent::_construct();
    }
       /**
     * Set tab title
     *
     * @return void
     */
    public function setTabTitle()
    {
        $title =  __('Faq');
        $this->setTitle($title);
    }
    /**
     * @param Array
     * @return $this
    */
    public function setCollection($collection)
    {
        $this->_collection = $collection;
        return $this;
    }

    /**
     * @return Array
     */
    public function getCollection(){
        return $this->_collection;
    }

    public function getProduct(){
        return $this->_coreRegistry->registry('current_product');
    }

    public function getToolbarBlock()
    {
        $block = $this->getLayout()->getBlock('lofmpfaq_toolbar');
        if ($block) {
            return $block;
        }
    }

    public function getCategories(){
        $currentProduct = $this->getProduct();
        $sellerId = $currentProduct->getSellerId();
        $storeId = $this->_storeManager->getStore()->getId();
        $categoryCollection = $this->_categoryFactory->getCollection()
            ->addFieldToFilter('status',1)
            ->addFieldToFilter('main_table.seller_id', ['eq' => $sellerId])
            ->addFieldToFilter('store_id', ['eq' => $storeId]);
        return $categoryCollection;
    }

    public function _toHtml(){
        $adminEnable = $this->getConfig('general_settings/enable');
        $adminEnableAll = $this->getConfig('general_settings/enable_all_seller');

        $currentProduct = $this->getProduct();
        $sellerId = $currentProduct->getSellerId();

        $sellerEnable = $this->faqSellerFactory->create()
                             ->load($sellerId, "seller_id")->getSellerId();
        $productEnable = $this->faqProductFactory->create()
                             ->load($currentProduct->getId(), "product_id")->getId();

        if($adminEnable && ($sellerEnable || $adminEnableAll) && $productEnable){
            return parent::_toHtml();
        }

        return;
    }

    public function getSellerSettings(){
        if(!isset($this->_sellerSettings)){
            $currentProduct = $this->getProduct();
            $sellerId = $currentProduct->getSellerId();
            $this->_sellerSettings = $this->_configHelper->getSellerSettings($sellerId);
        }
        return $this->_sellerSettings;
    }
    
    protected function _beforeToHtml()
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $currentProduct = $this->getProduct();
        $sellerId = $currentProduct->getSellerId();
        $sellerSettings = $this->getSellerSettings();
        $itemsperpage = isset($sellerSettings['itemsperpage']) && $sellerSettings['itemsperpage']?(int)$sellerSettings['itemsperpage']:20;

        $questionCollection = $this->_questionFactory->getCollection()
                                ->addFieldToFilter('main_table.status',1)
                                ->addFieldToFilter('main_table.seller_id', ['eq' => $sellerId])
                                ->addFieldToFilter('main_table.store_id', ['eq' => $storeId]);

        $questionCollection->getSelect()
                            ->join(
                                ['question_product' => $this->_resource->getTableName('lofmp_faq_question_product')],
                                'question_product.question_id = main_table.question_id'
                            )
                            ->where('question_product.product_id = (?)', $currentProduct->getId())
                            ->order('position ASC');
        
        if($itemsperpage ){
            $questionCollection->setPageSize($itemsperpage);
            $questionCollection->setCurPage(1);
        }
        
        $categoryIds = [];
        foreach($questionCollection as $question){
            $categoryIds[] = $question->getCategoryId();
        }

        $categoryCollection = $this->_categoryFactory->getCollection()
                                   ->addFieldToFilter('status',1)
                                   ->addFieldToFilter('store_id', ['eq' => $storeId])
                                   ->addFieldToFilter('category_id', ['in' => $categoryIds]);

        $categoryCollection->getSelect()
                           ->group('category_id')
                           ->order('position ASC');

        $data = [];
        foreach($categoryCollection as $category){
            $categoryId = $category->getId();
            $questions = [];
            foreach ($questionCollection as $id => $question){
                if($question->getCategoryId() == $categoryId){
                    $questions[] = $question;
                }
            }
            $data[] = ['category' => $category, 'questions' => $questions];
        }
        $this->setCollection($data);

        $toolbar = $this->getToolbarBlock();
        // set collection to toolbar and apply sort
        if($itemsperpage && $toolbar){
            $toolbar->setData('_current_limit',$itemsperpage)->setCollection($questionCollection);
            $this->setChild('toolbar', $toolbar);
        }

        return parent::_beforeToHtml();
    }
}