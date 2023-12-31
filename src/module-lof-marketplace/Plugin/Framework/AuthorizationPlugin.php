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

namespace Lof\MarketPlace\Plugin\Framework;

use Lof\MarketPlace\Model\SellerFactory;
use Magento\Customer\Model\Session;

class AuthorizationPlugin
{
    /**
     * @var SellerFactory
     */
    private $sellerFactory;

    /**
     * @var Session
     */
    private $session;

    /**
     * @param SellerFactory $sellerFactory
     * @param Session $session
     */
    public function __construct(
        SellerFactory $sellerFactory,
        Session $session
    ) {
        $this->sellerFactory = $sellerFactory;
        $this->session = $session;
    }

    /**
     * @param \Magento\Framework\Authorization $subject
     * @param $result
     * @param $resource
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterIsAllowed(
        \Magento\Framework\Authorization $subject,
        $result,
        $resource
    ) {
        $customerSession = $this->session;
        $customerId = $customerSession->getId();
        $seller = $this->sellerFactory->create()->load($customerId, 'customer_id');
        $status = $seller->getStatus();

        if ($customerSession->isLoggedIn() && $status == 1) {
            if ($resource == 'Magento_Catalog::edit_product_design') {
                return true;
            }
        }

        return $result;
    }
}
