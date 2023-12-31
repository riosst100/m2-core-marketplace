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
 * @package    Lofmp_CancelOrder
 * @copyright  Copyright (c) 2021 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\CancelOrder\Observer\MarketPlace;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class SellerCancelOrder implements ObserverInterface
{
    const XML_PATH_ENABLED = 'lofmp_cancelorder/general/enable';
    const XML_PATH_ENABLED_SEND_TO_ADMIN = 'lofmp_cancelorder/general/notify_admin';
    const XML_PATH_ENABLED_SEND_TO_CUSTOMER = 'lofmp_cancelorder/general/notify_customer';
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
    /**
     * @var \Magento\Catalog\Model\Session
     */
    protected $catalogSession;
    /**
     * Warning (exception) errors array
     *
     * @var array
     */
    protected $_errors = [];

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTimeFactory
     */
    protected $_dateFactory;

    /**
     * Catalog data
     *
     * @var \Magento\Catalog\Helper\Data
     */
    protected $_catalogData = null;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Website collection array
     *
     * @var array|null
     */
    protected $_website = null;
    

    protected $stockItem;

    protected $_collectionSubscriber;
    protected $_collectionPrice;
    protected $_email;
    protected $sourceDataBySku;

    /**
     * @var Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_dateTime;

    /**
     * @var TimezoneInterface
     */
    protected $_timezoneInterface;

    public function __construct(
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockItem,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Lofmp\CancelOrder\Model\Email $email,
        \Magento\InventoryCatalogAdminUi\Model\GetSourceItemsDataBySku $sourceDataBySku,
        TimezoneInterface $timezoneInterface,
        DateTime $dateTime
    )
    {
        $this->_email = $email;
        $this->_catalogData     = $catalogData;
        $this->stockItem = $stockItem;
        $this->_dateFactory     = $dateFactory;
        $this->_scopeConfig     = $scopeConfig;
        $this->_storeManager      = $storeManager;
        $this->catalogSession   = $catalogSession;
        $this->customerRepository = $customerRepository;
        $this->sourceDataBySku = $sourceDataBySku;
        $this->_dateTime = $dateTime;
        $this->_timezoneInterface = $timezoneInterface;
    }

    public function getTimezoneDateTime($dateTime = "today"){
        if($dateTime === "today" || !$dateTime){
            $dateTime = $this->_dateTime->gmtDate();
        }
        
        $today = $this->_timezoneInterface
            ->date(
                new \DateTime($dateTime)
            )->format('Y-m-d H:i:s');
        return $today;
    }

    public function getSourceStocks($_product_sku){
        return $this->sourceDataBySku->execute($_product_sku);
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $seller_id = $observer->getSellerId();
        $order_id = $observer->getOrderId();
        $order = $observer->getOrder();
        if ($order) {
            if (!$this->_scopeConfig->getValue(
                self::XML_PATH_ENABLED,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $order->getStoreId()
            )){
                return false;
            }

            $customerEmail = $order->getCustomerEmail();
            $customerName = $order->getCustomerName();
            $email = $this->_email;
            $emailData = [
                "seller_id" => $seller_id,
                "order_id" => $order_id,
                "order" => $order
            ];
            try {
                $website = $this->_getWebsiteByStore($order->getStoreId());
                $email->setWebsite($website);
                //process send email
                //1. Notify to admin user
                if ($this->_scopeConfig->getValue(
                    self::XML_PATH_ENABLED_SEND_TO_ADMIN,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $order->getStoreId()
                )) {
                    $email->setEmailData($emailData)
                        ->send(true);
                }
                //2. Notify to customer
                if ($this->_scopeConfig->getValue(
                    self::XML_PATH_ENABLED_SEND_TO_CUSTOMER,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $order->getStoreId()
                )) {
                    $email->clean()
                        ->setEmailData($emailData)
                        ->setReciverEmail($customerEmail)
                        ->setReciverName($customerName)
                        ->send();
                }
                return true;
            } catch (\Exception $e) {
                echo $e->getMessage();
                return false;
            }
        }
    }

    protected function _getWebsiteByStore($storeId = 0)
    {
        if ($this->_website === null) {
            try {
                $websiteId = (int)$this->_storeManager->getStore($storeId)->getWebsiteId();
                $this->_website = $this->_storeManager->getWebsite($websiteId);
            } catch (\Exception $e) {
                $this->_errors[] = $e->getMessage();
            }
        }
        return $this->_website;
    }
}