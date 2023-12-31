<?php
namespace Lof\MarketPlace\Plugin\Wishlist\Model;

use Lof\MarketPlace\Helper\Seller;
use Lof\MarketPlace\Model\Vacation;
use Magento\Catalog\Model\Product;
use Magento\Wishlist\Model\Item;

class ItemPlugin
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
     * Do not add product to cart in wishlist if seller is being on vacation
     *
     * @param Item $subject
     * @param mixed $result
     * @return mixed
     */
    public function afterGetProduct(Item $subject, $result)
    {
        try {
            $sellerId = $this->sellerHelper->getSellerIdByProduct($result->getId());
            $vacation = $this->sellerHelper->getVacationBySellerId($sellerId);
            if ($vacation->getStatus() == Vacation::STATUS_ENABLED) {
                $result->setStatus(false);
            }
        } catch (\Exception $e){
            return $result;
        }
        return $result;
    }
}
