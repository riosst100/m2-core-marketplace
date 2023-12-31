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
 * @package    Lof_Auction
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
declare(strict_types=1);

namespace Lof\Auction\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Lof\Auction\Api\ProductRepositoryInterface;
use Magento\Framework\GraphQl\Query\Resolver\Argument\SearchCriteria\Builder as SearchCriteriaBuilder;

/**
 * Class Auctions
 * @package Lof\Auction\Model\Resolver
 */
class Auctions implements ResolverInterface
{

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var ProductRepositoryInterface
     */
    private $auctionRepository;

    /**
     * Auctions constructor.
     * @param ProductRepositoryInterface $auctionRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        ProductRepositoryInterface $auctionRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->auctionRepository = $auctionRepository;
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
    ) {
        if ($args['currentPage'] < 1) {
            throw new GraphQlInputException(__('currentPage value must be greater than 0.'));
        }
        if ($args['pageSize'] < 1) {
            throw new GraphQlInputException(__('pageSize value must be greater than 0.'));
        }
        $searchCriteria = $this->searchCriteriaBuilder->build('lofAuctionList', $args);
        $searchCriteria->setCurrentPage($args['currentPage']);
        $searchCriteria->setPageSize($args['pageSize']);

        $searchResult = $this->auctionRepository->getList($searchCriteria);

        return [
            'total_count' => $searchResult->getTotalCount(),
            'items'       => $searchResult->getItems(),
        ];
    }
}
