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
 * @package    Lof_MarketplaceGraphQl
 * @copyright  Copyright (c) 2022 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
declare(strict_types=1);

namespace Lof\MarketplaceGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Catalog\Model\Product;

/**
 * Class Seller
 *
 * @package Lof\MarketplaceGraphQl\Model\Resolver
 */
class Seller extends AbstractSellerQuery implements ResolverInterface
{
    /**
     * @inheritDoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!isset($value['model'])) {
            throw new GraphQlInputException(__('Value must contain "model" property.'));
        }
        /** @var Product $product */
        $product = $value['model'];
        $productSku = $product->getSku();
        if (empty($productSku)) {
            throw new GraphQlInputException(__('Value must contain "product_sku" property.'));
        }
        $store = $context->getExtensionAttributes()->getStore();
        $storeId = $store->getId();
        $data = [];
        try {
            $sellerData = $this->_sellerRepository->getSellerByProductSku($productSku, $storeId);
            $data = $sellerData ? $sellerData->__toArray() : [];
            $data["model"] = $sellerData;
        } catch (\Exception $e) {
            //
        }
        return $data;
    }
}
