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

namespace Lof\MarketPlace\Observer;

use Magento\Framework\Event\ObserverInterface;

class SaveSellerExtraInfo implements ObserverInterface
{
    /**
     * @var \Lof\MarketPlace\Helper\Report
     */
    protected $_helperReport;

    /**
     * SaveSellerExtraInfo constructor.
     * @param \Lof\MarketPlace\Helper\Report $helperReport
     */
    public function __construct(
        \Lof\MarketPlace\Helper\Report $helperReport
    ) {
        $this->_helperReport = $helperReport;
    }

    /**
     * Checking whether the using static urls in WYSIWYG allowed event
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $seller = $observer->getEvent()->getData('seller');
        $sellerId = $seller->getData('seller_id');
        if ($sellerId && $sellerId > 0) {
            $sellerProductCount = $this->_helperReport->getTotalProduct($sellerId) ?: 0;
            $seller->setProductCount($sellerProductCount)->save();
        }

        return $this;
    }
}
