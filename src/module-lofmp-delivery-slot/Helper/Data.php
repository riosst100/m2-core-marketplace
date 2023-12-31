<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_DeliverySlot
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\DeliverySlot\Helper;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\App\Helper\AbstractHelper;
use Lof\MarketPlace\Model\SellerFactory;
use Lof\MarketPlace\Model\ConfigFactory;

/**
 * Class Data
 * @package Lofmp\DeliverySlot\Helper
 */
class Data extends AbstractHelper
{
    const XML_PATH_GROUP = 'delivery_slot';
    const XML_PATH_CONFIG = 'delivery_slot/';
    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    public $serializer;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $directory;

    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    protected $_customerSession;

    /**
     * @var SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var ConfigFactory
     */
    protected $sellerConfigFactory;

    protected $_seller = [];

    protected $_sellerConfigData = null;

    /**
     * Data constructor.
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directory
     * @param \Magento\Customer\Model\SessionFactory $customerSession
     * @param SellerFactory $sellerFactory
     * @param ConfigFactory $sellerConfigFactory
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        \Magento\Framework\App\Helper\Context $context,
        DateTime $dateTime,
        \Magento\Framework\App\Filesystem\DirectoryList $directory,
        \Magento\Customer\Model\SessionFactory $customerSession,
        SellerFactory $sellerFactory,
        ConfigFactory $sellerConfigFactory
    ) {
        $this->dateTime = $dateTime;
        $this->storeManager = $storeManager;
        $this->directory = $directory;
        $this->_customerSession = $customerSession;
        $this->sellerFactory = $sellerFactory;
        $this->sellerConfigFactory = $sellerConfigFactory;
        parent::__construct($context);
    }

    public function getSeller(){
        $customerId = $this->_customerSession->create()->getCustomerId();
        if($customerId && !isset($this->_seller[$customerId])){
            $this->_seller[$customerId] = $this->sellerFactory->create()->load($customerId, 'customer_id');
            if($this->_seller[$customerId]->getId() && $this->_seller[$customerId]->getStatus() == 0) { //need approval
                $this->_seller[$customerId] = null;
            }
        }
        return $this->_seller[$customerId];
    }

    public function getSellerByCustomer(){
        $seller = $this->getSeller();
        return $seller?$seller->getData():null;
    }
    /**
     * check admin vacation mode
     * @param string $target_date
     * @param string|int|null $storeId
     * @return int|null
     */
    public function checkVacationMode($target_date, $storeId = null)
    {
        $is_enabled = $this->getDeliverySlotVacationConfig('enabled', $storeId);
        $from_date = $this->getDeliverySlotVacationConfig('from_date', $storeId);
        $to_date = $this->getDeliverySlotVacationConfig('to_date', $storeId);
        if ($is_enabled) {
            $fromStrToTime = $this->dateTime->timestamp($this->dateTime->date('d-m-Y', $from_date));
            $toStrToTime = $this->dateTime->timestamp($this->dateTime->date('d-m-Y', $to_date));
            $targetTime = $this->dateTime->timestamp($this->dateTime->date('d-m-Y', $target_date));

            if ($fromStrToTime <= $targetTime && $targetTime <= $toStrToTime) {
                return 0;
            } else {
                return 1;
            }
        } else {
            return 1;
        }
    }

    /**
     * check seller vacation mode
     * @param string $target_date
     * @param int[]|null $sellerIds
     * @param string|int|null $storeId
     * @return int|null
     */
    public function checkSellerVacationMode($target_date, $sellerId = null, $storeId = null)
    {
        if (!$sellerId) {
            return $this->checkVacationMode($target_date, $storeId); //return default vacation mode of admin
        }
        $is_enabled = $this->getSellerDeliverySlotVacationConfig('enabled', $sellerId, $storeId);
        $from_date = $this->getSellerDeliverySlotVacationConfig('from_date', $sellerId, $storeId);
        $to_date = $this->getSellerDeliverySlotVacationConfig('to_date', $sellerId, $storeId);
        if ($is_enabled) {
            $fromStrToTime = $this->dateTime->timestamp($this->dateTime->date('d-m-Y', $from_date));
            $toStrToTime = $this->dateTime->timestamp($this->dateTime->date('d-m-Y', $to_date));
            $targetTime = $this->dateTime->timestamp($this->dateTime->date('d-m-Y', $target_date));
            
            if ($fromStrToTime <= $targetTime && $targetTime <= $toStrToTime) {
                return 0;
            } else {
                return 1;
            }
        } else {
            return 1;
        }
    }
    
    /**
     * Get config allow seller manager Slots
     * @return int|boolean|null
     */
    public function allowSellerManageDeliverySlots($storeId = null) {
        return $this->getDeliverySlotConfig("allow_seller_manage", $storeId);
    }
    /**
     * @param $code
     * @param null $storeId
     * @return mixed
     */
    public function getDeliverySlotConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_CONFIG
            . 'lofmp_delivery_slot_settings/' . $code, $storeId);
    }
    /**
     * @param string $code
     * @param int|null $storeId
     * @return mixed
     */
    public function getDeliverySlotVacationConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_CONFIG
            . 'vacation/' . $code, $storeId);
    }

    /**
     * @param string $code
     * @param int $sellerId
     * @param int|null $storeId
     * @return mixed
     */
    public function getSellerDeliverySlotVacationConfig($code, $sellerId, $storeId = null)
    {
        $path = "delivery_slot/vacation/".$code;
        return $this->getSellerConfig($path, $storeId, true, $sellerId);
    }

    /**
     * @param string $code
     * @param int $sellerId
     * @param int|null $storeId
     * @return mixed
     */
    public function isSellerEnabledDeliverySlot($sellerId, $storeId = null)
    {
        $path = "delivery_slot/general/enabled";
        return $this->getSellerConfig($path, $storeId, true, $sellerId);
    }
    /**
     * @param $field
     * @param null $storeId
     * @return mixed
     */
    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get seller config model
     * @return \Lof\MarketPlace\Model\Config
     */
    public function getSellerConfigModel() 
    {
        return $this->sellerConfigFactory->create();
    }

     /**
     * @param $field
     * @param int|null $storeId
     * @param boolean $returnValue
     * @param int|null $sellerId
     * @param string|int|float|null
     * @return mixed
     */
    public function getSellerConfig($field, $storeId = null, $returnValue = true, $sellerId = null, $default = null)
    {
        $sellerConfigData = $this->getSellerConfigData($storeId, $sellerId);
        if (!$returnValue) {
            return isset($sellerConfigData[$field])?$sellerConfigData[$field]:false;
        }
        return isset($sellerConfigData[$field]) && isset($sellerConfigData[$field]["value"])?$sellerConfigData[$field]["value"]:$default;
    }

    /**
     * Get Seller Config Data
     * @param int|null $storeId
     * @param int|null $sellerId
     * @return 
     */
    public function getSellerConfigData($storeId = null, $sellerId = null)
    {
        if (!$this->_sellerConfigData) {
            if (!$sellerId) {
                $seller = $this->getSeller();
                $sellerId = $seller->getId();
            }
            
            $storeId = $storeId?$storeId:$this->storeManager->getStore()->getId();
            $defaultCollection = $this->sellerConfigFactory->create()->getCollection()
                        ->addFieldToFilter("seller_id", $sellerId)
                        ->addScopeFilter('default', 0, self::XML_PATH_GROUP);

            if ($defaultCollection->getSize()) {
                foreach ($defaultCollection as $configItem) {
                    $this->_sellerConfigData[$configItem->getPath()] = $configItem->getData();
                }
            }

            $collection = $this->sellerConfigFactory->create()->getCollection()
                        ->addFieldToFilter("seller_id", $sellerId)
                        ->addScopeFilter(ScopeInterface::SCOPE_STORE, $storeId, self::XML_PATH_GROUP);
            if ($collection->getSize()) {
                foreach ($collection as $configItem) {
                    $this->_sellerConfigData[$configItem->getPath()] = $configItem->getData();
                }
            }
        }
        return $this->_sellerConfigData;
    }

    /**
     * Get current store
     * @return Object|null
     */
    public function getStore()
    {
        return $this->storeManager->getStore();
    }
}
