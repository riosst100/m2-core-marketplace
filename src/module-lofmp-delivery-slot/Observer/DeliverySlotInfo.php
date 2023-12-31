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

namespace Lofmp\DeliverySlot\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ObjectManager;
use Psr\Log\LoggerInterface;
use Magento\Sales\Model\ResourceModel\OrderFactory;
use Magento\Sales\Model\OrderFactory as OrderModelFactory;

/**
 * Class DeliverySlotInfo
 * @package Lofmp\DeliverySlot\Observer
 */
class DeliverySlotInfo implements ObserverInterface
{
    public function __construct(
        LoggerInterface $logger,
        OrderFactory $orderFactory,
        OrderModelFactory $orderModelFactory
    ) {
        $this->logger = $logger;
        $this->orderFactory = $orderFactory;
        $this->orderModelFactory = $orderModelFactory;
    }

    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->logger->info('order attributes');
        /** @var \Magento\Framework\App\Action\Action $controller */
        $transport = $observer->getTransport();
        $order = $transport['order'];
        $this->logger->info($order->getId());
        $orderResource = $this->orderFactory->create();
        $orderModel = $this->orderModelFactory->create();
        $orderResource->load($orderModel, $order->getId());
        $this->logger->info($orderModel->getData('delivery_date'));
        $transport['deliveryDate'] = $orderModel->getData('delivery_date');
        $transport['deliveryTimeSlot'] = $orderModel->getData('delivery_time_slot');
        $this->logger->info($transport['deliveryDate']);
    }
}
