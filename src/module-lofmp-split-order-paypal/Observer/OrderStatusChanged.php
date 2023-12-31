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
 * @package    Lofmp_SplitOrderPaypal
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SplitOrderPaypal\Observer;

use Magento\Checkout\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\ResourceModel\Order\Payment\CollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Payment as PaymentResource;

class OrderStatusChanged implements ObserverInterface
{
    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * @var Session
     */
    protected $_checkoutSession;

    /**
     * @var CollectionFactory
     */
    protected $paymentCollectionFactory;

    /**
     * @var PaymentResource
     */
    private $paymentResource;

    public function __construct(
        OrderFactory $orderFactory,
        Session $checkoutSession,
        CollectionFactory $paymentCollectionFactory,
        PaymentResource $paymentResource
    ) {
        $this->orderFactory = $orderFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->paymentCollectionFactory = $paymentCollectionFactory;
        $this->paymentResource = $paymentResource;
    }

    /**
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function execute(Observer $observer)
    {
        $orderIds = $this->_checkoutSession->getOrderIds();
        if ($orderIds) {
            foreach ($orderIds as $orderId => $incrementId) {
                $orderChild =  $this->orderFactory->create()->load($orderId);
                if ($orderChild->getPpParentOrderId()) {
                    $parentOrder = $this->orderFactory->create()->load($orderChild->getPpParentOrderId());
                    $orderChild->setState($parentOrder->getState());
                    $orderChild->setStatus($parentOrder->getStatus());
                    $orderChild->save();

                    if ($parentOrder->getData('pp_is_main_order')) {
                        $collection = $this->paymentCollectionFactory->create()
                            ->addFieldToFilter('entity_id', $parentOrder->getId());
                        foreach ($collection as $payment) {
                            if (!$payment->getId()) {
                                continue;
                            }
                            if ($payment->getId() != $payment->getParentId()) {
                                $payment->setParentId($parentOrder->getId());
                                $this->paymentResource->save($payment);
                            }
                        }
                    }
                }
            }
        }
    }
}
