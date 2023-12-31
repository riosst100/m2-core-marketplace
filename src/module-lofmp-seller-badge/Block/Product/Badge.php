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
 * @package    Lofmp_SellerBadge
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerBadge\Block\Product;

class Badge extends \Lofmp\SellerBadge\Block\AbstractBadge
{
    /**
     * @var mixed|null
     */
    protected $current_seller = null;

    /**
     * @return bool|mixed
     */
    public function canDisplay()
    {
        return $this->helperData->isEnabled();
    }

    /**
     * Retrieve current product model
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->_coreRegistry->registry('current_product');
    }

    /**
     * @return int|array
     */
    public function getSellerId()
    {
        $product = $this->getProduct();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $sellerProduct = $objectManager->get(\Lof\MarketPlace\Model\SellerProduct::class)
            ->load($product->getId(), 'product_id');
        return $sellerProduct->getData() ? $sellerProduct->getSellerId() : 0;
    }

    /**
     * @return mixed|null
     */
    public function getCurrentSeller()
    {
        if (!$this->current_seller) {
            $sellerIds = $this->getSellerId();
            $sellerId = is_array($sellerIds) && $sellerIds ? $sellerIds[0] : (int)$sellerIds;
            $this->current_seller = $sellerId;
            if ($sellerId) {
                $this->setData('current_seller_id', $sellerId);
            }
        }
        return $this->current_seller;
    }
}
