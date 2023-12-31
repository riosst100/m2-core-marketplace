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

class AfterRemoveItem implements ObserverInterface
{
    /**
     * @var QuoteCollection
     */
    protected $_quoteCollection;

    /**
     * @param QuoteCollection $quoteCollectionFactory
     */
    public function __construct(QuoteCollection $quoteCollectionFactory)
    {
        $this->_quoteCollection = $quoteCollectionFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $item = $observer->getEvent()->getData('quote_item');
        $itemId = (int) $item->getId();
        $collection = $this->_quoteCollection
                            ->create()
                            ->addFieldToFilter('item_id', $itemId);
        foreach ($collection as $item) {
            $item->delete();
        }
    }
}
