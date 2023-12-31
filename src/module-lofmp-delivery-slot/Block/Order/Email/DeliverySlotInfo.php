<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_DeliverySlot
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\DeliverySlot\Block\Order\Email;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\ResourceModel\OrderFactory;
use Magento\Sales\Model\OrderFactory as OrderModelFactory;
use Magento\Framework\App\RequestInterface;

/**
 * Class DeliverySlotInfo
 * @package Lofmp\DeliverySlot\Block\Order\Email
 */
class DeliverySlotInfo extends Template
{
    /**
     * DeliverySlotInfo constructor.
     * @param Context $context
     * @param OrderFactory $orderFactory
     * @param OrderModelFactory $orderModelFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        OrderFactory $orderFactory,
        OrderModelFactory $orderModelFactory,
        RequestInterface $request,
        array $data = []
    ) {
        $this->orderFactory = $orderFactory;
        $this->orderModelFactory = $orderModelFactory;
        $this->request = $request;
        parent::__construct($context, $data);
    }

    /**
     * @return bool|\Magento\Sales\Model\Order|void
     */
    public function getDeliveryInfo()
    {
        $orderId =  $this->getRequest()->getParam('order_id');
        if (!empty($orderId)) {
            $orderModel = $this->orderModelFactory->create();
            $orderResource = $this->orderFactory->create();
            $orderResource->load($orderModel, $orderId);
            return $orderModel;
        } else {
            return false;
        }
    }
}
