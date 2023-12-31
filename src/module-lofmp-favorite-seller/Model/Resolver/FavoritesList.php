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
use Lofmp\FavoriteSeller\Model\SubscriptionRepository;
use Lofmp\FavoriteSeller\Model\Config\Config as FavoriteSellerConfig;
use Magento\Framework\GraphQl\Query\Resolver\Argument\SearchCriteria\Builder;
use Magento\Search\Model\Query;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\GraphQl\Query\Resolver\Argument\SearchCriteria\ArgumentApplier\Filter;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Adding a seller to favorite
 */
class FavoritesList implements ResolverInterface
{
    /**
     * @var string
     */
    private const SPECIAL_CHARACTERS = '-+~/\\<>\'":*$#@()!,.?`=%&^';
    /**
     * @var FavoriteSellerConfig
     */
    private $favoriteSellerConfig;

    /**
     * @var SubscriptionRepository
     */
    protected $subscriptionRepository;

    /**
     * @var Builder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param FavoriteSellerConfig $favoriteSellerConfig
     * @param SubscriptionRepository $subscriptionRepository
     * @param Builder $searchCriteriaBuilder
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        FavoriteSellerConfig $favoriteSellerConfig,
        SubscriptionRepository $subscriptionRepository,
        Builder $searchCriteriaBuilder,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->favoriteSellerConfig = $favoriteSellerConfig;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->scopeConfig = $scopeConfig;
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
        if ($args['currentPage'] < 1) {
            throw new GraphQlInputException(__('currentPage value must be greater than 0.'));
        }
        if ($args['pageSize'] < 1) {
            throw new GraphQlInputException(__('pageSize value must be greater than 0.'));
        }

        $store = $context->getExtensionAttributes()->getStore();
        if(isset($args['filter']) && $args['filter']){
            $args[Filter::ARGUMENT_NAME] = $this->formatMatchFilters($args['filter'], $store);
        }

        $searchCriteria = $this->searchCriteriaBuilder->build('lofmp_favorite_list', $args);
        $searchCriteria->setCurrentPage($args['currentPage']);
        $searchCriteria->setPageSize($args['pageSize']);
        $searchResult = $this->subscriptionRepository->customerGetList($customerId,$searchCriteria);

        $totalPages = $args['pageSize'] ? ((int)ceil($searchResult->getTotalCount() / $args['pageSize'])) : 0;
        $itemsData = [];
        foreach ($searchResult->getItems() as $item) {
            $itemsData[] = $item->getData();
        }
        return [
            'total_count' => $searchResult->getTotalCount(),
            'items' => $itemsData,
            'page_info' => [
                'page_size' => $args['pageSize'],
                'current_page' => $args['currentPage'],
                'total_pages' => $totalPages
            ]
        ];
    }


    /**
     * Format match filter to behave like fuzzy match
     *
     * @param array $filter
     * @param StoreInterface $store
     * @return array
     * @throws InputException
     */
    private function formatMatchFilters(array $filters, StoreInterface $store): array
    {
        $minQueryLength = $this->scopeConfig->getValue(
            Query::XML_PATH_MIN_QUERY_LENGTH,
            ScopeInterface::SCOPE_STORE,
            $store
        );
        $availableMatchFilters = ["store_id"];
        foreach ($filters as $filter => $condition) {
            $conditionType = current(array_keys($condition));
            $tmpminQueryLength = $minQueryLength;
            if(in_array($filter, $availableMatchFilters)){
                $tmpminQueryLength = 1;
            }
            if ($conditionType === 'match') {
                $searchValue = trim(str_replace(self::SPECIAL_CHARACTERS, '', $condition[$conditionType]));
                $matchLength = strlen($searchValue);
                if ($matchLength < $tmpminQueryLength) {
                    throw new GraphQlInputException(__('Invalid match filter. Minimum length is %1.', $tmpminQueryLength));
                }
                unset($filters[$filter]['match']);
                if($filter == "store_id"){
                    $searchValue = (int)$searchValue;
                }
                $filters[$filter]['like'] = '%' . $searchValue . '%';
            }
        }
        return $filters;
    }
}
