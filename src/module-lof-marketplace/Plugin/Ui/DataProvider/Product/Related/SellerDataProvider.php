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

namespace Lof\MarketPlace\Plugin\Ui\DataProvider\Product\Related;

use Lof\MarketPlace\Model\SellerFactory;
use Magento\Customer\Model\Session;

class SellerDataProvider
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
     * @param \Magento\Catalog\Ui\DataProvider\Product\Related\AbstractDataProvider $subject
     * @param $result
     */
    public function afterGetCollection(
        \Magento\Catalog\Ui\DataProvider\Product\Related\AbstractDataProvider $subject,
        $result
    ) {
        $customerSession = $this->session;
        $customerId = $customerSession->getId();
        $seller = $this->sellerFactory->create()->load($customerId, 'customer_id');
        $sellerId = (int)$seller->getId();
        $result->getSelect()->where('seller_id = ?', $sellerId);

        return $result;
    }
}
