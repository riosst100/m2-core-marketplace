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

declare(strict_types=1);

namespace Lof\MarketplaceGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Query\Resolver\Argument\SearchCriteria\Builder as SearchCriteriaBuilder;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Lof\MarketPlace\Api\SellersFrontendRepositoryInterface;
use Lof\MarketPlace\Api\SellerProductsRepositoryInterface;
use Lof\MarketPlace\Api\SellersRepositoryInterface;

/**
 * Class AbstractSellerQuery
 *
 * @package Lof\MarketplaceGraphQl\Model\Resolver
 */
abstract class AbstractSellerQuery
{
    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var ProductRepositoryInterface
     */
    protected $_productRepository;

    /**
     * @var int
     */
    protected $_labelFlag;
    /**
     * @var SellersFrontendRepositoryInterface
     */
    protected $_sellerRepository;

    /**
     * @var SellerProductsRepositoryInterface
     */
    protected $_productSeller;

    /**
     * @var SellersRepositoryInterface
     */
    protected $_sellerManagementRepository;

    /**
     * AbstractSellerQuery constructor.
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SellersFrontendRepositoryInterface $seller
     * @param SellerProductsRepositoryInterface $productSeller
     * @param ProductRepositoryInterface $productRepository
     * @param SellersRepositoryInterface $sellerManagementRepository
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SellersFrontendRepositoryInterface $seller,
        SellerProductsRepositoryInterface $productSeller,
        ProductRepositoryInterface $productRepository,
        SellersRepositoryInterface $sellerManagementRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_sellerRepository = $seller;
        $this->_productSeller = $productSeller;
        $this->_productRepository = $productRepository;
        $this->_sellerManagementRepository = $sellerManagementRepository;
    }

    /**
     * @param array $args
     *
     * @throws GraphQlInputException
     */
    protected function validateArgs(array $args)
    {
        if ($this->_labelFlag && !isset($args['seller_id'])) {
            throw new GraphQlInputException(__('Seller id is required.'));
        }
    }
}
