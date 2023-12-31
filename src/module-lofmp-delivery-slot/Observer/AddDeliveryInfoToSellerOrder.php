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

use Lofmp\DeliverySlot\Model\ResourceModel\DeliverySlotsFactory;
use Lofmp\DeliverySlot\Model\DeliverySlotsFactory as DeliverSlotsModelFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollection;
use Psr\Log\LoggerInterface;
/**
 * Class AddDeliveryInfoToSellerOrder
 * @package Lofmp\DeliverySlot\Observer
 */
class AddDeliveryInfoToSellerOrder implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var CollectionFactory
     */
    protected $orderCollection;

    protected $deliverySlotsFactory;

    protected $deliverySlotsModelFactory;

    protected $logger;

    public function __construct(
        DeliverySlotsFactory $deliverySlotsFactory,
        DeliverSlotsModelFactory $deliverySlotsModelFactory,
        OrderCollection $orderCollection,
        LoggerInterface $logger
    ) {
        $this->deliverySlotsFactory = $deliverySlotsFactory;
        $this->deliverySlotsModelFactory = $deliverySlotsModelFactory;
        $this->orderCollection = $orderCollection;
        $this->logger = $logger;
    }
    /**
     * transfer the order comment from the quote object to the order object during the
     * sales_model_service_quote_submit_before event
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /* @var $order \Lof\MarketPlace\Model\Order */
        $order = $observer->getOrder();
        $sellerId = $order->getSellerId();
        $orderCollection = $this->orderCollection->create();
        $orderCollection->addFieldToFilter('main_table.entity_id', $order->getOrderId());
        if ($orderCollection->getSize()) {
            $firstOrderRecord = $orderCollection->getFirstItem();
            $deliverySlotResource = $this->deliverySlotsFactory->create();
            $deliverySlotModel = $this->deliverySlotsModelFactory->create();
            $deliverySlotResource->load($deliverySlotModel, $firstOrderRecord->getData('delivery_slot_id'));
            if ($deliverySlotModel->getSellerId() == $sellerId) {
                $deliverySlot = $deliverySlotModel->getData('start_time').'-'.$deliverySlotModel->getData('end_time');
                $order->setData('delivery_time_slot', $deliverySlot);
                $order->setData('delivery_date', $firstOrderRecord->getData('delivery_date'));
                $order->setData('delivery_comment', $firstOrderRecord->getData('delivery_comment'));
                $order->setData('delivery_slot_id', $firstOrderRecord->getData('delivery_slot_id'));
            }
        }
    }
}
