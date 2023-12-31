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
use Magento\Framework\App\RequestInterface;
use Lofmp\PriceComparison\Model\ResourceModel\Product\CollectionFactory as ItemsCollection;

class DeleteProduct implements ObserverInterface
{
    /**
     * @var ItemsCollection
     */
    protected $_itemsCollection;

    /**
     * @param ItemsCollection $itemsCollectionFactory
     */
    public function __construct(ItemsCollection $itemsCollectionFactory)
    {
        $this->_itemsCollection = $itemsCollectionFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $productId = $observer->getProduct()->getId();
            $collection = $this->_itemsCollection
                                ->create()
                                ->addFieldToFilter('product_id', $productId);
            foreach ($collection as $item) {
                $item->delete();
            }
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
    }
}
