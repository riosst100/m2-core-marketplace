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

use Lofmp\CouponCode\Api\Data\LogSearchResultsInterfaceFactory;
use Lofmp\CouponCode\Api\Data\LogInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Lofmp\CouponCode\Model\ResourceModel\Log as ResourceLog;
use Lofmp\CouponCode\Model\ResourceModel\Log\CollectionFactory as LogCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class LogManagement
{
	protected $_registry;
	protected $logFactory;
	protected $_couponHelper;
	protected $orderRepository;
	protected $_priceCurrency;

	protected $resource;

    protected $searchResultsFactory;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $dataLogFactory;

    private $storeManager;


	 /**
     * @var array|null
     */
    protected $_seller = [];

	 public function __construct(
        \Magento\Framework\Registry $registry,
        \Lofmp\CouponCode\Model\LogFactory $logFactory,
        \Lofmp\CouponCode\Helper\Data $helperData,
		ResourceLog $resource,
        \Magento\Sales\Api\Data\OrderInterface $orderRepository,
        \Magento\Store\Model\StoreManagerInterface $priceCurrency,
		LogSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->_registry = $registry;
        $this->logFactory = $logFactory;
        $this->_couponHelper = $helperData;
        $this->orderRepository = $orderRepository;
        $this->_priceCurrency = $priceCurrency;
		$this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataLogFactory = $dataLogFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->resource = $resource;
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
    public function getLog($customerId, $coupon_code, $email, $page = 1, $limit = 20)
    {
    	if(!$this->_couponHelper->getConfig('general_settings/show') || !$this->_couponHelper->getConfig('general_settings/allow_track_log')) {
            return [];
        }
		try {
            $seller = $this->getCurrentSeller($customerId);
            if (!$seller) {
                throw new NoSuchEntityException(__('seller with id "%1" does not exist.', $customerId));
            }

			$coupon_code = trim($coupon_code);
			$customer_email = trim($email);
			if($coupon_code && $customer_email) {
				$collection = $this->logFactory->create()->getCollection();
				$collection = $collection->addFieldToFilter("coupon_code", $coupon_code)
										->addFieldToFilter("email_address", $customer_email);

				$collection->getSelect()
							->join(
								['lofmp_couponcode_rule' => $this->logFactory->create()->getResource()->getTable("lofmp_couponcode_rule")],
								'main_table.rule_id = lofmp_couponcode_rule.rule_id',
								[
									'seller_id'
								]
							)
							->where('lofmp_couponcode_rule.seller_id = ?', (int)$seller->getId())
							->group(
								'main_table.rule_id'
							);
				$collection->setCurPage($page);
				$collection->setPageSize($limit);

				$searchResults = $this->searchResultsFactory->create();
				$searchResults->setTotalCount($collection->getSize());
				$searchResults->setItems($collection->getItems());
				return $searchResults;
			}

        } catch (\Exception $exception) {
            throw new NoSuchEntityException(__(
                'Could not found the Coupon Log: %1',
                $exception->getMessage()
            ));
        }

    }

	/**
     * {@inheritdoc}
     */
    public function getById($customerId, $logId)
    {
		$seller = $this->getCurrentSeller($customerId);
		if (!$seller) {
			throw new NoSuchEntityException(__('seller with id "%1" does not exist.', $customerId));
		}
        $log = $this->logFactory->create();
        $log->getResource()->load($log, $logId);
        if (!$log->getId()) {
            throw new NoSuchEntityException(__('Log with id "%1" does not exist.', $logId));
        }
        return $log;
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
        $collection = $this->logFactory->create()->getCollection();
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
}
