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
 * @package    Lofmp_SplitCart
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
namespace Lofmp\SplitCart\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class ConfigData extends AbstractHelper
{
    const PATH_GENERAL_SETTING = 'split_cart_config/general/';

    /**
     * @param $path
     * @param null $storeId
     * @return mixed
     */
    public function getConfigValue($path, $storeId = null)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param $field
     * @param null $storeId
     * @return mixed
     */
    public function getGeneralConfig($field, $storeId = null)
    {
        return $this->getConfigValue(self::PATH_GENERAL_SETTING . $field, $storeId);
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        $status = $this->getGeneralConfig('enable');
        return $status == 0 ? false : true;
    }

    /**
     * @return bool
     */
    public function isAllowAddSellerData()
    {
        $status = $this->getGeneralConfig('allow_add_seller');
        return $status == 0 ? false : true;
    }

    /**
     * @return array
     */
    public function getIgnoreRoutes()
    {
        $ignore_routes = $this->getGeneralConfig('ignore_routes');
        return explode("\n", $ignore_routes);
    }
}
