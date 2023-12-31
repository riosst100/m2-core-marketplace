<?php
/**
 * LandofCoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   LandofCoder
 * @package    Lofmp_CouponCode
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\CouponCode\Model;

use Lofmp\CouponCode\Api\CouponRepositoryInterface;
use Lofmp\CouponCode\Api\Data\CouponSearchResultsInterfaceFactory;
use Lofmp\CouponCode\Api\Data\CouponInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Lofmp\CouponCode\Model\ResourceModel\Coupon as ResourceCoupon;
use Lofmp\CouponCode\Model\ResourceModel\Coupon\CollectionFactory as CouponCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;


class CouponRepository implements couponRepositoryInterface
{

    protected $resource;

    protected $couponFactory;

    protected $couponCollectionFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $dataCouponFactory;

    private $storeManager;

    protected $_objectManager;


    /**
     * @param ResourceCoupon $resource
     * @param CouponFactory $couponFactory
     * @param CouponInterfaceFactory $dataCouponFactory
     * @param CouponCollectionFactory $couponCollectionFactory
     * @param CouponSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceCoupon $resource,
        CouponFactory $couponFactory,
        CouponInterfaceFactory $dataCouponFactory,
        CouponCollectionFactory $couponCollectionFactory,
        CouponSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Lofmp\CouponCode\Helper\Data $helper,
        \Lofmp\CouponCode\Helper\Generator $generateHelper
    ) {
        $this->resource                 = $resource;
        $this->couponFactory            = $couponFactory;
        $this->couponCollectionFactory  = $couponCollectionFactory;
        $this->searchResultsFactory     = $searchResultsFactory;
        $this->dataObjectHelper         = $dataObjectHelper;
        $this->dataCouponFactory        = $dataCouponFactory;
        $this->dataObjectProcessor      = $dataObjectProcessor;
        $this->storeManager             = $storeManager;
        $this->_objectManager           = $objectManager;
        $this->_couponHelper            = $helper;
        $this->couponGenerator          = $generateHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function save() {
        $requestHttp = $this->_objectManager->create('\Magento\Framework\App\Request\Http');
        $conditions = $requestHttp->getParams();
        $customer_email = isset($conditions["customer_email"])? trim($conditions["customer_email"]) : null;
        $rule_id = isset($conditions["rule_id"])? trim($conditions["rule_id"]) : null;

        try {
            if($customer_email && $rule_id){
                $couponRuleData = $this->_couponHelper->getCouponRuleData($rule_id);
                $ruleId = (int)$couponRuleData->getRuleId();
                if($ruleId) {
                    $limit_time_generated_coupon = (int)$couponRuleData->getLimitGenerated();
                    $coupon_collection = $this->_objectManager->create('Lofmp\CouponCode\Model\Coupon')->getCollection();
                    $number_generated_coupon = (int)$coupon_collection->getTotalByEmail($customer_email, $rule_id);

                    if($limit_time_generated_coupon <= 0 || ($number_generated_coupon < $limit_time_generated_coupon)) {//check number coupons was generated for same email address
                        $this->couponGenerator->setCustomerEmail($customer_email);
                        $coupon_alias = "redeem-".md5($customer_email);
                        $this->couponGenerator->setCouponAlias($coupon_alias);
                    }
                    $coupon_exists = false;
                    $coupon_model = $this->_objectManager->create('Lofmp\CouponCode\Model\Coupon')->getCouponByAlias($coupon_alias);
                    if($coupon_model->getId()){
                        $coupon_exists = true;
                    }
                    if(!$coupon_exists){
                        $coupon_code = $this->couponGenerator->generateCoupon($rule_id);
                        $res = ["coupon_code" => $coupon_code];
                        return json_encode($res);
                    }
                }
            }
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the coupon: %1',
                $exception->getMessage()
            ));
        }
        return $coupon;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($couponId)
    {
        $coupon = $this->couponFactory->create();
        $coupon->getResource()->load($coupon, $couponId);
        if (!$coupon->getId()) {
            throw new NoSuchEntityException(__('Coupon with id "%1" does not exist.', $couponId));
        }
        return $coupon;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->couponCollectionFactory->create();
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

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setItems($collection->getItems());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function getMyList(
        int $customerId,
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->couponCollectionFactory->create();
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

        $collection->addFieldToFilter("customer_id", $customerId);

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

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setItems($collection->getItems());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Lofmp\CouponCode\Api\Data\CouponInterface $coupon
    ) {
        try {
            $coupon->getResource()->delete($coupon);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Coupon: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($couponId)
    {
        return $this->delete($this->getById($couponId));
    }

}
