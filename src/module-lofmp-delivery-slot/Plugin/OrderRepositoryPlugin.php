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
namespace Lofmp\DeliverySlot\Plugin;

use Magento\Sales\Model\ResourceModel\OrderFactory;
use Magento\Sales\Model\OrderFactory as OrderModelFactory;

/**
 * Class OrderRepositoryPlugin
 * @package Lofmp\DeliverySlot\Plugin
 */
class OrderRepositoryPlugin
{
    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * @var OrderModelFactory
     */
    protected $orderModelFactory;

    /**
     * OrderRepositoryPlugin constructor.
     *
     * @param OrderFactory $orderFactory
     * @param OrderModelFactory $orderModelFactory
     */
    public function __construct(
        OrderFactory $orderFactory,
        OrderModelFactory $orderModelFactory
    ) {
        $this->orderFactory = $orderFactory;
        $this->orderModelFactory = $orderModelFactory;
    }

    /**
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function afterGet(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Api\Data\OrderInterface $order
    ) {
        $orderResource = $this->orderFactory->create();
        $orderModel = $this->orderModelFactory->create();
        $orderResource->load($orderModel, $order->getEntityId());
        $extensionAttributes = $order->getExtensionAttributes();
        $extensionAttributes->setDeliveryDate(
            $orderModel->getData('delivery_date')
        );
        $extensionAttributes->setDeliverySlot(
            $orderModel->getData('delivery_time_slot')
        );
        return $order;
    }
}
