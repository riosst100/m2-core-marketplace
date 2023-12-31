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
 * @package    Lof_MarketPermissions
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
namespace Lof\MarketPermissions\Model\Customer\Source;

use Magento\Customer\Api\Data\GroupInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class Group.
 */
class Group implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var GroupRepositoryInterface
     */
    private $groupRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * @param GroupRepositoryInterface $groupRepository
     * @param SortOrderBuilder $sortOrderBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        GroupRepositoryInterface $groupRepository,
        SortOrderBuilder $sortOrderBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->groupRepository = $groupRepository;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * {@inheritdoc}
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function toOptionArray()
    {
        $customerGroups = [];
        $sort = $this->sortOrderBuilder
            ->setField(GroupInterface::CODE)
            ->setAscendingDirection()
            ->create();
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(GroupInterface::ID, GroupInterface::NOT_LOGGED_IN_ID, 'neq')
            ->addSortOrder($sort)
            ->create();
        $groups = $this->groupRepository->getList($searchCriteria);
        foreach ($groups->getItems() as $group) {
            $customerGroups[] = [
                'label' => $group->getCode(),
                'value' => $group->getId(),
            ];
        }

        return $customerGroups;
    }
}
