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

namespace Lofmp\MultiShipping\ConfigProvider;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class CheckoutConfigProvider implements ConfigProviderInterface
{
    const CONFIG_PATH_MULTI_SHIPPING_CARRIER_TITLE = 'lofmp_multishipping/general/carrier_title';
    const CONFIG_PATH_MULTI_SHIPPING_CARRIER_HEADING = 'lofmp_multishipping/general/carriers_heading';
    const CONFIG_PATH_MULTI_SHIPPING_METHOD_TITLE = 'lofmp_multishipping/general/method_title';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Lofmp\MultiShipping\Helper\Data
     */
    protected $_config;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Lofmp\MultiShipping\Helper\Data $config
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        \Lofmp\MultiShipping\Helper\Data $config
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->_config = $config;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        if (!$this->_config->isEnabled()) {
            return [];
        }

        return [
            'sellermultishipping' => [
                'carrier_title' => $this->scopeConfig->getValue(
                    self::CONFIG_PATH_MULTI_SHIPPING_CARRIER_TITLE,
                    ScopeInterface::SCOPE_STORE
                ) ?: '',
                'method_title' => $this->scopeConfig->getValue(
                    self::CONFIG_PATH_MULTI_SHIPPING_METHOD_TITLE,
                    ScopeInterface::SCOPE_STORE
                ) ?: '',
                'carriers_heading' => $this->scopeConfig->getValue(
                    self::CONFIG_PATH_MULTI_SHIPPING_CARRIER_HEADING,
                    ScopeInterface::SCOPE_STORE
                ) ?: '',
            ],
        ];
    }
}
