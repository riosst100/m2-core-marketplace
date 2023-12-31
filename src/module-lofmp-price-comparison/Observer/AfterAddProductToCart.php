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

class AfterAddProductToCart implements ObserverInterface
{
    /**
     * Request instance.
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Lofmp\PriceComparison\Helper\Data 
     * 
     * */
    protected $_assignHelper;

    /**
     * @var \Magento\Checkout\Model\CartFactory
     */
    protected $_cart;

    /**
     * @var \Lofmp\PriceComparison\Model\QuoteFactory
     */
    protected $_quote;
    /**
     * @var  \Lof\MarketPlace\Helper\Data
     */
    protected $marketData;

    /**
     * @param RequestInterface $request
     * @param \Lofmp\PriceComparison\Helper\Data $helper
     * @param \Magento\Checkout\Model\CartFactory $cartFactory
     * @param \Lofmp\PriceComparison\Model\QuoteFactory $quoteFactory
     */
    public function __construct(
        RequestInterface $request,
        \Lofmp\PriceComparison\Helper\Data $helper,
        \Lof\MarketPlace\Helper\Data $marketData,
        \Magento\Checkout\Model\CartFactory $cartFactory,
        \Lofmp\PriceComparison\Model\QuoteFactory $quoteFactory
    ) {
        $this->marketData = $marketData;
        $this->_request = $request;
        $this->_assignHelper = $helper;
        $this->_cart = $cartFactory;
        $this->_quote = $quoteFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $helper = $this->_assignHelper;
        if ($helper->isEnabled()) {
            $info = $observer->getEvent()->getData('info');
            $productId = $observer->getEvent()->getData('product_id');
            
            $data = $this->_request->getParams();
            if (is_array($info)) {
                $assignId = 0;
                if (array_key_exists("mpassignproduct_id", $info)) {
                    $assignId = $info['mpassignproduct_id'];
                }
                $productId = isset($info['product'])?(int)$info['product']:(int)$productId;
            } else {
                $productId = (int) $this->_request->getParam('product');
                $assignId = (int) $this->_request->getParam('mpassignproduct_id');
            }
            $cartModel = $this->_cart->create();
            $quote = $cartModel->getQuote();
            $quoteId = $quote->getId();
            $ownerId = $helper->getSellerIdByProductId($productId);
            $customer_id = $this->marketData->getCustomerId();
            $flag = 0;
            if ($assignId > 0) {
                $sellerId = $helper->getAssignSellerIdByAssignId($assignId);
            } else {
                $sellerId = $ownerId;
            }
            foreach ($quote->getAllVisibleItems() as $item) {
                $itemId = $item->getId();
                $qty = $item->getQty();
            }
            if ($helper->isNewProduct($productId, $assignId)) {
                $model = $this->_quote->create();
                $quoteData = [
                                'item_id' => $itemId,
                                'seller_id' => $sellerId,
                                'customer_id' =>$customer_id,
                                'owner_id' => $ownerId,
                                'qty' => $qty,
                                'product_id' => $productId,
                                'assign_id' => $assignId,
                                'quote_id' => $quoteId,
                            ];
                $model->setData($quoteData)->save();
            }
        }
    }
}
