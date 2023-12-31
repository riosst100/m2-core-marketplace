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

class CustomPrice implements ObserverInterface
{
    /**
     * Request instance.
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Lofmp\PriceComparison\Helper\Data
     */
    protected $_assignHelper;

    /**
     * @var \Magento\Checkout\Model\CartFactory
     */
    protected $_cart;

    /**
     * @param RequestInterface $request
     * @param \Lofmp\PriceComparison\Helper\Data $helper
     * @param \Magento\Checkout\Model\CartFactory $cartFactory
     */
    public function __construct(
        RequestInterface $request,
        \Lofmp\PriceComparison\Helper\Data $helper,
        \Magento\Checkout\Model\CartFactory $cartFactory
    ) {
        $this->_request = $request;
        $this->_assignHelper = $helper;
        $this->_cart = $cartFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->_assignHelper->isEnabled()) {
            $helper = $this->_assignHelper;
            $item = $observer->getEvent()->getData('quote_item');
            $info = $observer->getEvent()->getData('info');
            if (is_object($info)) {
                $info = $info->getData();
            }
            $data = $this->_request->getParams();
            $product = $observer->getEvent()->getData('product');
            $assignId = 0;
            $qty = 1;
            $productId = $product->getId();
            if (is_array($info)) {
                if (array_key_exists("mpassignproduct_id", $info)) {
                    $assignId = $info['mpassignproduct_id'];
                }
                if (array_key_exists("qty", $info)) {
                    $qty = $info['qty'];
                }
            }
            /* if (!$helper->isQtyAllowed($qty, $productId, $assignId)) {
                $error = __('Requested quantity not available from seller.');
                throw new \Magento\Framework\Exception\LocalizedException($error);
            }*/
            $itemId = (int) $item->getId();
            if ($itemId > 0) {
                $originalQty = $item->getQty() - $qty;
                $item->setQty($originalQty);
                $cartModel = $this->_cart->create();
                $quote = $cartModel->getQuote();
                $requestedItemId = $helper->getRequestedItemId($assignId, $productId, $quote->getId());
                foreach ($quote->getAllItems() as $item) {
                    $quoteItemId = $item->getId();
                    if ($requestedItemId == $quoteItemId) {
                        $qty = $item->getQty() + $qty;
                        $item->setQty($qty);
                    }
                }
            } else {
                if ($assignId > 0) {
                    // $item = ( $item->getParentItem() ? $item->getParentItem() : $item );
                    $price = $helper->getAssignProductPrice($assignId);
                    $item->setCustomPrice($price);
                    $item->setOriginalCustomPrice($price);
                    $item->getProduct()->setIsSuperMode(true);
                }
            }
        }
    }
}
