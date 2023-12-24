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
 * @package    Lof_SmtpEmail
 * @copyright  Copyright (c) 2016 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\SmtpEmail\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
     /**
     * @var null $_storeId
     */
    protected $_storeId = null;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $_storeManager;

    /**
     * @var \Lof\SmtpEmail\Model\Config\Source\Providers
     */
    protected $_providers;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\ObjectManagerInterface
     * @param \Lof\SmtpEmail\Model\Config\Source\Providers $providers
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Lof\SmtpEmail\Model\Config\Source\Providers $providers
    )
    {
        $this->_objectManager = $objectManager;
        $this->_storeManager = $storeManager;
        $this->_providers = $providers;
        parent::__construct($context);
    }

    /**
     * @param int|null $storeId
     * @return void
     */
    public function setStoreId($storeId = null)
    {
        $this->_storeId = $storeId;
    }

    /**
     * Return brand config value by key and store
     *
     * @param string $key
     * @param \Magento\Store\Model\Store|int|string $store
     * @return string|null
     */
    public function getConfig($key, $store = null)
    {
        $store = $this->_storeManager->getStore($store);
        //$websiteId = $store->getWebsiteId();
        $result = $this->scopeConfig->getValue(
            'lofsmtpemail/'.$key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store);
        return $result;
    }

    /**
     * Get IP
     *
     * @return string|mixed
     */
    public function getIp()
    {
        return $this->_remoteAddress->getRemoteAddress();
    }

    /**
     * Get system config password
     *
     * @param \Magento\Store\Model\ScopeInterface::SCOPE_STORE|null $store
     * @return mixed
     */
    public function getConfigPassword($store = null)
    {
        return $this->scopeConfig->getValue('lofsmtpemail/smtp_config/password', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * Get system config username
     *
     * @param \Magento\Store\Model\ScopeInterface::SCOPE_STORE|null $store
     * @return mixed
     */
    public function getConfigUsername($store = null)
    {
        return $this->scopeConfig->getValue('lofsmtpemail/smtp_config/username', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * Get system config password
     *
     * @param \Magento\Store\Model\ScopeInterface::SCOPE_STORE|null $store
     * @return mixed
     */
    public function getConfigAuth($store = null)
    {
        return $this->scopeConfig->getValue('lofsmtpemail/smtp_config/auth', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }
    /**
     * Get system config set return path
     *
     * @param \Magento\Store\Model\ScopeInterface::SCOPE_STORE|null $store
     * @return int|mixed
     */
    public function getConfigSetReturnPath($store = null)
    {
        return (int)$this->scopeConfig->getValue('lofsmtpemail/smtp_config/set_return_path', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * Get system config return path email
     *
     * @param \Magento\Store\Model\ScopeInterface::SCOPE_STORE|null $store
     * @return string
     */
    public function getConfigReturnPathEmail($store = null)
    {
       return $this->scopeConfig->getValue('lofsmtpemail/smtp_config/return_path_email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }
    /**
     * Get system config reply to
     *
     * @param \Magento\Store\Model\ScopeInterface::SCOPE_STORE|null $store
     * @return bool
     */
    public function getConfigSetReplyTo($store = null)
    {
        return $this->scopeConfig->getValue('lofsmtpemail/smtp_config/set_reply_to', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * Get system config ssl
     *
     * @param \Magento\Store\Model\ScopeInterface::SCOPE_STORE|null $store
     */
    public function getConfigSsl($store = null)
    {
        return $this->scopeConfig->getValue('lofsmtpemail/smtp_config/ssl', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }
     /**
     * Get system config from
     *
     * @param \Magento\Store\Model\ScopeInterface::SCOPE_STORE|null $store
     * @return string
     */
    public function getConfigSetFrom($store = null)
    {
        return $this->scopeConfig->getValue('lofsmtpemail/smtp_config/set_from', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }
    /**
     * Get system config password
     *
     * @param \Magento\Store\Model\ScopeInterface::SCOPE_STORE|null $store
     */
    public function getConfigSmtpHost($store = null)
    {
        return $this->scopeConfig->getValue('lofsmtpemail/smtp_config/smtphost', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * Get system config username
     *
     * @param \Magento\Store\Model\ScopeInterface::SCOPE_STORE|null $store
     * @return mixed
     */
    public function getConfigPort($store = null)
    {
        return $this->scopeConfig->getValue('lofsmtpemail/smtp_config/port', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @param \Magento\Store\Model\ScopeInterface::SCOPE_STORE|null $store
     * @return mixed
     */
    public function getProviderName($store = null)
    {
        $providerId = $this->scopeConfig->getValue('lofsmtpemail/smtp_config/provider', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
        $providerName = $this->_providers->getProviderName((int)$providerId);
        return $providerName;
    }

}
