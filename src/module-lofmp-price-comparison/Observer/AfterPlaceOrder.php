<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_PriceComparison
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\PriceComparison\Observer;

use Magento\Framework\Event\ObserverInterface;
use Lofmp\PriceComparison\Model\ResourceModel\Quote\CollectionFactory as QuoteCollection;

class AfterPlaceOrder implements ObserverInterface
{
    /**
     * @var \Lofmp\PriceComparison\Model\ProductFactory
     */
    protected $_items;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_order;

    /**
     * @var QuoteCollection
     */
    protected $_quoteCollection;

    /**
     * @var \Lofmp\PriceComparison\Helper\Data 
     * 
     * */
    protected $_assignHelper;

    /**
     * @param \Lofmp\PriceComparison\Model\ProductFactory $itemsFactory
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param QuoteCollection $quoteCollectionFactory
     * @param \Lofmp\PriceComparison\Helper\Data $helper
     */
    public function __construct(
        \Lofmp\PriceComparison\Model\ProductFactory $itemsFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        QuoteCollection $quoteCollectionFactory,
        \Lofmp\PriceComparison\Helper\Data $helper
    ) {
        $this->_items = $itemsFactory;
        $this->_order = $orderFactory;
        $this->_quoteCollection = $quoteCollectionFactory;
        $this->_assignHelper = $helper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->_assignHelper->isEnabled()) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $orderIds = $observer->getEvent()->getData('order_ids');
            $orderId = $orderIds[0];
            $order = $this->_order->create()->load($orderId);
            $orderedItems = $order->getAllItems();
            foreach ($orderedItems as $item) {
                $qty = $item->getQtyOrdered();
                $quoteItemId = $item->getQuoteItemId();
                $collection = $this->_quoteCollection
                                    ->create()
                                    ->addFieldToFilter('item_id', $quoteItemId);
                foreach ($collection as $row) {
                    $assignId = $row->getAssignId();
                    if ($assignId > 0) {
                        $assignData = $this->_items->create()->load($assignId);
                        $qty = $assignData->getQty() - $qty;
                        $assignData->addData(['qty' => $qty])->setId($assignId)->save();
                    }
                }
            }
        }
    }
}
