<?php
/**
 * Lof CouponCode is a powerful tool for managing the processing return and exchange requests within your workflow. This, in turn, allows your customers to request and manage returns and exchanges directly from your webstore. The Extension compatible with magento 2.x
 * Copyright (C) 2017  Landofcoder.com
 * 
 * This file is part of Lofmp/CouponCode.
 * 
 * Lofmp/CouponCode is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Lofmp\CouponCode\Model;

use Lofmp\CouponCode\Api\Data\RuleSearchResultsInterfaceFactory as SearchResultsInterfaceFactory;
use Lofmp\CouponCode\Model\ResourceModel\Rule as ResourceRule;
use Magento\Quote\Api\CouponManagementInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class RuleManagement
{
    private $collectionProcessor;

    protected $searchResultsFactory;
    /**
     * Quote repository.
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;
    /**
     * Quote repository.
     *
     * @var \Magento\Framework\ObjectManagerInterface $objectManager
     */
    protected $_objectManager;

    /**
     * @var \Lofmp\CouponCode\Model\RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var \Lofmp\CouponCode\Helper\Seller
     */
    protected $sellerHelper;

    /**
     * @var ResourceRule
     */
    protected $resource;

    /**
     * @var array|null
     */
    protected $_seller = [];

    /**
     * Constructs a rule read service object.
     *
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository Quote repository
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Lofmp\CouponCode\Model\RuleFactory $ruleFactory
     * @param \Lofmp\CouponCode\Helper\Seller $sellerHelper
     * @param ResourceRule $resource
     * @param CollectionProcessorInterface $collectionProcessor
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Lofmp\CouponCode\Model\RuleFactory $ruleFactory,
        \Lofmp\CouponCode\Helper\Seller $sellerHelper,
        ResourceRule $resource,
        CollectionProcessorInterface $collectionProcessor,
        SearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->_objectManager = $objectManager;
        $this->ruleFactory = $ruleFactory;
        $this->sellerHelper = $sellerHelper;
        $this->resource = $resource;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * Get Current seller info
     * 
     * @param int $customerId
     * @return \Lof\MarketPlace\Model\Seller|bool|null
     */
    protected function getCurrentSeller ($customerId) 
    {
        if (!isset($this->_seller[$customerId])) {
            $this->_seller[$customerId] = $this->sellerHelper->getActiveSeller($customerId);
        }
        return $this->_seller[$customerId];
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        $customerId,
        \Lofmp\CouponCode\Api\Data\RuleInterface $rule
    ) {

        if ($rule) {
            $seller = $this->getCurrentSeller($customerId);
            if (!$seller) {
                throw new NoSuchEntityException(__('seller with id "%1" does not exist.', $customerId));
            }
            try {
                /** @var $model \Magento\SalesRule\Model\Rule */
                $model = $this->ruleFactory->create();
                $model_sale_rule = $this->_objectManager->create('Magento\SalesRule\Model\Rule');

                if ($rule->getSimpleAction() == 'by_percent' && $rule->getDiscountAmount() != null) {
                    $data['simple_action'] = $rule->getSimpleAction();
                    $data['discount_amount'] = min(100, $rule->getDiscountAmount());
                }

                if ($rule->getConditions()) {
                    $data['conditions'] = $rule->getConditions();
                } 
                if ($rule->getActions()) {
                    $data['actions'] = $rule->getActions();
                }
                if($rule->getCouponsGenerated()){
                    $data['coupons_generated'] = $rule->getCouponsGenerated();
                }else{
                    $data['coupons_generated'] = 0;
                }
                $data['seller_id'] = $seller->getId();
                $data['coupons_length'] = $rule->getCodeLength();
                $data['coupon_type'] = '2';
                $data['use_auto_generation'] = '1';
                $data['name'] = $rule->getRuleName();
                $model_sale_rule->loadPost($data);
                $model->setData($data);
                $model_sale_rule->save();
                $model->setData('rule_id',$model_sale_rule->getId());
                $model->setData('name',$model_sale_rule->getName());
                $model->save();

                return $model->getDataModel();
            } catch (\Exception $exception) {
                throw new CouldNotSaveException(__(
                    'Could not save the rule: %1',
                    $exception->getMessage()
                ));
            }
        }
        throw new NoSuchEntityException(__('Rule Data is required.'));
    }

    /**
     * {@inheritdoc}
     */
    public function getById($customerId, $ruleId)
    {
        $seller = $this->getCurrentSeller($customerId);
        if (!$seller) {
            throw new NoSuchEntityException(__('seller with id "%1" does not exist.', $customerId));
        }
        $rule = $this->ruleFactory->create()->load((int)$ruleId);
        if (!$rule->getId()) {
            throw new NoSuchEntityException(__('Rule with id "%1" does not exist.', $ruleId));
        }
        if ($rule->getSellerId() != $seller->getId()) {
            throw new NoSuchEntityException(__('Rule with id "%1" is not available.', $ruleId));
        }
        return $rule->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        $customerId,
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $seller = $this->getCurrentSeller($customerId);
        if (!$seller) {
            throw new NoSuchEntityException(__('seller with id "%1" does not exist.', $customerId));
        }
        $collection = $this->ruleFactory->create()->getCollection();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue(), false);
                    continue;
                }
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }

        $collection->addFieldToFilter("seller_id", $seller->getId());

        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        
        $items = [];
        foreach ($collection as $model) {
            $items[] = $model->getDataModel();
        }
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setItems($items);
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        $customerId,
        \Lofmp\CouponCode\Api\Data\RuleInterface $rule
    ) {
        try {
            $seller = $this->getCurrentSeller($customerId);
            if (!$seller) {
                throw new NoSuchEntityException(__('seller with id "%1" does not exist.', $customerId));
            }
            if ($rule->getSellerId() != $seller->getId()) {
                throw new NoSuchEntityException(__('Rule with id "%1" is not available.', $ruleId));
            }
            $rule->getResource()->delete($rule);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Rule: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($customerId, $ruleId)
    {
        if($rule = $this->getById($ruleId)){
            $this->delete($customerId, $rule);
            return true;
        }else{
            throw new CouldNotDeleteException(__('Could not delete the Rule: %1'));
        }
    }
}
