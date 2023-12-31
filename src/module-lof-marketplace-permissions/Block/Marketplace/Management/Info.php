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

namespace Lof\MarketPermissions\Block\Marketplace\Management;

use Magento\Framework\View\Element\Template\Context;
use Magento\Authorization\Model\UserContextInterface;
use Lof\MarketPermissions\Api\SellerManagementInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * seller management info.
 */
class Info extends \Magento\Framework\View\Element\Template
{
    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @var SellerManagementInterface
     */
    private $sellerManagement;

    /**
     * @var string
     */
    private $xmlPathAllowRegister = 'lof_seller/general/allow_seller_registration';

    /**
     * @var \Lof\MarketPermissions\Api\AuthorizationInterface
     */
    private $authorization;

    /**
     * @param Context $context
     * @param UserContextInterface $userContext
     * @param SellerManagementInterface $sellerManagement
     * @param \Lof\MarketPermissions\Api\AuthorizationInterface $authorization
     * @param array $data [optional]
     */
    public function __construct(
        Context $context,
        UserContextInterface $userContext,
        SellerManagementInterface $sellerManagement,
        \Lof\MarketPermissions\Api\AuthorizationInterface $authorization,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->userContext = $userContext;
        $this->sellerManagement = $sellerManagement;
        $this->authorization = $authorization;
    }

    /**
     * Checks if user edit is allowed.
     *
     * @return bool
     */
    public function isUserEditAllowed()
    {
        return $this->authorization->isAllowed('Lof_MarketPermissions::users_edit');
    }

    /**
     * Checks if roles edit is allowed.
     *
     * @return bool
     */
    public function isRoleEditAllowed()
    {
        return $this->authorization->isAllowed('Lof_MarketPermissions::roles_edit');
    }

    /**
     * Has current customer seller.
     *
     * @return bool
     */
    public function hasCustomerSeller()
    {
        $hasSeller = false;
        $customerId = $this->userContext->getUserId();
        if ($customerId) {
            $seller = $this->sellerManagement->getByCustomerId($customerId);
            if ($seller) {
                $hasSeller = true;
            }
        }

        return $hasSeller;
    }

    /**
     * Get create new seller url.
     *
     * @return string
     */
    public function getCreateSellerAccountUrl()
    {
        return $this->getUrl('lofmarketplace/seller/create/');
    }

    /**
     * Is seller registration allowed.
     *
     * @param string $scopeType
     * @param string|null $scopeCode
     * @return bool
     */
    public function isAllowedRegister($scopeType = ScopeInterface::SCOPE_WEBSITE, $scopeCode = null)
    {
        return $this->_scopeConfig->isSetFlag($this->xmlPathAllowRegister, $scopeType, $scopeCode);
    }
    /**
     * Prepare layout for change buyer
     *
     * @return Object
     */
    public function _prepareLayout() {
        $this->pageConfig->getTitle ()->set(__('Manager Seller Structure'));
        return parent::_prepareLayout ();
    }
}
