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
namespace Lofmp\DeliverySlot\Block\Marketplace\Order;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Api\ShipmentRepositoryInterfaceFactory;
use Magento\Sales\Model\ResourceModel\OrderFactory;
use Magento\Sales\Model\OrderFactory as OrderModelFactory;

/**
 * Class DeliverySlotInfo
 * @package Lofmp\DeliverySlot\Block\Marketplace\Order
 */
class DeliverySlotInfo extends Template
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

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
        $this->request = $context->getRequest();
        parent::__construct($context, $data);
    }

    /**
     * @return mixed|string
     */
    public function getShipmentId()
    {
        $shipmentId = $this->getRequest()->getParam("shipment_id");
        if (!$shipmentId) {
            $path = trim($this->request->getPathInfo(), '/');
            $params = explode('/', $path);
            $shipmentId = end($params);
        }
        return (int)$shipmentId;
    }

    /**
     * @return \Magento\Sales\Model\Order|void
     */
    public function getOrder()
    {
        $shippmentId =  $this->getShipmentId();
        $shippingRepository = $this->shipmentRepositoryInterfaceFactory->create();
        $shipmentDetails = $shippingRepository->get($shippmentId);
        $shipmentDetails->getOrderId();
        $orderModel = $this->orderModelFactory->create();
        $orderResource = $this->orderFactory->create();
        $orderResource->load($orderModel, $shipmentDetails->getOrderId());
        return $orderModel;
    }
}
