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
use Magento\Catalog\Api\ProductRepositoryInterface;
use Lof\MarketplaceGraphQl\Model\Resolver\Products\Query\ProductQueryInterface;
use Lof\MarketPlace\Api\SellersRepositoryInterface;
use Lof\MarketPlace\Api\SellerRatingsRepositoryInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\Argument\SearchCriteria\Builder as SearchCriteriaBuilder;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Class SellerRates
 *
 * @package Lof\MarketplaceGraphQl\Model\Resolver
 */
class SellerRates extends AbstractSellerQuery implements ResolverInterface
{
    /**
     * @var ProductQueryInterface
     */
    private $searchQuery;

    /**
     * @var mixed|array
     */
    protected $_sellers = [];

    /**
     * @var SellerRatingsRepositoryInterface
     */
    protected $sellerRatingRepository;

    /**
     * @inheritdoc
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SellersFrontendRepositoryInterface $seller,
        SellerProductsRepositoryInterface $productSeller,
        ProductRepositoryInterface $productRepository,
        ProductQueryInterface $searchQuery,
        SellersRepositoryInterface $sellerManagementRepository,
        SellerRatingsRepositoryInterface $sellerRatingRepository
    ) {
        $this->searchQuery = $searchQuery;
        $this->sellerRatingRepository = $sellerRatingRepository;
        parent::__construct($searchCriteriaBuilder, $seller, $productSeller, $productRepository, $sellerManagementRepository);
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    )
    {
        if ($args['currentPage'] < 1) {
            throw new GraphQlInputException(__('currentPage value must be greater than 0.'));
        }
        if ($args['pageSize'] < 1) {
            throw new GraphQlInputException(__('pageSize value must be greater than 0.'));
        }
        if (!isset($args['seller_url'])) {
            throw new GraphQlInputException(
                __("'seller_url' input argument is required.")
            );
        }

        $searchCriteria = $this->searchCriteriaBuilder->build( 'lof_marketplace_seller_ratings', $args );
        $searchCriteria->setCurrentPage( $args['currentPage'] );
        $searchCriteria->setPageSize( $args['pageSize'] );

        $searchResult = $this->sellerRatingRepository->getListByUrl($args['seller_url'], $searchCriteria);
        $totalPages = $args['pageSize'] ? ((int)ceil($searchResult->getTotalCount() / $args['pageSize'])) : 0;

        return [
            'total_count' => $searchResult->getTotalCount(),
            'items'       => $searchResult->getItems(),
            'page_info' => [
                'page_size' => $args['pageSize'],
                'current_page' => $args['currentPage'],
                'total_pages' => $totalPages
            ],
            'model' => $searchResult
        ];
    }
}
