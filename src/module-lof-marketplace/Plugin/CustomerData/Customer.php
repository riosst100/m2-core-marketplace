<?php

/**
 * Lof
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Lof.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Lof
 * @package     Lof_MarketPlace
 * @copyright   Copyright (c) 2021 Lof (https://landofcoder.com/)
 * @license     https://landofcoder.com/LICENSE.txt
 */

namespace Lof\MarketPlace\Plugin\CustomerData;

use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Customer\Helper\View;
use Lof\MarketPlace\Model\SellerFactory;

/**
 * Class Customer
 * @package Lof\MarketPlace\Plugin\CustomerData
 */
class Customer
{
    /**
     * @var CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var View
     */
    protected $customerViewHelper;

    /**
     * @var SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $helperData;

    /**
     * @param CurrentCustomer $currentCustomer
     * @param View $customerViewHelper
     * @param SellerFactory $sellerFactory
     * @param \Lof\MarketPlace\Helper\Data $helperData
     */
    public function __construct(
        CurrentCustomer $currentCustomer,
        View $customerViewHelper,
        SellerFactory $sellerFactory,
        \Lof\MarketPlace\Helper\Data $helperData
    ) {
        $this->currentCustomer = $currentCustomer;
        $this->customerViewHelper = $customerViewHelper;
        $this->sellerFactory = $sellerFactory;
        $this->helperData = $helperData;
    }

    /**
     * {@inheritdoc}
     */
    public function afterGetSectionData(
        \Magento\Customer\CustomerData\Customer $subject,
        $result = []
    )
    {
        if ($this->currentCustomer->getCustomerId() && $this->helperData->getConfig('general_settings/enable')) {
            $customer = $this->currentCustomer->getCustomer();
            $seller = $this->sellerFactory->create()->load((int)$this->currentCustomer->getCustomerId(), 'customer_id');
            if ($seller && $seller->getId()) {
                $result['seller_id'] = $seller->getId();
                $result['seller_status'] = $seller->getStatus();
            }
        }
        return $result;
    }
}
