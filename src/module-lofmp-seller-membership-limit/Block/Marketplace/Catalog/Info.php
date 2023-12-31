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

namespace Lofmp\SellerMembershipLimit\Block\Marketplace\Catalog;

class Info extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Lofmp\SellerMembership\Model\Membership
     */
    protected $membership;

    /**
     * @var \Lof\MarketPlace\Model\SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * Info constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Lofmp\SellerMembership\Model\Membership $membership,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        $this->membership = $membership;
        $this->sellerFactory = $sellerFactory;
        $this->session = $customerSession;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->canDisplay()) {
            return '';
        }

        return parent::_toHtml();
    }

    /**
     * @return bool
     */
    public function canDisplay()
    {
        $customerSession = $this->session;
        if (!$customerSession->isLoggedIn()) {
            return false;
        }

        $customerId = $customerSession->getId();
        $seller = $this->sellerFactory->create()->load($customerId, 'customer_id');

        if (!$seller || count($seller->getData()) == 0) {
            return false;
        }

        if ($seller->getStatus() != 1) {
            return false;
        }

        $sellerId = $seller->getData('seller_id');
        if (!$sellerId) {
            return false;
        }

        $membership = $this->membership->getCollection()
            ->addFieldToFilter('seller_id', $sellerId)
            ->getFirstItem();

        if (!$membership || count($membership->getData()) == 0) {
            return false;
        }

        $limitProductDuration = $membership->getData('limit_product_duration');

        if ($limitProductDuration != 0) {
            return false;
        }

        return true;
    }
}
