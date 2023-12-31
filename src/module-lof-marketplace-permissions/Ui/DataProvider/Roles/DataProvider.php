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

namespace Lof\MarketPermissions\Ui\DataProvider\Roles;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\View\Element\UiComponent\DataProvider\Reporting;
use Magento\Framework\Api\SearchResultsInterface;

/**
 * Class DataProvider
 */
class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    /**
     * @var \Lof\MarketPermissions\Model\RoleRepository
     */
    private $roleRepository;

    /**
     * @var \Lof\MarketPermissions\Model\SellerUser
     */
    private $sellerUser;

    /**
     * @var \Lof\MarketPermissions\Model\UserRoleManagement
     */
    private $userRoleManagement;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param Reporting $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\App\RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param \Lof\MarketPermissions\Model\RoleRepository $roleRepository
     * @param \Lof\MarketPermissions\Model\SellerUser $sellerUser
     * @param \Lof\MarketPermissions\Model\UserRoleManagement $userRoleManagement
     * @param array $meta
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        Reporting $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\App\RequestInterface $request,
        FilterBuilder $filterBuilder,
        \Lof\MarketPermissions\Model\RoleRepository $roleRepository,
        \Lof\MarketPermissions\Model\SellerUser $sellerUser,
        \Lof\MarketPermissions\Model\UserRoleManagement $userRoleManagement,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
        $this->roleRepository = $roleRepository;
        $this->sellerUser = $sellerUser;
        $this->userRoleManagement = $userRoleManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->formatOutput($this->getSearchResult());
    }

    /**
     * Returns Search result
     *
     * @return SearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSearchResult()
    {
        $this->addOrder('role_name', 'ASC');
        $filter = $this->filterBuilder
            ->setField('main_table.seller_id')
            ->setConditionType('eq')
            ->setValue($this->sellerUser->getCurrentSellerId())
            ->create();
        $this->searchCriteriaBuilder->addFilter($filter);
        $this->searchCriteria = $this->searchCriteriaBuilder->create();
        $this->searchCriteria->setRequestName($this->name);

        return $this->roleRepository->getList($this->getSearchCriteria(), true);
    }

    /**
     * @param SearchResultsInterface $searchResult
     * @return array
     */
    private function formatOutput(SearchResultsInterface $searchResult)
    {
        $arrItems = [];
        $arrItems['totalRecords'] = $searchResult->getTotalCount();

        $arrItems['items'] = [];
        foreach ($searchResult->getItems() as $item) {
            $itemData = [];
            foreach ($item->getData() as $key => $value) {
                $itemData[$key] = $value;
            }
            $roleId = $item->getRoleId();
            $itemData['users_count'] = $this->userRoleManagement->getUsersCountByRoleId($roleId);
            $arrItems['items'][] = $itemData;
        }
        return $arrItems;
    }
}
