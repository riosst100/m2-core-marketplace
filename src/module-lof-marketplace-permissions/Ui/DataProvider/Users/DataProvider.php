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

namespace Lof\MarketPermissions\Ui\DataProvider\Users;

use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Lof\MarketPermissions\Model\Seller\StructureFactory;

/**
 * Class DataProvider.
 */
class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    /**
     * Tree structure.
     *
     * @var \Lof\MarketPermissions\Model\Seller\StructureFactory
     */
    private $structureFactory;

    /**
     * @var \Lof\MarketPermissions\Model\SellerUser
     */
    private $sellerUser;

    /**
     * @var \Lof\MarketPermissions\Api\RoleManagementInterface
     */
    private $roleManagement;

    /**
     * @var \Lof\MarketPermissions\Model\SellerAdminPermission
     */
    private $sellerAdminPermission;

    /**
     * DataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param StructureFactory $structureFactory
     * @param \Lof\MarketPermissions\Model\SellerUser $sellerUser
     * @param \Lof\MarketPermissions\Api\RoleManagementInterface $roleManagement
     * @param \Lof\MarketPermissions\Model\SellerAdminPermission $sellerAdminPermission
     * @param array $meta
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        StructureFactory $structureFactory,
        \Lof\MarketPermissions\Model\SellerUser $sellerUser,
        \Lof\MarketPermissions\Api\RoleManagementInterface $roleManagement,
        \Lof\MarketPermissions\Model\SellerAdminPermission $sellerAdminPermission,
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
        $this->structureFactory = $structureFactory;
        $this->sellerUser = $sellerUser;
        $this->roleManagement = $roleManagement;
        $this->sellerAdminPermission = $sellerAdminPermission;
    }

    /**
     * Get data.
     *
     * @return array
     */
    public function getData()
    {
        try {
            $searchResult = $this->getSearchResult();
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return $this->createEmptySearchResult();
        }

        return $this->prepareSearchResult($searchResult);
    }

    /**
     * Create empty search result.
     *
     * @return array
     */
    private function createEmptySearchResult()
    {
        $arrItems = [];
        $arrItems['items'] = [];
        $arrItems['totalRecords'] = 0;
        return $arrItems;
    }

    /**
     * Returns Search result.
     *
     * @return SearchResultInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSearchResult()
    {
        $sellerId = $this->sellerUser->getCurrentSellerId();
        $this->filterBuilder->setField('seller_customer.seller_id');
        if ($sellerId == 0) {
            throw new \Magento\Framework\Exception\NoSuchEntityException();
        } else {
            $this->filterBuilder->setConditionType('eq')
                ->setValue($sellerId);
        }
        $filter = $this->filterBuilder->create();
        $this->searchCriteriaBuilder->addFilter($filter);

        return parent::getSearchResult();
    }

    /**
     * Prepare search result.
     *
     * @param SearchResultInterface $searchResult
     * @return array
     */
    private function prepareSearchResult(SearchResultInterface $searchResult)
    {
        $arrItems = [];
        $arrItems['items'] = [];

        foreach ($searchResult->getItems() as $item) {
            $itemData = [];

            foreach ($item->getCustomAttributes() as $attribute) {
                $itemData[$attribute->getAttributeCode()] = $attribute->getValue();
            }
            if ($this->sellerAdminPermission->isGivenUserSellerAdmin($item->getId())) {
                $itemData['role_id'] = $this->roleManagement->getSellerAdminRoleId();
                $roleName = $this->roleManagement->getSellerAdminRoleName();
                $itemData['role_name'] = __($roleName);
            }

            $itemData['team'] = $this->getTeamName($item->getId());
            $arrItems['items'][] = $itemData;
        }

        $arrItems['totalRecords'] = $searchResult->getTotalCount();

        return $arrItems;
    }

    /**
     * Get team name.
     *
     * @param int $customerId
     * @return null|string
     */
    private function getTeamName($customerId)
    {
        /**
         * @var \Lof\MarketPermissions\Model\Seller\Structure $structure
         */
        $structure = $this->structureFactory->create();
        return $structure->getTeamNameByCustomerId($customerId);
    }
}
