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
 * @package    Lofmp_FlatRateShipping
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\FlatRateShipping\Helper;

use Lof\MarketPlace\Model\SellerFactory;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    protected $_customerSession;

    /**
     * @var SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $marketplaceData;

    /**
     * @var \Lof\MarketPlace\Model\ConfigFactory
     */
    protected $marketplaceConfig;

    /**
     * @var array
     */
    protected $_seller = [];

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Customer\Model\SessionFactory $customerSession
     * @param SellerFactory $sellerFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\SessionFactory $customerSession,
        SellerFactory $sellerFactory,
        \Lof\MarketPlace\Helper\Data $marketplaceData,
        \Lof\MarketPlace\Model\ConfigFactory $marketplaceConfig
    ) {
        parent::__construct($context);
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_customerSession = $customerSession;
        $this->sellerFactory = $sellerFactory;
        $this->marketplaceData = $marketplaceData;
        $this->marketplaceConfig = $marketplaceConfig;
    }

    /**
     * @return \Lof\MarketPlace\Model\Seller|mixed|null
     */
    public function getSeller()
    {
        $customerId = $this->_customerSession->create()->getCustomerId();
        if ($customerId && !isset($this->_seller[$customerId])) {
            $this->_seller[$customerId] = $this->sellerFactory->create()->load($customerId, 'customer_id');
            if ($this->_seller[$customerId]->getId() && $this->_seller[$customerId]->getStatus() == 0) { //need approval
                $this->_seller[$customerId] = null;
            }
        }
        return $this->_seller[$customerId];
    }

    /**
     * @return array|mixed|null
     */
    public function getSellerByCustomer()
    {
        $seller = $this->getSeller();
        return $seller ? $seller->getData() : null;
    }

    /**
     * get shipping is enabled or not for system config.
     */
    public function getFlatRateShippingEnabled()
    {
        return $this->_scopeConfig->getValue(
            'carriers/lofmpflatrateshipping/active',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * get multi shipping is enabled or not for system config.
     */
    public function getMpmultishippingEnabled()
    {
        return $this->_scopeConfig->getValue(
            'carriers/mp_multi_shipping/active',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * get table rate shipping title from system config.
     */
    public function getFlatRateShippingTitle()
    {
        return $this->_scopeConfig->getValue(
            'carriers/lofmpflatrateshipping/title',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * get table rate shipping name from system config.
     */
    public function getFlatRateShippingName()
    {
        return $this->_scopeConfig->getValue(
            'carriers/lofmpflatrateshipping/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * get table rate shipping allow admin settings from system config.
     */
    public function getFlatRateShippingAllowadmin()
    {
        return $this->_scopeConfig->getValue(
            'carriers/lofmpflatrateshipping/allowadmin',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return int|mixed
     */
    public function getPartnerId()
    {
        $seller = $this->getSeller();
        return $seller ? $seller->getId() : 0;
    }

    /**
     * @param $sellerId
     * @return bool
     */
    public function isSellerEnabled($sellerId)
    {
        try {
            $key = 'shipping/lofmpflatrateshipping/active';
            $keyTable = $this->marketplaceData->getTableKey('key');
            $sellerKeyTable = $this->marketplaceData->getTableKey('seller_id');
            $config = $this->marketplaceConfig->create()
                ->loadByField([$keyTable, $sellerKeyTable], [$key, $sellerId])->getValue();
            return $config == null ? true : !!$config;
        } catch (\Exception $e) {
            return true;
        }
    }
}
