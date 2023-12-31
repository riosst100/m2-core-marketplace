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

namespace Lof\MarketPlace\Block\Seller;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;

class Becomeseller extends Template
{
    /**
     * @var \Lof\MarketPlace\Model\Group
     */
    protected $_groupFactory;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Customer\Block\Account\Dashboard
     */
    protected $customerDashboard;

    /**
     * @var \Magento\Directory\Model\Config\Source\Country
     */
    protected $_country;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Block\Account\Dashboard $customerDashboard
     * @param \Magento\Directory\Model\Config\Source\Country $country
     * @param \Lof\MarketPlace\Model\Group $groupFactory
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param array $data
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Directory\Model\Config\Source\Country $country,
        \Magento\Customer\Block\Account\Dashboard $customerDashboard,
        \Lof\MarketPlace\Model\Group $groupFactory,
        \Lof\MarketPlace\Helper\Data $helper,
        array $data = []
    ) {
        $this->customerDashboard = $customerDashboard;
        $this->_groupFactory = $groupFactory;
        $this->_helper = $helper;
        $this->_country = $country;
        parent::__construct($context, $data);

        if ($this->_helper->getConfig('general_settings/customer_become_seller')) {
            $this->setTemplate('Lof_MarketPlace::seller/advanced_becomeseller.phtml');
        } else {
            $this->setTemplate('Lof_MarketPlace::seller/becomeseller.phtml');
        }
    }

    /**
     * get list countries
     * @return array|mixed|null
     */
    public function getCountries($default_country_code = "US")
    {
        $default_country_code = $default_country_code ? $default_country_code : "US";
        return $this->_country->toOptionArray(false, $default_country_code);
    }

    /**
     * @return \Magento\Customer\Block\Account\Dashboard
     */
    public function getCustomerInfo()
    {
        return $this->customerDashboard;
    }

    /**
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    public function getGroupCollection()
    {
        $groupCollection = $this->_groupFactory->getCollection();
        $availableGroup = $this->_helper->getConfig('group_seller/group_seller');
        if ($this->_helper->getConfig('group_seller/enable_group_seller') == '1' && $availableGroup) {
            $groupCollection->addFieldToFilter('group_id', ['in' => $availableGroup]);
        }
        return $groupCollection;
    }

    /**
     * @return Becomeseller
     */
    public function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Become a Seller'));
        return parent::_prepareLayout();
    }

    /**
     * @param string $urlKey
     * @return string
     * @throws NoSuchEntityException
     */
    public function getSellerUrl($urlKey)
    {
        $url = $this->_storeManager->getStore()->getBaseUrl();
        $helper = $this->_helper;
        $urlPrefixConfig = $helper->getConfig('general_settings/url_prefix');
        $urlPrefix = '';
        if ($urlPrefixConfig) {
            $urlPrefix = $urlPrefixConfig . '/';
        }
        $urlSuffix = $helper->getConfig('general_settings/url_suffix');
        return $url . $urlPrefix . $urlKey . $urlSuffix;
    }

    /**
     * isSeller function
     *
     * @return boolean
     */
    public function isSeller()
    {
        $seller = $this->_helper->getSellerId();
        if ($seller) {
            return true;
        }

        return false;
    }
}
