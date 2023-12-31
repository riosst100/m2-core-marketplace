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

use Magento\Sales\Model\OrderFactory as OrderModel;
use Magento\Sales\Model\ResourceModel\OrderFactory as OrderResourceModel;
use Lofmp\DeliverySlot\Model\ResourceModel\DeliverySlotsFactory;
use Lofmp\DeliverySlot\Model\DeliverySlotsFactory as DeliverSlotsModelFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Lofmp\DeliverySlot\Model\ResourceModel\DeliverySlots\CollectionFactory as DeliverySlotsCollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Quote\Model\QuoteRepository;

/**
 * Class PaymentInformationManagementPlugin
 * @package Lofmp\DeliverySlot\Plugin
 */
class PaymentInformationManagementPlugin
{
    /**
     * @var OrderModel
     */
    protected $orderModelFactory;

    /**
     * @var OrderResourceModel
     */
    protected $orderResourceModelFactory;

    /**
     * @var DeliverySlotsFactory
     */
    protected $deliverySlotsFactory;

    /**
     * @var DeliverSlotsModelFactory
     */
    protected $deliverySlotsModelFactory;

    /**
     * @var OrderCollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var DeliverySlotsCollectionFactory
     */
    protected $deliverySlotsCollectionFactory;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * PaymentInformationManagementPlugin constructor.
     *
     * @param OrderModel $orderModelFactory
     * @param OrderResourceModel $orderResourceModelFactory
     * @param DeliverySlotsFactory $deliverySlotsFactory
     * @param DeliverSlotsModelFactory $deliverySlotsModelFactory
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param DeliverySlotsCollectionFactory $deliverySlotsCollectionFactory
     * @param DateTime $dateTime
     */
    public function __construct(
        OrderModel $orderModelFactory,
        OrderResourceModel $orderResourceModelFactory,
        DeliverySlotsFactory $deliverySlotsFactory,
        DeliverSlotsModelFactory $deliverySlotsModelFactory,
        OrderCollectionFactory $orderCollectionFactory,
        DeliverySlotsCollectionFactory $deliverySlotsCollectionFactory,
        DateTime $dateTime,
        QuoteRepository $quoteRepository
    ) {
        $this->orderModelFactory = $orderModelFactory;
        $this->orderResourceModelFactory = $orderResourceModelFactory;
        $this->deliverySlotsFactory = $deliverySlotsFactory;
        $this->deliverySlotsModelFactory = $deliverySlotsModelFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->deliverySlotsCollectionFactory = $deliverySlotsCollectionFactory;
        $this->dateTime = $dateTime;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param \Magento\Checkout\Api\PaymentInformationManagementInterface $subject
     * @param $cartId
     * @param \Magento\Quote\Api\Data\PaymentInterface $paymentMethod
     * @param \Magento\Quote\Api\Data\AddressInterface|null $billingAddress
     */
    public function beforeSavePaymentInformationAndPlaceOrder(
        \Magento\Checkout\Api\PaymentInformationManagementInterface $subject,
        $cartId,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        if (!empty($paymentMethod->getExtensionAttributes())) {
            if (!empty($paymentMethod->getExtensionAttributes()->getDeliverySlot())) {
                $slotId = $paymentMethod->getExtensionAttributes()->getDeliverySlot();
                try {
                    $deliverySlotResource = $this->deliverySlotsFactory->create();
                    $deliverySlotModel = $this->deliverySlotsModelFactory->create();
                    $deliverySlotResource->load($deliverySlotModel, $slotId);
                    $quote = $this->quoteRepository->get($cartId);
                    $quote->setData('delivery_comment', $paymentMethod->getExtensionAttributes()->getDeliveryComment());
                    $quote->setData('delivery_date', $paymentMethod->getExtensionAttributes()->getDeliveryDate());
                    $quote->setData('delivery_slot_id', $slotId);
                    $this->quoteRepository->save($quote);
                } catch (\Exception $e) {
                    return $e->getMessage();
                }
            }

            if (!empty($paymentMethod->getExtensionAttributes()->getDeliveryDate())) {
                $deliveryDate = $paymentMethod->getExtensionAttributes()->getDeliveryDate();
            }

            if (isset($deliveryDate) && isset($slotId)) {
                $ordersCount = $this->orderCollectionFactory->create()
                    ->addFieldToFilter('delivery_slot_id', $slotId)
                    ->addFieldToFilter('delivery_date', $deliveryDate)
                    ->getTotalCount();
                $slotDetails = $this->deliverySlotsCollectionFactory->create()
                    ->addFieldToFilter('slot_id', $slotId)
                    ->getFirstItem();
                $allocation = $slotDetails->getData('allocation');
                $day = strtolower($this->dateTime->date('D', $deliveryDate));
                $slotDay = $slotDetails->getData('day');
                if ($day != $slotDay) {
                    throw new LocalizedException(new Phrase(
                        'The Slot Not Existed'
                    ));
                }
                if ($allocation && !($ordersCount < $allocation)) {
                    throw new LocalizedException(new Phrase(
                        'The Slot has been booked. Please Choose Another Slot'
                    ));
                }
            }
        }
    }
}
