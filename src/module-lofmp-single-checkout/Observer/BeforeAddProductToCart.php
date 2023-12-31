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
 * @package    Lofmp_SingleCheckout
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\SingleCheckout\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Message\ManagerInterface;

class BeforeAddProductToCart implements ObserverInterface
{
    /**
     * Request instance.
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;


    /**
     * @var \Magento\Checkout\Model\CartFactory
     */
    protected $_cart;

    /**
     * @var  \Lof\MarketPlace\Helper\Data
     */
    protected $marketData;


    protected $sellerProduct;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */

    protected $_messageManager;

    /**
     * @param RequestInterface $request
     * @param \Lofmp\SingleCheckout\Helper\Data $helper
     * @param \Magento\Checkout\Model\CartFactory $cartFactory
     * @param \Lofmp\SingleCheckout\Model\QuoteFactory $quoteFactory
     */
    public function __construct(
        RequestInterface $request,
          ManagerInterface $messageManager,
        \Lof\MarketPlace\Helper\Data $marketData,
        \Magento\Checkout\Model\CartFactory $cartFactory,
        \Lof\MarketPlace\Model\SellerProduct $sellerProduct
    )
    {
        $this->sellerProduct = $sellerProduct;
        $this->marketData = $marketData;
        $this->_request = $request;
        $this->_cart = $cartFactory;
         $this->_messageManager = $messageManager;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if(!$this->marketData->getConfig('general_settings/enable_singlecheckout')) {
            return;
        }
        $product_id = $observer->getRequest()->getParams('product')['product'];
     
        $seller_product = $this->sellerProduct->load($product_id,'product_id');

       
        
        $cartModel = $this->_cart->create();
        $quote = $cartModel->getQuote();
   
        if(count($seller_product->getData())) {

            $seller_id = $seller_product->getData('seller_id');
            foreach ($quote->getAllItems() as $item) {
                if ($seller_id && $seller_id == $item->getLofSellerId()) continue;
                $sellerProduct =  $this->sellerProduct->load($item->getProductId(),'product_id');
                if(count($sellerProduct->getData())) {
                    $sellerId = $sellerProduct->getData('seller_id');
                    if($seller_id != $sellerId) {
                        $this->_messageManager->addError(__('At a time you can add only single seller’s product in the cart.'));
                        $observer->getRequest()->setParam('product', false);
                        return ;
                    }
                }
            }
        } 

        return $this;
    }

}