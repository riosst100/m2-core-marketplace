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
namespace Lofmp\DeliverySlot\Block\Adminhtml\Order;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Sales\Api\ShipmentRepositoryInterfaceFactory;
use Magento\Sales\Model\ResourceModel\OrderFactory;
use Magento\Sales\Model\OrderFactory as OrderModelFactory;

/**
 * Class DeliverySlotInfo
 * @package Lofmp\DeliverySlot\Block\Adminhtml\Order
 */
class DeliverySlotInfo extends Template
{
    /**
     * DeliverySlotInfo constructor.
     * @param Context $context
     * @param array   $data
     */
    public function __construct(
        Context $context,
        ShipmentRepositoryInterfaceFactory $shipmentRepositoryInterfaceFactory,
        OrderFactory $orderFactory,
        OrderModelFactory $orderModelFactory,
        array $data = []
    ) {
        $this->shipmentRepositoryInterfaceFactory = $shipmentRepositoryInterfaceFactory;
        $this->orderFactory = $orderFactory;
        $this->orderModelFactory = $orderModelFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Sales\Model\Order|void
     */
    public function getOrder()
    {
        $shippmentId =  $this->getRequest()->getParam('shipment_id');
        $shippingRepository = $this->shipmentRepositoryInterfaceFactory->create();
        $shipmentDetails = $shippingRepository->get($shippmentId);
        $shipmentDetails->getOrderId();
        $orderModel = $this->orderModelFactory->create();
        $orderResource = $this->orderFactory->create();
        $orderResource->load($orderModel, $shipmentDetails->getOrderId());
        return $orderModel;
    }
}
