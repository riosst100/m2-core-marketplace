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
 * @package    Lofmp_SplitOrder
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SplitOrder\Helper;

use Lof\MarketPlace\Helper\DataRule;
use Lof\MarketPlace\Helper\Uploadimage;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        $this->_moduleManager = $context->getModuleManager();
    }
    /**
     * Check if module is active.
     *
     * @param int $storeId
     * @return bool
     */
    public function isActive($storeId = null)
    {
        return (bool)$this->scopeConfig->isSetFlag(
            'lofmp_split_order/module/enabled',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param $module
     * @return bool
     */
    public function isEnableModule($module)
    {
        return $this->_moduleManager->isEnabled($module);
    }

    /**
     * Get attributes to split.
     *
     * @return string
     */
    public function getAttributes()
    {
        return 'seller_id';
    }

    /**
     * Check if should split delivery.
     *
     * @param string $storeId
     * @return bool
     */
    public function isShippingSplit($storeId = null)
    {
        return (bool)$this->scopeConfig->isSetFlag(
            'lofmp_split_order/options/shipping',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get which kind of attribute related with qty should be load.
     *
     * @param int $storeId
     * @return string
     */
    public function getQtyType($storeId = null)
    {
        return $this->scopeConfig->getValue(
            'lofmp_split_order/options/attribute_qty',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * If should apply out of stock if inventory is empty.
     *
     * @param int $storeId
     * @return string
     */
    public function getBackorder($storeId = null)
    {
        return (bool)$this->scopeConfig->isSetFlag(
            'lofmp_split_order/options/qty_backorder',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
