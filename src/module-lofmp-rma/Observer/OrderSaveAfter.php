<?php
/**
 * LandOfCoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   LandOfCoder
 * @package    Lofmp_Rma
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\Rma\Observer;

use Magento\Framework\Event\ObserverInterface;

class OrderSaveAfter implements ObserverInterface
{
    /**
     * @var \Lofmp\Rma\Model\OrderStatusHistoryFactory
     */
    protected $orderStatusHistoryFactory;

    /**
     * @var \Lofmp\Rma\Model\OrderStatusHistoryRepository
     */
    protected $orderStatusHistoryRepository;

    /**
     * OrderSaveAfter constructor.
     *
     * @param \Lofmp\Rma\Model\OrderStatusHistoryFactory $orderStatusHistoryFactory
     * @param \Lofmp\Rma\Model\OrderStatusHistoryRepository $orderStatusHistoryRepository
     */
    public function __construct(
        \Lofmp\Rma\Model\OrderStatusHistoryFactory $orderStatusHistoryFactory,
        \Lofmp\Rma\Model\OrderStatusHistoryRepository $orderStatusHistoryRepository
    ) {
        $this->orderStatusHistoryFactory = $orderStatusHistoryFactory;
        $this->orderStatusHistoryRepository = $orderStatusHistoryRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        if (!$order = $observer->getEvent()->getOrder()) {
            return;
        }
        $status = $order->getStatus();
        $historyStatus = $this->orderStatusHistoryFactory->create()->getCollection()
            ->addFieldToFilter('order_id', $order->getId())
            ->getFirstItem();

        if ($status != $historyStatus->getStatus()) {
            $historyStatus->setOrderId($order->getId());
            $historyStatus->setStatus($status);
            $historyStatus->setCreatedAt(strtotime($order->getUpdatedAt()));
            $this->orderStatusHistoryRepository->save($historyStatus);
        }
    }
}
