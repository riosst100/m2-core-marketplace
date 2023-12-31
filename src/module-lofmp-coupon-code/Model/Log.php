<?php
/**
 * LandofCoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   LandofCoder
 * @package    Lofmp_CouponCode
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\CouponCode\Model;

use Lofmp\CouponCode\Api\Data\LogInterface;

class Log extends \Magento\Framework\Model\AbstractModel implements LogInterface
{


    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $_storeManager;

    /**
     * URL Model instance
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    protected $_resource;

    protected $_couponCollection;
    /**
     * Page cache tag
     */
    /**
     * @param \Magento\Framework\Model\Context                          $context                  
     * @param \Magento\Framework\Registry                               $registry                 
     * @param \Lofmp\CouponCode\Model\ResourceModel\Coupon|null                $resource
     * @param \Lofmp\CouponCode\Model\ResourceModel\Coupon\Collection|null $resourceCollection                 
     * @param \Lofmp\CouponCode\Model\ResourceModel\Coupon\CollectionFactory|null $couponCollection      
     * @param \Magento\Store\Model\StoreManagerInterface                $storeManager             
     * @param \Magento\Framework\UrlInterface                           $url                      
     * @param array                                                     $data                     
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Lofmp\CouponCode\Model\ResourceModel\Log $resource = null,
        \Lofmp\CouponCode\Model\ResourceModel\Log\Collection $resourceCollection = null,
        \Lofmp\CouponCode\Model\ResourceModel\Log\CollectionFactory $couponCollection,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $url,
        array $data = []
        ) {
        $this->_storeManager = $storeManager;
        $this->_url = $url;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_resource = $resource;
        $this->_couponCollection = $couponCollection;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Lofmp\CouponCode\Model\ResourceModel\Log');
    }

    /**
     * Get log_id
     * @return string
     */
    public function getLogId()
    {
        return $this->getData(self::LOG_ID);
    }

    /**
     * Set log_id
     * @param string $logId
     * @return \Lofmp\CouponCode\Api\Data\LogInterface
     */
    public function setLogId($logId)
    {
        return $this->setData(self::LOG_ID, $logId);
    }
}
