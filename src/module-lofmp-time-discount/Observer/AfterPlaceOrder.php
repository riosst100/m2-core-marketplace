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
 * @package    Lofmp_TimeDiscount
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\TimeDiscount\Observer;

use Magento\Framework\Event\ObserverInterface;
use Lofmp\TimeDiscount\Model\ResourceModel\Quote\CollectionFactory as QuoteCollection;

class AfterPlaceOrder implements ObserverInterface
{
    /**
     * @var \Lofmp\TimeDiscount\Model\ProductFactory
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
     * @var Quote
     */
    protected $time_quote;

    /**
     * @param \Lofmp\TimeDiscount\Model\ProductFactory $itemsFactory
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param QuoteCollection $quoteCollectionFactory
     */
    public function __construct(
        \Lofmp\TimeDiscount\Model\ProductFactory $itemsFactory,
        \Lofmp\TimeDiscount\Model\Quote $time_quote,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        QuoteCollection $quoteCollectionFactory
    )
    {
        $this->time_quote = $time_quote;
        $this->_items = $itemsFactory;
        $this->_order = $orderFactory;
        $this->_quoteCollection = $quoteCollectionFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $orderIds = $observer->getEvent()->getOrderIds(); 
        $order_id = $orderIds[0];
        $objectManager       = \Magento\Framework\App\ObjectManager::getInstance ();
        $orderDetails        = $objectManager->get ( 'Magento\Sales\Model\Order' );
        $orderData           = $orderDetails->load ( $order_id );
        $quote_id = $orderData->getData('quote_id');

        $time_quote = $this->time_quote->getCollection()->addFieldToFilter('quote_id',$quote_id);
        foreach ($time_quote as $key => $_time_quote) {
            $_time_quote->setOrderId($order_id);
            $_time_quote->save();
        }
    }
}