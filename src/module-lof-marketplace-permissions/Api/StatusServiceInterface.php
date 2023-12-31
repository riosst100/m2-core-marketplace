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

namespace Lof\MarketPermissions\Api;

use Magento\Store\Model\ScopeInterface;

/**
 * Service Status interface
 *
 * @api
 * @since 100.0.0
 */
interface StatusServiceInterface
{
    /**
     * Is module active.
     *
     * @param string $scopeType
     * @param string|null $scopeCode
     * @return bool
     */
    public function isActive($scopeType = ScopeInterface::SCOPE_WEBSITE, $scopeCode = null);

    /**
     * Is seller registration from the storefront allowed.
     *
     * @param string $scopeType
     * @param string|null $scopeCode
     * @return bool
     */
    public function isStorefrontRegistrationAllowed($scopeType = ScopeInterface::SCOPE_WEBSITE, $scopeCode = null);
}
