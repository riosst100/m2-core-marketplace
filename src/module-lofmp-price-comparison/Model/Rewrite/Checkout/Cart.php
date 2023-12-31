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

namespace Lofmp\PriceComparison\Model\Rewrite\Checkout;

class Cart extends \Magento\Checkout\Model\Cart
{
    /**
     * Add product to shopping cart (quote)
     *
     * @param int|Product $productInfo
     * @param \Magento\Framework\DataObject|int|array $requestInfo
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function addProduct($productInfo, $requestInfo = null)
    {
        $product = $this->_getProduct($productInfo);
        $productId = $product->getId();
        $request = $this->_getProductRequest($requestInfo);

        if ($productId) {
            $stockItem = $this->stockRegistry->getStockItem($productId, $product->getStore()->getWebsiteId());
            $minQty = $stockItem->getMinSaleQty();
            //If product was not found in cart and there is set minimal qty for it
            if ($minQty
                && $minQty > 0
                && $request->getQty() < $minQty
                && !$this->getQuote()->hasProductId($productId)
            ) {
                $request->setQty($minQty);
            }
        }

        if ($productId) {
            try {
                $quoteItem = $this->getQuote()->addProduct($product, $request);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->_checkoutSession->setUseNotice(false);
                $quoteItem = $e->getMessage();
            }
            /**
             * String we can get if prepare process has error
             */
            if (is_string($quoteItem)) {
                if ($product->hasOptionsValidationFail()) {
                    $url = $product->getUrlModel()->getUrl(
                        $product,
                        ['_query' => ['startcustomization' => 1]]
                    );
                } else {
                    $url = $product->getProductUrl();
                }
                $this->_checkoutSession->setRedirectUrl($url);
                if ($this->_checkoutSession->getUseNotice() === null) {
                    $this->_checkoutSession->setUseNotice(true);
                }
                throw new \Magento\Framework\Exception\LocalizedException(__($quoteItem));
            }
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(__('The product does not exist.'));
        }
        $this->_eventManager->dispatch(
            'checkout_cart_product_add_after',
            ['quote_item' => $quoteItem, 'product' => $product, 'info' => $requestInfo]
        );
        $this->_checkoutSession->setLastAddedProductId($productId);
        return $this;
    }
}
