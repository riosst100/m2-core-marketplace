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

namespace Lof\MarketPermissions\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Email templates config.
 *
 * Provides access to seller email templates configuration.
 */
class EmailTemplate
{

    /**
     * @var string
     */
    private $sellerCustomerAssignUserTemplate = 'lof_marketpermissions/email/customer_seller_customer_assign_template';

    /**
     * @var string
     */
    private $inactivateCustomerTemplate = 'lof_marketpermissions/email/customer_account_locked_template';

    /**
     * @var string
     */
    private $activateCustomerTemplate = 'lof_marketpermissions/email/customer_account_activated_template';

    /**
     * @var string
     */
    private $customerInactivateSuperUserTemplate = 'lof_marketpermissions/email/customer_inactivate_super_user_template';

    /**
     * @var string
     */
    private $customerRemoveSuperUserTemplate = 'lof_marketpermissions/email/customer_remove_super_user_template';

    /**
     * @var string
     */
    private $salesRepresentativeUserTemplate = 'lof_marketpermissions/email/customer_sales_representative_template';


    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }


    /**
     * Get seller customer assign user template.
     *
     * @param string $scopeType
     * @param null|string $scopeCode
     * @return string
     */
    public function getSellerCustomerAssignUserTemplateId(
        $scopeType = ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ) {
        return $this->scopeConfig->getValue($this->sellerCustomerAssignUserTemplate, $scopeType, $scopeCode);
    }


    /**
     * Get customer inactivate superuser template.
     *
     * @param string $scopeType
     * @param null|string $scopeCode
     * @return string
     */
    public function getCustomerInactivateSuperUserTemplateId(
        $scopeType = ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ) {
        return $this->scopeConfig->getValue($this->customerInactivateSuperUserTemplate, $scopeType, $scopeCode);
    }

    /**
     * Get customer remove superuser template.
     *
     * @param string $scopeType
     * @param null|string $scopeCode
     * @return string
     */
    public function getCustomerRemoveSuperUserTemplateId(
        $scopeType = ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ) {
        return $this->scopeConfig->getValue($this->customerRemoveSuperUserTemplate, $scopeType, $scopeCode);
    }

    /**
     * Get sales representative user template.
     *
     * @param string $scopeType
     * @param null|string $scopeCode
     * @return string
     */
    public function getSalesRepresentativeUserTemplateId(
        $scopeType = ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ) {
        return $this->scopeConfig->getValue($this->salesRepresentativeUserTemplate, $scopeType, $scopeCode);
    }

    /**
     * Get activate customer template.
     *
     * @param string $scopeType
     * @param null|string $scopeCode
     * @return string
     */
    public function getActivateCustomerTemplateId(
        $scopeType = ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ) {
        return $this->scopeConfig->getValue($this->activateCustomerTemplate, $scopeType, $scopeCode);
    }

    /**
     * Get inactivate customer template.
     *
     * @param string $scopeType
     * @param null|string $scopeCode
     * @return string
     */
    public function getInactivateCustomerTemplateId(
        $scopeType = ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ) {
        return $this->scopeConfig->getValue($this->inactivateCustomerTemplate, $scopeType, $scopeCode);
    }
}
