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
 * @package    Lofmp_SplitCart
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SplitCart\Plugin\Cart;

use Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory as SellerCollectionFactory;
use Lof\MarketPlace\Model\Seller;
use Lofmp\SplitCart\Helper\ConfigData;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Quote\Api\Data\CartItemExtensionFactory;

class AddSellerToCartItemPlugin
{
    /**
     * @var SellerCollectionFactory
     */
    private $sellerCollectionFactory;

    /**
     * @var ConfigData
     */
    private $moduleConfig;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var CartItemExtensionFactory
     */
    private $cartItemExtensionFactory;

    /**
     * @var mixed|array
     */
    private $sellers = [];

    /**
     * @param SellerCollectionFactory $sellerCollectionFactory
     * @param ConfigData $moduleConfig
     * @param ProductRepositoryInterface $productRepository
     * @param CartItemExtensionFactory $cartItemExtensionFactory
     */
    public function __construct(
        SellerCollectionFactory $sellerCollectionFactory,
        ConfigData $moduleConfig,
        ProductRepositoryInterface $productRepository,
        CartItemExtensionFactory $cartItemExtensionFactory
    ) {
        $this->sellerCollectionFactory = $sellerCollectionFactory;
        $this->productRepository = $productRepository;
        $this->moduleConfig = $moduleConfig;
        $this->cartItemExtensionFactory = $cartItemExtensionFactory;
    }

    /**
     * @param \Magento\Quote\Model\Quote $subject
     * @param mixed $result
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetItems(\Magento\Quote\Model\Quote $subject, $result)
    {
        if ($this->moduleConfig->isEnabled() && $this->moduleConfig->isAllowAddSellerData() && $result) {
            $tmpItems = [];
            foreach ($result as $cartItem) {
                /** @var \Magento\Quote\Api\Data\CartItemInterface $cartItem */
                if ($cartItem->getLofSellerId()) {
                    $sellerData = $this->getSeller($cartItem->getLofSellerId());
                } else {
                    $sellerData = $this->getSellerByProductSku($cartItem->getSku());
                }

                $extensionAttributes = $cartItem->getExtensionAttributes();
                if ($extensionAttributes === null) {
                    $extensionAttributes = $this->cartItemExtensionFactory->create();
                }
                if (!$extensionAttributes->getSellerId()) {
                    $extensionAttributes->setSellerId(
                        $sellerData["sellerId"]
                    );
                }
                if (!$extensionAttributes->getSellerUrl()) {
                    $extensionAttributes->setSellerUrl(
                        $sellerData["sellerUrlKey"]
                    );
                }
                if (!$extensionAttributes->getSellerName()) {
                    $extensionAttributes->setSellerName(
                        $sellerData["sellerName"]
                    );
                }
                $cartItem->setExtensionAttributes($extensionAttributes);
                $tmpItems[] = $cartItem;
            }
            $result = $tmpItems;
        }
        return $result;
    }

    /**
     * Get seller data info
     * @param string $sku
     * @return mixed
     */
    protected function getSellerByProductSku($sku)
    {
        $sellerId = 0;
        $sellerUrlKey = "";
        $sellerName = "";
        $product = $this->productRepository->get($sku);
        if ($product && $product->getSellerId()) {
            $seller = $this->getSellerById($product->getSellerId());
            if ($seller && $seller->getId()) {
                $sellerId = $product->getSellerId();
                $sellerName = $seller->getName();
                $sellerUrlKey = $seller->getUrlKey();
            }
        }
        return [
            "sellerId" => $sellerId,
            "sellerUrlKey" => $sellerUrlKey,
            "sellerName" => $sellerName
        ];
    }

    /**
     * Get seller data info
     * @param string $seller_id
     * @return mixed
     */
    protected function getSeller($sellerId)
    {
        $sellerId = 0;
        $sellerUrlKey = "";
        $sellerName = "";
        if ($sellerId) {
            $seller = $this->getSellerById($sellerId);
            if ($seller && $seller->getId()) {
                $sellerName = $seller->getName();
                $sellerUrlKey = $seller->getUrlKey();
            }
        }
        return [
            "sellerId" => $sellerId,
            "sellerUrlKey" => $sellerUrlKey,
            "sellerName" => $sellerName
        ];
    }

    /**
     * get seller by seller id
     *
     * @param int $sellerId
     * @return \Lof\MarketPlace\Model\Seller
     */
    protected function getSellerById($sellerId)
    {
        if (!isset($this->sellers[$sellerId])) {
            $this->sellers[$sellerId] = $this->sellerCollectionFactory->create()
                                        ->addFieldToFilter('seller_id', $sellerId)
                                        ->addFieldToFilter("status", Seller::STATUS_ENABLED)
                                        ->getFirstItem();
        }

        return $this->sellers[$sellerId];
    }
}
