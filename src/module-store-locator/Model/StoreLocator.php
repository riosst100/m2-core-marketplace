<?php

namespace CoreMarketplace\StoreLocator\Model;

class StoreLocator extends \Lofmp\StoreLocator\Model\StoreLocator
{
    /**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $_storeManager;

    /**
     * URL Model instance
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    /**
     * @var \Magento\Catalog\Helper\Category
     */
    protected $_storelocatorHelper;

    protected $_resource;
    
    /**
     * @param \Magento\Framework\Model\Context                               $context                  
     * @param \Magento\Framework\Registry                                    $registry                 
     * @param \Magento\Store\Model\StoreManagerInterface                     $storeManager             
     * @param \Lof\Blog\Model\ResourceModel\Blog|null                        $resource                 
     * @param \Lof\Blog\Model\ResourceModel\Blog\Collection|null             $resourceCollection       
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory 
     * @param \Magento\Store\Model\StoreManagerInterface                     $storeManager             
     * @param \Magento\Framework\UrlInterface                                $url                      
     * @param \Lof\Blog\Helper\Data                                          $brandHelper              
     * @param array                                                          $data                     
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Lofmp\StoreLocator\Model\ResourceModel\StoreLocator $resource = null,
        \Lofmp\StoreLocator\Model\ResourceModel\StoreLocator\Collection $resourceCollection = null,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $url,
        \Lofmp\StoreLocator\Helper\Data $storelocatorHelper,
        array $data = []
    ) {
        $this->_storeManager = $storeManager;
        $this->_url = $url;
        $this->_resource = $resource;
        $this->_storelocatorHelper = $storelocatorHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $storeManager, $url, $storelocatorHelper,$data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Lofmp\StoreLocator\Model\ResourceModel\StoreLocator');
    }

    /**
     * Prevent blocks recursion
     *
     * @return \Magento\Framework\Model\AbstractModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave()
    {
        return parent::beforeSave();
    }

    public function getStoreLocatorId(){
        return $this->getId();
    }

    public function getCreateTime(){
        $dateTime = $this->getData('create_time');
        $dateFormat = $this->_storelocatorHelper->getConfig('general/dateformat');
        return $this->_storelocatorHelper->getFormatDate($dateTime, $dateFormat);
    }

    public function getTag()
    {
        return $this->hasData('tag') ? $this->getData('tag') : $this->getData('tag');
    }

    public function getCategory()
    {
        return $this->hasData('category') ? $this->getData('category') : $this->getData('category');
    }

    /**
     * Receive page store ids
     *
     * @return int[]
     */
    public function getStores()
    {
        return $this->hasData('stores') ? $this->getData('stores') : $this->getData('store_id');
    }

    /**
     * Prepare page's statuses.
     * Available event cms_page_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }
}
