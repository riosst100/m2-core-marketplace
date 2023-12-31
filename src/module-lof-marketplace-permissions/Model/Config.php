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
 * @package    Lof_MarketPermissions
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPermissions\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config.
 */
class Config implements \Lof\MarketPermissions\Api\StatusServiceInterface
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var string
     */
    private $xmlPathActive = 'lof_marketpermissions/general/enable';

    /**
     * @var string
     */
    private $xmlPathStorefrontRegistrationAllowed = 'lof_marketpermissions/general/allow_seller_registration';

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function isActive($scopeType = ScopeInterface::SCOPE_WEBSITE, $scopeCode = null)
    {
        return $this->scopeConfig->isSetFlag($this->xmlPathActive, $scopeType, $scopeCode);
    }

    /**
     * {@inheritdoc}
     */
    public function isStorefrontRegistrationAllowed($scopeType = ScopeInterface::SCOPE_WEBSITE, $scopeCode = null)
    {
        return $this->scopeConfig->isSetFlag($this->xmlPathStorefrontRegistrationAllowed, $scopeType, $scopeCode);
    }
}
