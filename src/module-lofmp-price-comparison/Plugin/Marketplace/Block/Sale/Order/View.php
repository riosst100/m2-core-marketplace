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

namespace Lofmp\PriceComparison\Plugin\Marketplace\Block\Sale\Order;

use Lof\MarketPlace\Block\Sale\Order\View as OrderView;

class View
{
    /**
     * @var \Lofmp\PriceComparison\Model\ProductFactory
     */
    protected $_itemsFactory;
    /**
     * @param \Lofmp\PriceComparison\Model\ProductFactory $itemsFactory
     */
    public function __construct(
        \Lofmp\PriceComparison\Model\ProductFactory $itemsFactory
    ) {
        $this->_itemsFactory = $itemsFactory;
    }
    public function aroundCheckProductIsOfSeller(OrderView $subject, \Closure $proceed, $product_id, $seller_id = 0)
    {
        if ($product_id && $seller_id) {
            $collection = $this->_itemsFactory->create()->getCollection();
            $collection->addFieldToFilter("product_id", (int)$product_id)
                        ->addFieldToFilter("seller_id", (int)$seller_id);
            if ($collection->count()) {
                return true;
            }
        }
        return $proceed($product_id, $seller_id);
    }
}
