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
 * @package    Lofmp_StoreLocator
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\StoreLocator\Model;

class Tag extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Blog's Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

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
     * Page cache tag
     */
    const CACHE_TAG = 'lofmp_storelocator_tag';
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
        \Lofmp\StoreLocator\Model\ResourceModel\Tag $resource = null,
        \Lofmp\StoreLocator\Model\ResourceModel\Tag\Collection $resourceCollection = null,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $url,
        \Lofmp\StoreLocator\Helper\Data $storelocatorHelper,
        array $data = []
        ) {
        $this->_storeManager = $storeManager;
        $this->_url = $url;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_resource = $resource;
        $this->_storelocatorHelper = $storelocatorHelper;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Lofmp\StoreLocator\Model\ResourceModel\Tag');
    }

    /**
     * Prevent blocks recursion
     *
     * @return \Magento\Framework\Model\AbstractModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave()
    {
        $needle = 'tag_id="' . $this->getId() . '"';
        if ($this->getContent() && false == strstr($this->getContent(), $needle)) {
            return parent::beforeSave();
        }
        throw new \Magento\Framework\Exception\LocalizedException(
            __('Make sure that category content does not reference the block itself.')
            );
    }

    public function getCreateTime(){
        $dateTime = $this->getData('create_time');
        $dateFormat = $this->_storelocatorHelper->getConfig('general/dateformat');
        return $this->_storelocatorHelper->getFormatDate($dateTime, $dateFormat);
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

    public function getListTagNameByIds($ids){

        $readConnection = $this->_resource->getConnection('core_read');

        $where = 'tag_id IN('.$ids.')';

        $query = 'SELECT name FROM ' . $this->_resource->getTable('lofmp_storelocator_tag') . ' WHERE ' . $where;

        $results = $readConnection->fetchAll($query);

        $data = array();

        foreach ($results as $key => $result) {
            $data[$key] = $result['name'];
        }
        
        return implode(",", $data);
    }

    public function selectNameIN($names){
        $readConnection = $this->_resource->getConnection('core_read');
        $query = $this->getCollection()->getSelect('name')->where('name IN (?) ', explode(',',$names));
        $results = $readConnection->fetchAll($query);

        $data = array();

        foreach ($results as $result) {
            $data[] = $result['tag_id'];
        }

        return $data;
    }
}
