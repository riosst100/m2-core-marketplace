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
use Magento\Catalog\Model\Layer\Resolver;
use Lof\MarketplaceGraphQl\Model\Resolver\Products\Query\ProductQueryInterface;
use Lof\MarketPlace\Model\SellerFactory;
use Lof\MarketPlace\Api\SellersRepositoryInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\Argument\SearchCriteria\Builder as SearchCriteriaBuilder;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Class ProductSellers
 *
 * @package Lof\MarketplaceGraphQl\Model\Resolver
 */
class ProductBySellerUrl extends AbstractSellerQuery implements ResolverInterface
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
     * @var SellerFactory
     */
    protected $sellerFactory;

    /**
     * @inheritdoc
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SellersFrontendRepositoryInterface $seller,
        SellerProductsRepositoryInterface $productSeller,
        ProductRepositoryInterface $productRepository,
        ProductQueryInterface $searchQuery,
        SellerFactory $sellerFactory,
        SellersRepositoryInterface $sellerManagementRepository
    ) {
        $this->searchQuery = $searchQuery;
        $this->sellerFactory = $sellerFactory;
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
        $seller = $this->getSellerByUrl($args['seller_url']);
        if (!$seller || ($seller && !$seller->getId())) {
            throw new GraphQlInputException(
                __("Seller %1 is not exists.", $args['seller_url'])
            );
        }
        $args['seller_id'] = $seller->getId();
        $searchResult = $this->searchQuery->getResult($args, $info, $context);

        if ($searchResult->getCurrentPage() > $searchResult->getTotalPages() && $searchResult->getTotalCount() > 0) {
            throw new GraphQlInputException(
                __(
                    'currentPage value %1 specified is greater than the %2 page(s) available.',
                    [$searchResult->getCurrentPage(), $searchResult->getTotalPages()]
                )
            );
        }

        return [
            'total_count' => $searchResult->getTotalCount(),
            'items' => $searchResult->getProductsSearchResult(),
            'page_info' => [
                'page_size' => $searchResult->getPageSize(),
                'current_page' => $searchResult->getCurrentPage(),
                'total_pages' => $searchResult->getTotalPages()
            ],
            'search_result' => $searchResult,
            'layer_type' => isset($args['search']) ? Resolver::CATALOG_LAYER_SEARCH : Resolver::CATALOG_LAYER_CATEGORY,
        ];
    }

    /**
     * get seller by sellerUrl
     *
     * @param string $sellerUrl
     * @return \Lof\MarketPlace\Model\Seller
     */
    protected function getSellerByUrl(string $sellerUrl)
    {
        if (!isset($this->_sellers[$sellerUrl])) {
            $seller = $this->sellerFactory->create()->getCollection()
                    ->addFieldToFilter('url_key', ['eq' => $sellerUrl])
                    ->addFieldToFilter("status", \Lof\MarketPlace\Model\Seller::STATUS_ENABLED)
                    ->getFirstItem();
            $this->_sellers[$sellerUrl] = $seller;
        }
        return $this->_sellers[$sellerUrl];
    }
}
