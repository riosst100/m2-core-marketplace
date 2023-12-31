<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/terms
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_CustomerMembership
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\CustomerMembership\Helper;

use Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory as SellerCollectionFactory;
use Lof\MarketPlace\Model\Seller;
use Magento\Customer\Model\SessionFactory as CustomerSessionFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var string
     */
    const XML_PATH_GROUP = "lofsellercommunity";

    /**
     * @var string
     */
    const XML_PATH_ENABLED = 'general_settings/enabled';

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var TimezoneInterface
     */
    protected $timezoneInterface;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Customer\Model\Customer|null
     */
    protected $customer = null;

    /**
     * @var CustomerSessionFactory
     */
    public $customerSessionFactory;

    /**
     * @var int
     */
    protected $sellerId = 0;

    /**
     * @var Seller|null
     */
    protected $_seller = null;

    /**
     * @var SellerCollectionFactory
     */
    protected $sellerCollectionFactory;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\Helper\Context                           $context
     * @param \Magento\Store\Model\StoreManagerInterface                      $storeManager
     * @param CustomerSessionFactory                               $customerSessionFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface               $customerRepository
     * @param SellerCollectionFactory $sellerCollectionFactory
     * @param DateTime $dateTime
     * @param TimezoneInterface $timezoneInterface
     * @param ProductCategoryList $productCategory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        CustomerSessionFactory $customerSessionFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        SellerCollectionFactory $sellerCollectionFactory,
        DateTime $dateTime,
        TimezoneInterface $timezoneInterface
    ) {
        $this->storeManager             = $storeManager;
        $this->customerSessionFactory    = $customerSessionFactory;
        $this->customerRepository        = $customerRepository;
        $this->dateTime = $dateTime;
        $this->timezoneInterface = $timezoneInterface;
        $this->sellerCollectionFactory = $sellerCollectionFactory;
        parent::__construct($context);
    }

    /**
     * @param string $key
     * @param string $group
     * @param int|string|null $store
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getConfig($key, $group = "", $store = null)
    {
        if (empty($group)) {
            $group = self::XML_PATH_GROUP;
        }
        $store  = $this->storeManager->getStore($store);
        return $this->scopeConfig->getValue(
            $group.'/' . $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Is enabled
     * @return bool|int
     */
    public function isEnabled()
    {
        return (int)$this->getConfig(self::XML_PATH_ENABLED);
    }

    /**
     * get base url
     *
     * @return string
     */
    public function getBaseUrl()
    {
        try {
            return $this->storeManager->getStore()->getBaseUrl();
        } catch (NoSuchEntityException $e) {
            return $e;
        }
    }

    /**
     * @return \Lof\MarketPlace\Model\Seller
     */
    public function getSeller()
    {
        if (!$this->_seller && $this->getSellerId()) {
            $this->_seller = $this->getSellerById($this->getSellerId());
        }
        return $this->_seller;
    }

    /**
     * get current seller id
     * @return int
     */
    public function getSellerId()
    {
        if (!$this->sellerId && $this->getCustomerId()) {
            $seller = $this->getSellerByCustomer($this->getCustomerId());
            $this->sellerId = $seller && $seller->getId() ? $seller->getId() : 0;
        }
        return $this->sellerId;
    }

    /**
     * Set seller id
     *
     * @param int $seller_id
     * @return $this
     */
    public function setSellerId($seller_id)
    {
        $this->sellerId = $seller_id;
        return $this;
    }

    /**
     * @return \Magento\Customer\Model\Customer|null
     */
    public function getCustomer()
    {
        if (!$this->_customer) {
            $this->_customer = $this->customerSessionFactory->create()->getCustomer();
        }
        return $this->_customer;
    }

    /**
     * get current customer id
     * @return int
     */
    public function getCustomerId()
    {
        if (!$this->_customerId) {
            /** @var \Magento\Customer\Model\Customer $customer  */
            $customer = $this->getCustomer();
            $this->_customerId = $customer ? (int)$customer->getId() : 0;
        }
        return $this->_customerId;
    }

    /**
     * get seller by customer id
     *
     * @param int $customerId
     * @return \Lof\MarketPlace\Model\Seller
     */
    public function getSellerByCustomer($customerId)
    {
        $seller = $this->sellerCollectionFactory->create()
            ->addFieldToFilter('customer_id', ['eq' => $customerId])
            ->addFieldToFilter("status", Seller::STATUS_ENABLED)
            ->getFirstItem();

        $this->_seller = $seller;
        return $this->_seller;
    }

    /**
     * get seller by seller id
     *
     * @param int $sellerId
     * @return \Lof\MarketPlace\Model\Seller
     */
    public function getSellerById($sellerId)
    {
        $seller = $this->sellerCollectionFactory->create()
            ->addFieldToFilter('seller_id', ['eq' => $sellerId])
            ->addFieldToFilter("status", Seller::STATUS_ENABLED)
            ->getFirstItem();

        $this->_seller = $seller;
        return $this->_seller;
    }

    /**
     * get seller by seller url
     *
     * @param string $sellerUrl
     * @return \Lof\MarketPlace\Model\Seller
     */
    public function getSellerByUrl($sellerUrl)
    {
        $seller = $this->sellerCollectionFactory->create()
            ->addFieldToFilter('url_key', ['eq' => $sellerUrl])
            ->addFieldToFilter("status", Seller::STATUS_ENABLED)
            ->getFirstItem();

        $this->_seller = $seller;
        return $this->_seller;
    }

    /**
     * Get Date Time
     * @return DateTime
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * Get Timezone date time
     * @param string $dateTime
     * @return string|null
     */
    public function getTimezoneDateTime($dateTime = "today")
    {
        if ($dateTime === "today" || !$dateTime) {
            $dateTime = $this->dateTime->gmtDate();
        }

        $today = $this->timezoneInterface
            ->date(
                new \DateTime($dateTime)
            )->format('Y-m-d H:i:s');
        return $today;
    }

    /**
     * Get Timezone Name
     * @return string
     */
    public function getTimezoneName()
    {
        return $this->timezoneInterface->getConfigTimezone(\Magento\Store\Model\ScopeInterface::SCOPE_STORES);
    }

    /**
     * write system log
     *
     * @param array|mixed
     * @param string $logFileName
     * @return void
     */
    public function writeLog($data, $logFileName = "sellercommunity.log")
    {
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/'.$logFileName);
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        if ($data) {
            $message_log = "--- Start ---";
            $message_log .= "\nTrace: ";
            if (is_array($data)) {
                foreach ($data as $key => $value) {
                    if (!is_object($value)) {
                        $message_log .= "\n";
                        $message_log .= is_array($value)?implode(", ", $value):$value;
                    }
                }
            } elseif (!is_object($data)) {
                $message_log .= "\n".$data;
            }
            $message_log .= "\n--- End ---";
            $logger->info($message_log);
        }
    }

    /**
     * @param $data_array
     * @return array
     */
    public function xss_clean_array($data_array)
    {
        $result = [];
        if (is_array($data_array)) {
            foreach ($data_array as $key => $val) {
                $val = $this->xss_clean($val);
                $result[$key] = $val;
            }
        }
        return $result;
    }

    /**
     * @param $data
     * @return string|string[]|null
     */
    public function xss_clean($data)
    {
        if (!is_string($data)) {
            return $data;
        }
        // Fix &entity\n;
        $data = str_replace(['&amp;', '&lt;', '&gt;'], ['&amp;amp;', '&amp;lt;', '&amp;gt;'], $data);
        $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
        $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
        $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

        // Remove any attribute starting with "on" or xmlns
        $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

        // Remove javascript: and vbscript: protocols
        $data = preg_replace(
            '#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu',
            '$1=$2nojavascript...',
            $data
        );
        $data = preg_replace(
            '#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu',
            '$1=$2novbscript...',
            $data
        );
        $data = preg_replace(
            '#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u',
            '$1=$2nomozbinding...',
            $data
        );

        // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
        $data = preg_replace(
            '#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i',
            '$1>',
            $data
        );
        $data = preg_replace(
            '#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i',
            '$1>',
            $data
        );
        $data = preg_replace(
            '#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu',
            '$1>',
            $data
        );

        // Remove namespaced elements (we do not need them)
        $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

        do {
            // Remove really unwanted tags
            $old_data = $data;
            $data = preg_replace(
                '#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i',
                '',
                $data
            );
        } while ($old_data !== $data);

        // we are done...
        return $data;
    }
}
