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
 * @package    Lof_PreOrder
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\PreOrder\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;

class AfterSaveProduct implements ObserverInterface
{
    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var \Lof\PreOrder\Helper\Data
     */
    protected $_preorderHelper;

    /**
     * @param RequestInterface $request
     * @param \Lof\PreOrder\Helper\Data $preorderHelper
     */
    public function __construct(
        RequestInterface $request,
        \Lof\PreOrder\Helper\Data $preorderHelper
    ) {
        $this->_request = $request;
        $this->_preorderHelper = $preorderHelper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $helper = $this->_preorderHelper;

        if ($helper->getAutoEmail()) {
            $isInStock = 0;
            $data = $this->_request->getParams();
            if (isset($data['product']['quantity_and_stock_status'])) {
                $stockData = $data['product']['quantity_and_stock_status'];
                if (array_key_exists('is_in_stock', $stockData)) {
                    $isInStock = $stockData['is_in_stock'];
                }
            }
            if ($isInStock == 1) {
                if (array_key_exists('id', $data)) {
                    $productId = $data['id'];
                    $productName = $data['product']['name'];
                    $emailIds = $helper->getCustomerEmailIds($productId);
                    $helper->sendNotifyEmail($emailIds, $productName);
                }
            }
        }
    }
}
