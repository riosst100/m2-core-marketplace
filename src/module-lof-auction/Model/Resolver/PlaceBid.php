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

use Magento\CustomerGraphQl\Model\Customer\GetCustomer;
use Magento\Framework\GraphQl\Config\Element\Field;

use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Lof\Auction\Api\AmountRepositoryInterface;
use Magento\Framework\GraphQl\Query\Resolver\Argument\SearchCriteria\Builder as SearchCriteriaBuilder;

/**
 * Class PlaceBid
 * @package Lof\Auction\Model\Resolver
 */
class PlaceBid implements ResolverInterface
{
    /**
     * @var GetCustomer
     */
    private $getCustomer;
    /**
     * @var AmountRepositoryInterface
     */
    private $amountRepository;


    /**
     * @param GetCustomer $getCustomer
     * @param AmountRepositoryInterface $amountRepositoryInterface
     */
    public function __construct(
        GetCustomer $getCustomer,
        AmountRepositoryInterface $amountRepositoryInterface
    ) {
        $this->getCustomer = $getCustomer;
        $this->amountRepository = $amountRepositoryInterface;
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

        if (!$context->getExtensionAttributes()->getIsCustomer()) {
            throw new GraphQlAuthorizationException(__('The current customer isn\'t authorized.'));
        }
        $customer = $this->getCustomer->execute($context);

        $isAuto = 0;
        $amount = 0;
        if (isset($args['is_auto'])) {
            $isAuto = $args['is_auto'];
        }
        if (isset($args['amount'])) {
            $amount = $args['amount'];
        }

        return $this->amountRepository->placeBid($customer->getId(), $args['product_id'], $amount, $isAuto);
    }
}
