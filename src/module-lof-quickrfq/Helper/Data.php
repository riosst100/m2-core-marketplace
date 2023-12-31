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
 * @package    Lof_Quickrfq
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\Quickrfq\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\DataObject;
use Magento\Framework\Escaper;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Class Data
 * @package Lof\Quickrfq\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     *
     */
    const XML_PATH_TAG = 'quickrfq';

    /**
     *
     */
    const XML_PATH_EMAIL_RECIPIENT = 'quickrfq/email/recipient';

    /**
     *
     */
    const XML_PATH_EMAIL_SENDER = 'quickrfq/email/sender';

    /**
     *
     */
    const XML_PATH_QUOTE_FILE_FORMATS = 'quickrfq/upload/file_formats';

    /**
     *
     */
    const EMAIL_TEMPLATE_NOTICE_SENDER = 'quickrfq_email_notice_sender';

    /**
     *
     */
    const EMAIL_TEMPLATE_NOTICE_RECEIVER = 'quickrfq_email_notice_receiver';

    /**
     *
     */
    const XML_PATH_QUOTE_MAXIMUM_FILE_SIZE = 'quickrfq/upload/maximum_file_size';

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_dateTime;

    /**
     * @var TimezoneInterface
     */
    protected $_timezoneInterface;

    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var Escaper
     */
    private $_escaper;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    private $inlineTranslation;

    /**
     * @var TransportBuilder
     */
    private $_transportBuilder;

    /**
     * @var \Magento\Store\Api\Data\StoreInterface|null
     */
    protected $_currentStore = null;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\Helper\Context              $context
     * @param \Magento\Framework\Escaper                         $escaper
     * @param \Magento\Framework\Mail\Template\TransportBuilder  $transportBuilder
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\Message\ManagerInterface        $messageManager
     * @param \Magento\Store\Model\StoreManagerInterface         $storeManager
     * @param DateTime $dateTime
     * @param TimezoneInterface $timezoneInterface
     */
    public function __construct(
        Context $context,
        Escaper $escaper,
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation,
        ManagerInterface $messageManager,
        StoreManagerInterface $storeManager,
        DateTime $dateTime,
        TimezoneInterface $timezoneInterface
    ) {
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_storeManager = $storeManager;
        $this->_escaper = $escaper;
        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->messageManager = $messageManager;
        $this->_dateTime = $dateTime;
        $this->_timezoneInterface = $timezoneInterface;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Stdlib\DateTime\DateTime
     */
    public function getDateTime()
    {
        return $this->_dateTime;
    }

    /**
     * @return string|null
     */
    public function getTimezoneDateTime($dateTime = "today")
    {
        if($dateTime === "today" || !$dateTime){
            $dateTime = $this->_dateTime->gmtDate();
        }
        
        $today = $this->_timezoneInterface
            ->date(
                new \DateTime($dateTime)
            )->format('Y-m-d H:i:s');
        return $today;
    }

    /**
     * @return string|null
     */
    public function getTimezoneName()
    {
        return $this->_timezoneInterface->getConfigTimezone(\Magento\Store\Model\ScopeInterface::SCOPE_STORES);
    }

    /**
     * Check approve quote is expired or not
     * 
     * @return bool
     */
    public function isExpiryQuote($quote) 
    {
        if ($expiryDate = $quote->getExpiry()) {
            $expiryTime = strtotime($expiryDate);
            $todayTime = strtotime($this->getTimezoneDateTime());
            if ($todayTime > $expiryTime) {
                return true;
            }
        }
        return false;
    }

    /**
     * Retrieve the module config
     *
     * @param string $config
     * @return mixed
     */
    public function getConfig($config)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_TAG . '/' . $config, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param $field
     * @param null $storeId
     * @return bool
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
     * @return bool|string|string[]
     */
    public function getAllowedExtensions()
    {
        $extensions = $this->getConfigValue(self::XML_PATH_QUOTE_FILE_FORMATS);
        $extensions = $extensions ? str_replace(' ', '', $extensions) : $extensions;

        return $extensions;
    }

    /**
     * @return mixed
     */
    public function isEnabled()
    {
        return $this->getConfig('option/enabled_module');
    }

    /**
     * Get from store configuration maximum size for negotiable quote attachable files.
     *
     * @return int|null
     */
    public function getMaxFileSize()
    {
        return $this->getConfigValue(self::XML_PATH_QUOTE_MAXIMUM_FILE_SIZE);
    }

    /**
     * Get notification when admin update quote info
     * @param array|null
     * @return string
     */
    public function getUpdateQuoteNotifyText($variableData = null)
    {
        $notificationText = $this->getConfig('quote_process/quote_update_text');
        $notificationText = $notificationText?$this->processVariablesData($notificationText, $variableData): "";
        return $notificationText;
    }

    /**
     * Get notification when admin update quote info
     * @param array|null
     * @return string
     */
    public function getCloseQuoteNotifyText($variableData = null)
    {
        $notificationText = $this->getConfig('quote_process/quote_close_text');
        $notificationText = $notificationText?$this->processVariablesData($notificationText, $variableData): "";
        return $notificationText;
    }

    /**
     * Get notification when admin update quote info
     * @param array|null
     * @return string
     */
    public function getApproveQuoteNotifyText($variableData = null)
    {
        $notificationText = $this->getConfig('quote_process/quote_approve_text');
        $notificationText = $notificationText?$this->processVariablesData($notificationText, $variableData): "";
        return $notificationText;
    }

    /**
     * Get notification when admin update quote info
     * @param array|null
     * @return string
     */
    public function getExpiryQuoteNotifyText($variableData = null)
    {
        $notificationText = $this->getConfig('quote_process/quote_expiry_text');
        $notificationText = $notificationText?$this->processVariablesData($notificationText, $variableData): "";
        return $notificationText;
    }

    /**
     * Get notification when admin update quote info
     * @param array|null
     * @return string
     */
    public function getRenewQuoteNotifyText($variableData = null)
    {
        $notificationText = $this->getConfig('quote_process/quote_renew_text');
        $notificationText = $notificationText?$this->processVariablesData($notificationText, $variableData): "";
        return $notificationText;
    }

    /**
     * Process variable data for notifciation text variable param
     * variable params: 
     * {quote_link}
     * {contact_name}
     * {phone}
     * {email}
     * {product_id}
     * {quantity}
     * {price_per_product}
     * {status}
     * {date_need_quote}
     * {update_date}
     * {seller_name}
     * {coupon_code}
     * {admin_quantity}
     * {admin_price}
     * {store_id}
     * {store_currency_code}
     * {store_name}
     * @param string|null $notificationText
     * @param array|null $variableData
     * @return string|null
     */
    public function processVariablesData($notificationText, $variableData = null)
    {
        if ($variableData) {
            $limit = 30;
            $i = 0;
            foreach ($variableData as $key => $value) {
                if ($key && $i < $limit && (is_string($value) || is_numeric($value))) {
                    $notificationText = str_replace("{$key}", $value, $notificationText);
                    $i++;
                }
            }
        }
        return $notificationText;
    }

    /**
     * @param $data
     * @param $emailRecipient
     * @param $template
     * @return bool
     * @throws \Exception
     */
    public function sendEmail($data, $emailRecipient, $template)
    {
        try {
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $postObject = new DataObject();
            $postObject->setData($data);
            $senderData = $this->scopeConfig->getValue(self::XML_PATH_EMAIL_SENDER, $storeScope);
            if (!empty($senderData)) {
                $senderDataName = $this->scopeConfig->getValue('trans_email/ident_' . $senderData . '/name', $storeScope);
                $senderDataEmails = $this->scopeConfig->getValue('trans_email/ident_' . $senderData . '/email', $storeScope);
            } else {
                $senderDataName = $this->scopeConfig->getValue('trans_email/ident_general/name', $storeScope);
                $senderDataEmails = $this->scopeConfig->getValue('trans_email/ident_general/email', $storeScope);
            }

            $sender = [
                'name' => $this->_escaper->escapeHtml($senderDataName),
                'email' => $this->_escaper->escapeHtml($senderDataEmails),
            ];
            $transport = $this->_transportBuilder
                ->setTemplateIdentifier($template)// this code we have mentioned in the email_templates.xml
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars(['data' => $postObject])
                ->setFrom($sender)
                ->addTo($emailRecipient)
                ->getTransport();

            $transport->sendMessage();
            $this->inlineTranslation->resume();

            return true;
        } catch (\Exception $e) {
            throw new \Exception(__($e->getMessage()));
        }
    }

    /**
     * @param $data
     * @return bool
     * @throws \Exception
     */
    public function sendMailNotice($data)
    {
        try {
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $this->inlineTranslation->suspend();
            $postObject = new DataObject();
            $postObject->setData($data);
            $senderData = $this->scopeConfig->getValue(self::XML_PATH_EMAIL_SENDER, $storeScope);
            if (!empty($senderData)) {
                $senderDataName = $this->scopeConfig->getValue('trans_email/ident_' . $senderData . '/name', $storeScope);
                $senderDataEmails = $this->scopeConfig->getValue('trans_email/ident_' . $senderData . '/email', $storeScope);
            } else {
                $senderDataName = $this->scopeConfig->getValue('trans_email/ident_general/name', $storeScope);
                $senderDataEmails = $this->scopeConfig->getValue('trans_email/ident_general/email', $storeScope);
            }
            $sender = [
                'name' => $this->_escaper->escapeHtml($senderDataName),
                'email' => $this->_escaper->escapeHtml($senderDataEmails),
            ];
            if (!isset($data['sender_name'])) {
                $postObject->setData('sender_name', $sender['name']);
            }
            if (isset($data['receiver'])) {
                $postObject->setData('receiver_email', $data['receiver']);
            }

            $transport = $this->_transportBuilder
                ->setTemplateIdentifier($data['template'])
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars([
                    'data' => $postObject,
                ])
                ->setFrom($sender)
                ->addTo($data['receiver_email'])
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
            return true;
        } catch (\Exception $e) {
            throw new \Exception(__($e->getMessage()));
        }
    }

    /**
     * Get current store
     * @param string|int|null $storeId
     * @return Object
     */
    public function getCurrentStore($storeId = null)
    {
        if (!$this->_currentStore) {
            $this->_currentStore = $this->_storeManager->getStore($storeId);
        }
        return $this->_currentStore;
    }
    /**
     * Get website identifier
     * @param string|int|null $storeId
     * @return string|int|null
     */
    public function getWebsiteId($storeId = null)
    {
        return $this->getCurrentStore($storeId)->getWebsiteId();
    }

    /**
     * Get Store code
     * @param string|int|null $storeId
     * @return string
     */
    public function getStoreCode($storeId = null)
    {
        return $this->getCurrentStore($storeId)->getCode();
    }

    /**
     * Get Store name
     * @param string|int|null $storeId
     * @return string
     */
    public function getStoreName($storeId = null)
    {
        return $this->getCurrentStore($storeId)->getName();
    }

    /**
     * Get current url for store
     * @param string|int|null $storeId
     * @param bool|string $fromStore Include/Exclude from_store parameter from URL
     * @return string
     */
    public function getStoreUrl($storeId = null, $fromStore = true)
    {
        return $this->getCurrentStore($storeId)->getCurrentUrl($fromStore);
    }

    public function xss_clean_array($data_array){
        $result = [];
        if(is_array($data_array)){
            foreach($data_array as $key=>$val){
                $val = $this->xss_clean($val);
                $result[$key] = $val;
            }
        }
        return $result;
    }
    public function xss_clean($data)
    {
        if(!is_string($data))
            return $data;
        // Fix &entity\n;
        $data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
        $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
        $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
        $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

        // Remove any attribute starting with "on" or xmlns
        $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

        // Remove javascript: and vbscript: protocols
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

        // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

        // Remove namespaced elements (we do not need them)
        $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

        do
        {
            // Remove really unwanted tags
            $old_data = $data;
            $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
        }
        while ($old_data !== $data);

        // we are done...
        return $data;
    }
}
