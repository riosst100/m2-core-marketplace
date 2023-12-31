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
use Lof\Auction\Api\MaxAbsenteeBidRepositoryInterface;

/**
 * Class DeleteSubscription
 * @package Lof\Auction\Model\Resolver
 */
class DeleteSubscription implements ResolverInterface
{
    /**
     * @var GetCustomer
     */
    private $getCustomer;
    /**
     * @var MaxAbsenteeBidRepositoryInterface
     */
    private $absenteeRepository;


    /**
     * @param GetCustomer $getCustomer
     * @param MaxAbsenteeBidRepositoryInterface $absenteeRepository
     */
    public function __construct(
        GetCustomer $getCustomer,
        MaxAbsenteeBidRepositoryInterface $absenteeRepository
    ) {
        $this->getCustomer = $getCustomer;
        $this->absenteeRepository = $absenteeRepository;
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
        if (!isset($args['subscrition_id']) || !$args['subscrition_id']) {
            throw new GraphQlInputException(__('Missing required param subscrition_id.'));
        }
        $customer = $this->getCustomer->execute($context);
        return $this->absenteeRepository->deleteMySubscription($customer->getId(), $args['subscrition_id']);
    }
}
