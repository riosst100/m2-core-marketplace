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
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Observer;

use Magento\Framework\Event\ObserverInterface;

class LoadProductSeller implements ObserverInterface
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        // TODO: check feature this file
        $product = $observer->getProduct();
        if ($product->getId()) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $sellerProduct = $objectManager->get(\Lof\MarketPlace\Model\SellerProduct::class)
                ->load($product->getId(), 'product_id');
            $sellerId = $sellerProduct->getData() ? $sellerProduct->getSellerId() : 0;
            $product->setData('product_seller', $sellerId);
        }
    }
}
