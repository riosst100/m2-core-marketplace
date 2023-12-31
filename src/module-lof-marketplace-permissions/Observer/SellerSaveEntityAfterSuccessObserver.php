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

namespace Lof\MarketPermissions\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SellerSaveEntityAfterSuccessObserver implements ObserverInterface
{

    /**
     * @var \Lof\MarketPlace\Model\Seller
     */
    protected $sellerModel;

    /**
     * @var \Lof\MarketPermissions\Model\SellerContext
     */
    private $sellerContext;

    /**
     * @var \Lof\MarketPermissions\Model\SellerAdminPermission
     */
    private $sellerAdminPermission;

    /**
     * SellerRegisterSuccessObserver constructor.
     * @param \Lof\MarketPlace\Model\Seller $sellerModel
     * @param \Lof\MarketPermissions\Model\SellerContext $sellerContext
     * @param \Lof\MarketPermissions\Model\SellerAdminPermission $sellerAdminPermission
     */
    public function __construct(
        \Lof\MarketPlace\Model\Seller $sellerModel,
        \Lof\MarketPermissions\Model\SellerContext $sellerContext,
        \Lof\MarketPermissions\Model\SellerAdminPermission $sellerAdminPermission
    ) {
        $this->sellerModel = $sellerModel;
        $this->sellerContext = $sellerContext;
        $this->sellerAdminPermission = $sellerAdminPermission;
    }

    /**
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        if (!$this->sellerContext->isModuleActive()) {
            return;
        }
        $data = $observer->getData('data');

        $seller = $this->sellerModel->load($data['entity_id'], 'customer_id');

        $isCurrentUserSellerAdmin = $this->sellerAdminPermission->isCurrentUserSellerAdmin();
        if (!$isCurrentUserSellerAdmin) {
            $this->sellerContext->createSellerAdmin($seller->getCustomerId());
        }
    }
}
