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

use Lof\Auction\Api\ProductRepositoryInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Class Auction
 * @package Lof\Auction\Model\Resolver
 */
class Auction implements ResolverInterface
{

    /**
     * @var ProductRepositoryInterface
     */
    private $auctionDataProvider;

    /**
     * Auction constructor.
     * @param ProductRepositoryInterface $auctionDataProvider
     */
    public function __construct(
        ProductRepositoryInterface $auctionDataProvider
    ) {
        $this->auctionDataProvider = $auctionDataProvider;
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
        $auction = $this->auctionDataProvider->getById($args['auction_id']);
        if (!$auctionData = $auction->getDataArray()) {
            throw new GraphQlInputException(__('Auction with this id does not exits.'));
        }
        return $auctionData;
    }
}
