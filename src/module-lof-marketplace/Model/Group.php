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
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Model;

use Lof\MarketPlace\Api\Data\GroupInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Group extends \Magento\Framework\Model\AbstractModel implements GroupInterface
{
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * @var string
     */
    protected $_eventPrefix = 'lof_marketplace_group';

    /**
     * Name of the event object
     *
     * @var string
     */
    protected $_eventObject = 'group';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Group constructor.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\UrlInterface $url
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param ResourceModel\Group|null $resource
     * @param ResourceModel\Group\Collection|null $resourceCollection
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Lof\MarketPlace\Model\ResourceModel\Group $resource = null,
        \Lof\MarketPlace\Model\ResourceModel\Group\Collection $resourceCollection = null,
        array $data = []
    ) {
        $this->_storeManager = $storeManager;
        $this->_url = $url;
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Lof\MarketPlace\Model\ResourceModel\Group::class);
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

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getUrl()
    {
        $url = $this->_storeManager->getStore()->getBaseUrl();
        $store = $this->_storeManager->getStore();
        $url_prefix = $this->_scopeConfig->getValue(
            'lofmarketplace/general_settings/url_prefix',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        $url_suffix = $this->_scopeConfig->getValue(
            'lofmarketplace/general_settings/url_suffix',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );

        $urlPrefix = '';
        if ($url_prefix) {
            $urlPrefix = $url_prefix . '/';
        }

        return $url . $urlPrefix . $this->getUrlKey() . $url_suffix;
    }

    /**
     * @inheritDoc
     */
    public function getGroupId()
    {
        return $this->getData(self::GROUP_ID);
    }

    /**
     * @inheritDoc
     */
    public function setGroupId($group_id)
    {
        $this->setId($group_id);
        return $this->setData(self::GROUP_ID, $group_id);
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @inheritDoc
     */
    public function getUrlKey()
    {
        return $this->getData(self::URL_KEY);
    }

    /**
     * @inheritDoc
     */
    public function setUrlKey($url_key)
    {
        return $this->setData(self::URL_KEY, $url_key);
    }

    /**
     * @inheritDoc
     */
    public function getPosition()
    {
        return $this->getData(self::POSITION);
    }

    /**
     * @inheritDoc
     */
    public function setPosition($position)
    {
        return $this->setData(self::POSITION, $position);
    }

    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @inheritDoc
     */
    public function getShowInSidebar()
    {
        return $this->getData(self::SHOW_IN_SIDEBAR);
    }

    /**
     * @inheritDoc
     */
    public function setShowInSidebar($show_in_sidebar)
    {
        return $this->setData(self::SHOW_IN_SIDEBAR, $show_in_sidebar);
    }

    /**
     * @inheritDoc
     */
    public function getLimitProduct()
    {
        return $this->getData(self::LIMIT_PRODUCT);
    }

    /**
     * @inheritDoc
     */
    public function setLimitProduct($limit_product)
    {
        return $this->setData(self::LIMIT_PRODUCT, $limit_product);
    }

    /**
     * @inheritDoc
     */
    public function getCanAddProduct()
    {
        return $this->getData(self::CAN_ADD_PRODUCT);
    }

    /**
     * @inheritDoc
     */
    public function setCanAddProduct($can_add_product)
    {
        return $this->setData(self::CAN_ADD_PRODUCT, $can_add_product);
    }

    /**
     * @inheritDoc
     */
    public function getCanUseShiping()
    {
        return $this->getData(self::CAN_USE_SHIPPING);
    }

    /**
     * @inheritDoc
     */
    public function setCanUseShiping($can_use_shipping)
    {
        return $this->setData(self::CAN_USE_SHIPPING, $can_use_shipping);
    }

    /**
     * @inheritDoc
     */
    public function getCanUseMessage()
    {
        return $this->getData(self::CAN_USE_MESSAGE);
    }

    /**
     * @inheritDoc
     */
    public function setCanUseMessage($can_use_message)
    {
        return $this->setData(self::CAN_USE_MESSAGE, $can_use_message);
    }

    /**
     * @inheritDoc
     */
    public function getCanUseVacation()
    {
        return $this->getData(self::CAN_USE_VACATION);
    }

    /**
     * @inheritDoc
     */
    public function setCanUseVacation($can_use_vacation)
    {
        return $this->setData(self::CAN_USE_VACATION, $can_use_vacation);
    }

    /**
     * @inheritDoc
     */
    public function getCanUseWithdrawal()
    {
        return $this->getData(self::CAN_USE_WITHDRAWAL);
    }

    /**
     * @inheritDoc
     */
    public function setCanUseWithdrawal($can_use_withdrawal)
    {
        return $this->setData(self::CAN_USE_WITHDRAWAL, $can_use_withdrawal);
    }
}
