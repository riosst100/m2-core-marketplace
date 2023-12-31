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

/**
 * Class AddDeliveryInfoToOrder
 * @package Lofmp\DeliverySlot\Observer
 */
class AddDeliveryInfoToOrder implements \Magento\Framework\Event\ObserverInterface
{

    public function __construct(
        DeliverySlotsFactory $deliverySlotsFactory,
        DeliverSlotsModelFactory $deliverySlotsModelFactory
    ) {
        $this->deliverySlotsFactory = $deliverySlotsFactory;
        $this->deliverySlotsModelFactory = $deliverySlotsModelFactory;
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
        /* @var $order \Magento\Sales\Model\Order */
        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();
        $deliverySlotResource = $this->deliverySlotsFactory->create();
        $deliverySlotModel = $this->deliverySlotsModelFactory->create();
        $deliverySlotResource->load($deliverySlotModel, $quote->getData('delivery_slot_id'));
        $deliverySlot = $deliverySlotModel->getData('start_time').'-'.$deliverySlotModel->getData('end_time');
        /** @var $quote \Magento\Quote\Model\Quote $quote */
        $order->setData('delivery_time_slot', $deliverySlot);
        $order->setData('delivery_date', $quote->getData('delivery_date'));
        $order->setData('delivery_comment', $quote->getData('delivery_comment'));
        $order->setData('delivery_slot_id', $quote->getData('delivery_slot_id'));
    }
}
