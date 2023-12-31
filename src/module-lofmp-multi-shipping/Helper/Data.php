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
 * @package    Lofmp_MultiShipping
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\MultiShipping\Helper;

use Lof\MarketPlace\Model\Config\Shipping\Methods\AbstractModel;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Lof\MarketPlace\Model\ConfigFactory
     */
    protected $_config;

    /**
     * @var \Lof\MarketPlace\Model\Source\Shipping\Methods
     */
    protected $_sellerShippingMethods;

    /**
     * @var \Lof\MarketPlace\Model\Config\Shipping\Address
     */
    protected $_shippingAddress;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Lof\MarketPlace\Model\Source\Shipping\Methods $sellerShippingMethods
     * @param \Lof\MarketPlace\Helper\Data $marketplaceData
     * @param \Lof\MarketPlace\Model\ConfigFactory $marketplaceConfig
     * @param \Lof\MarketPlace\Model\Config\Shipping\Address $shippingAddress
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Lof\MarketPlace\Model\Source\Shipping\Methods $sellerShippingMethods,
        \Lof\MarketPlace\Helper\Data $marketplaceData,
        \Lof\MarketPlace\Model\ConfigFactory $marketplaceConfig,
        \Lof\MarketPlace\Model\Config\Shipping\Address $shippingAddress
    ) {
        parent::__construct($context);
        $this->_objectManager = $objectManager;
        $this->_sellerShippingMethods = $sellerShippingMethods;
        $this->_helper = $marketplaceData;
        $this->_config = $marketplaceConfig;
        $this->_shippingAddress = $shippingAddress;
    }

    /**
     * @param int $storeId
     * @return string|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isEnabled($storeId = 0)
    {
        if ($storeId == 0) {
            $storeId = $this->_helper->getStore()->getId();
        }
        $isActiveOld = $this->getConfig('general/activation', $storeId);
        $isActiveNew = $this->getConfig('general/activation_multishipping', $storeId);
        return $isActiveOld || $isActiveNew;
    }

    /**
     * @param int $storeId
     * @return string|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isUseAdminShipping($storeId = 0)
    {
        if ($storeId == 0) {
            $storeId = $this->_helper->getStore()->getId();
        }
        return $this->getConfig('general/use_admin_shipping', $storeId);
    }

    /**
     * Return seller config value by key and store
     *
     * @param string $key
     * @param \Magento\Store\Model\Store|int|string $store
     * @return string|null
     */
    public function getConfig($key, $store = null)
    {
        $result = $this->scopeConfig->getValue(
            'lofmp_multishipping/' . $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        return $result;
    }

    /**
     * @param string $key
     * @param int $sellerId
     * @return bool|mixed|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getConfigValue($key = '', $sellerId = 0)
    {
        $value = false;
        if (strlen($key) > 0 && $sellerId) {
            $key_tmp = $this->_helper->getTableKey('key');
            $seller_id_tmp = $this->_helper->getTableKey('seller_id');
            $config = $this->_config->create()
                ->loadByField([$key_tmp, $seller_id_tmp], [$key, (int)$sellerId]);
            if ($config && $config->getSettingId()) {
                $value = $config->getValue();
            }
        }
        return $value;
    }

    /**
     * @param int $sellerId
     * @return array
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @phpcs:disable Squiz.PHP.CommentedOutCode.Found
     */
    public function getSellerMethods($sellerId = 0)
    {
        $methods = $this->_sellerShippingMethods->getMethods();
        $sellerMethods = [];
        if (count($methods) > 0) {
            $sellerShippingConfig = $this->getShippingConfig($sellerId);

            foreach ($methods as $code => $method) {
                $object = \Magento\Framework\App\ObjectManager::getInstance();
                $model = $object->get($method['model']);
                $fields = $model->getFields();
                if (count($fields) > 0) {
                    foreach ($fields as $id => $field) {
                        $key = strtolower(AbstractModel::SHIPPING_SECTION . '/' . $code . '/' . $id);
                        if (isset($sellerShippingConfig[$key])) {
                            $sellerMethods[$code][$id] = $sellerShippingConfig[$key]['value'];
                        }
                    }
                }
            }
        }
        return $sellerMethods;
    }

    /**
     * @param int $sellerId
     * @return array
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @phpcs:disable Squiz.PHP.CommentedOutCode.Found
     */
    public function getSellerAddress($sellerId = 0)
    {
        $sellerAddress = [];
        if ($sellerId) {
            $model = $this->_shippingAddress;
            $sellerShippingConfig = $this->getShippingConfig($sellerId);
            $seller = $this->_helper->getSellerById($sellerId);
            if ($seller && $seller->getId()) {
                $fields = $model->setSeller($seller)->getFields();
                if (count($fields) > 0) {
                    foreach ($fields as $id => $field) {
                        $key = strtolower(AbstractModel::SHIPPING_SECTION . '/address/' . $id);
                        if (isset($sellerShippingConfig[$key]) && strlen($sellerShippingConfig[$key]['value']) > 0) {
                            $sellerAddress[$id] = $sellerShippingConfig[$key]['value'];
                        }
                    }
                }
                if (!isset($sellerAddress['country_id'])) {
                    $sellerAddress['country_id'] = $seller->getCountry();
                }
                if (!isset($sellerAddress['city'])) {
                    $sellerAddress['city'] = $seller->getCity();
                }
                if (!isset($sellerAddress['postcode'])) {
                    $sellerAddress['postcode'] = $seller->getPostcode();
                }
                if (!isset($sellerAddress['region_id'])) {
                    $sellerAddress['region_id'] = $seller->getRegionId();
                }
                if (!isset($sellerAddress['region'])) {
                    $sellerAddress['region'] = $seller->getRegion();
                }
            }
        }
        return $sellerAddress;
    }

    /**
     * @param int $sellerId
     * @return array
     */
    public function getShippingConfig($sellerId = 0)
    {
        $values = [];
        if ($sellerId) {
            $group = $this->_helper->getTableKey('group');
            $sellerIdKey = $this->_helper->getTableKey('seller_id');
            $config = $this->_config->create()->getCollection()
                ->addFieldToFilter($group, ['eq' => AbstractModel::SHIPPING_SECTION])
                ->addFieldToFilter($sellerIdKey, ['eq' => $sellerId]);
            if ($config && count($config->getData()) > 0) {
                foreach ($config->getData() as $value) {
                    $values[$value['key']] = $value;
                }
            }
        }
        return $values;
    }

    /**
     * @param array $sellerAddress
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function validateAddress($sellerAddress = [])
    {
        $flag = true;
        if (!isset($sellerAddress['country_id']) || !$sellerAddress['country_id']) {
            return false;
        }
        if (!isset($sellerAddress['city']) || !$sellerAddress['city']) {
            return false;
        }
        if (!isset($sellerAddress['postcode']) || !$sellerAddress['postcode']) {
            return false;
        }
        if ((!isset($sellerAddress['region_id']) || !$sellerAddress['region_id'])
            && (!isset($sellerAddress['region']) || !$sellerAddress['region'])
        ) {
            return false;
        }
        return $flag;
    }

    /**
     * @param $activeMethods
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function validateSpecificMethods($activeMethods)
    {
        if (count($activeMethods) > 0) {
            $methods = $this->_sellerShippingMethods->getMethods();
            foreach ($activeMethods as $method => $methoddata) {
                if (isset($methods[$method]['model'])) {
                    $model = $this->_objectManager->get($methods[$method]['model'])
                        ->validateSpecificMethod($activeMethods[$method]);
                    if (!$model) {
                        return false;
                    }
                }
            }
            return true;
        } else {
            return false;
        }
    }
}
