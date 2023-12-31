<?php
namespace Lof\MultiShippingFix\Plugin\Checkout\Model;

use Magento\Checkout\Model\Session as CheckoutSession;
use Lof\MarketPlace\Helper\Seller as SellerHelper;

class DefaultConfigProvider
{
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var SellerHelper
     */
    protected $sellerHelper;

    /**
     * @var array
     */
    protected $productSellers = [];

    /**
     * Constructor
     *
     * @param CheckoutSession $checkoutSession
     * @param SellerHelper $sellerHelper
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        SellerHelper $sellerHelper
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->sellerHelper = $sellerHelper;
    }

    public function afterGetConfig(
        \Magento\Checkout\Model\DefaultConfigProvider $subject,
        array $result
    ) {
        $items = $result['totalsData']['items'];
        foreach ($items as $index => $item) {
            $quoteItem = $this->checkoutSession->getQuote()->getItemById($item['item_id']);
            $product_id = $quoteItem->getProduct()->getId();
            if (!isset($this->productSellers[$product_id])) {
                $this->productSellers[$product_id] = $this->sellerHelper->getSellerIdByProduct($product_id);
            }

            $result['quoteItemData'][$index]['seller_id'] = $this->productSellers[$product_id];
        }
        return $result;
    }
}
