<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lofmp\FavoriteSeller\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Lofmp\FavoriteSeller\Model\Config\Config as FavoriteSellerConfig;
use Lofmp\FavoriteSeller\Model\SubscriptionRepository;


class CheckSeller implements ResolverInterface
{
    /**
     * @var FavoriteSellerConfig
     */
    private $favoriteSellerConfig;

    /**
     * @var \Lofmp\FavoriteSeller\Model\SubscriptionFactory
     */
    protected $subscriptionFactory;

    /**
     * @var SubscriptionRepository
     */
    protected $subscriptionRepository;

    /**
     * @param FavoriteSellerConfig $favoriteSellerConfig
     * @param SubscriptionRepository $subscriptionRepository
     */
    public function __construct(
        FavoriteSellerConfig $favoriteSellerConfig,
        SubscriptionRepository $subscriptionRepository
    ) {
        $this->favoriteSellerConfig = $favoriteSellerConfig;
        $this->subscriptionRepository = $subscriptionRepository;
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
        if (!$this->favoriteSellerConfig->isEnabled()) {
            throw new GraphQlInputException(__('The favorite seller configuration is currently disabled.'));
        }

        $customerId = $context->getUserId();

        /* Guest checking */
        if (null === $customerId || 0 === $customerId) {
            throw new GraphQlAuthorizationException(__('The current user cannot perform operations on favorite seller'));
        }

        $sellerId = $args['sellerId'];
        return $this->subscriptionRepository->checkSeller($customerId,$sellerId);
    }
}
