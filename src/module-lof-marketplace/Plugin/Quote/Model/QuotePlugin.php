<?php
namespace Lof\MarketPlace\Plugin\Quote\Model;

use Lof\MarketPlace\Helper\Seller;
use Lof\MarketPlace\Model\Vacation;
use Magento\Catalog\Model\Product;
use Magento\Quote\Model\Quote;

class QuotePlugin
{
    /**
     * @param Seller $sellerHelper
     */
    public function __construct(
        Seller $sellerHelper
    ) {
        $this->sellerHelper = $sellerHelper;
    }


    /**
     * Do not add product to cart if seller is being on vacation
     *
     * @param Quote $subject
     * @param Product $product
     * @param null $request
     * @param string $processMode
     * @return array
     */
    public function beforeAddProduct(
        Quote $subject,
        Product $product,
        $request = null,
        $processMode = \Magento\Catalog\Model\Product\Type\AbstractType::PROCESS_MODE_FULL
    ) {
        try {
            $sellerId = $this->sellerHelper->getSellerIdByProduct($product->getId());
            $vacation = $this->sellerHelper->getVacationBySellerId($sellerId);
            if ($vacation->getStatus() == Vacation::STATUS_ENABLED) {
                $product->setIsSalable(0);
            }
        } catch (\Exception $e){
            return [$product,$request, $processMode];
        }
        return [$product, $request, $processMode];
    }
}
