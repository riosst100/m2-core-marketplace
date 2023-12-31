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

class QuoteSubmitAfter implements ObserverInterface
{
    /**
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $quoteSession;

    /**
     * @var \Lofmp\Rma\Model\RmaFactory
     */
    protected $rmaFactory;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * QuoteSubmitAfter constructor.
     *
     * @param \Magento\Backend\Model\Session\Quote $quoteSession
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Lofmp\Rma\Model\RmaFactory $rmaFactory
     */
    public function __construct(
        \Magento\Backend\Model\Session\Quote $quoteSession,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\ResourceConnection $resource,
        \Lofmp\Rma\Model\RmaFactory $rmaFactory
    ) {
        $this->quoteSession = $quoteSession;
        $this->objectManager = $objectManager;
        $this->_resource = $resource;
        $this->rmaFactory = $rmaFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getOrder();
        if ($rmaId = $this->quoteSession->getRmaId()) {
            /** @var \Lofmp\Rma\Model\Rma\Interceptor $rma */
            $rma = $this->rmaFactory->create()->load($rmaId);
            $id = $order->getId();
            $objArray = [
                're_rma_id' => $rmaId,
                're_exchange_order_id' => $id,
            ];
            $this->_resource->getConnection()->insert(
                $this->getTable('lofmp_rma_rma_order'),
                $objArray
            );
            $this->quoteSession->unsetRmaId();
        }
    }
}
