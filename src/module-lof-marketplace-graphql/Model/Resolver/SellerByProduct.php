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
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketplaceGraphQl\Model\Resolver;

use Lof\MarketPlace\Api\SellerProductsRepositoryInterface;
use Lof\MarketPlace\Api\SellersFrontendRepositoryInterface;
use Lof\MarketPlace\Api\SellersRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\Argument\SearchCriteria\Builder as SearchCriteriaBuilder;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
/**
 * Class SellerByProduct
 *
 * @package Lof\MarketplaceGraphQl\Model\Resolver
 */
class SellerByProduct extends AbstractSellerQuery implements ResolverInterface
{
    /**
     * @var ProductRepository
     */
    private $product;

    /**
     * @var Collection
     */
    private $productCollection;

    /**
     * @inheritdoc
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SellersFrontendRepositoryInterface $seller,
        SellerProductsRepositoryInterface $productSeller,
        ProductRepositoryInterface $productRepository,
        ProductRepository $product,
        Collection $collection,
        SellersRepositoryInterface $sellerManagementRepository
    )
    {
        $this->product = $product;
        $this->productCollection = $collection;
        parent::__construct($searchCriteriaBuilder, $seller, $productSeller, $productRepository, $sellerManagementRepository);
    }

    /**
     * @inheritDoc
     */
    public function resolve( Field $field, $context, ResolveInfo $info, array $value = null, array $args = null )
    {
        if (!isset($args['product_sku']) || (isset($args['product_sku']) && !$args['product_sku'])) {
            throw new GraphQlInputException(
                __("'product_sku' input argument is required.")
            );
        }

        $isGetProducts = isset($args['get_products']) ? (bool)$args['get_products'] : false;
        $isGetOtherInfo = isset($args['get_other_info']) ? (bool)$args['get_other_info'] : false;
        $store = $context->getExtensionAttributes()->getStore();
        $storeId = $store->getId();

        $sellerData = $this->_sellerRepository->getSellerByProductSku($args['product_sku'], $storeId, $isGetProducts, $isGetOtherInfo);
        $data = $sellerData ? $sellerData->__toArray() : [];
        $data["model"] = $sellerData;

        return $data;
    }
}
