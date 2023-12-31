<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://www.landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_FavoriteSeller
 * @copyright  Copyright (c) 2018 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\FavoriteSeller\Model;

use Lofmp\FavoriteSeller\Api\SellerCustomerRepositoryInterface;
use Lofmp\FavoriteSeller\Api\Data\SubscriptionCustomerInterface;
use Lofmp\FavoriteSeller\Model\SubscriptionFactory;
use Lofmp\FavoriteSeller\Model\ResourceModel\Subscription\CollectionFactory;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Lof\MarketPlace\Api\SellerRatingsRepositoryInterface;
use Lof\MarketPlace\Model\SellerFactory;
use Lof\MarketPlace\Helper\Data;
use Lof\MarketPlace\Helper\Seller;

class SubscriptionRepository implements \Lofmp\FavoriteSeller\Api\SellerCustomerRepositoryInterface
{

    /**
     * @var \Lofmp\FavoriteSeller\Model\SubscriptionFactory
     */
    protected $objectFactory;

    /**
     * @var \Lofmp\FavoriteSeller\Model\ResourceModel\Subscription\CollectionFactory
     */
    protected $collectionFactory;


    /**
     * @var SellerRatingsRepositoryInterface $sellerRatingRepo
     */
    protected $sellerRatingRepo;

    /**
     * @var SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var Seller
     */
    protected $seller;

    /**
     * @var Data
     */
    protected $data;
    /**
     * @param \Lofmp\FavoriteSeller\Model\SubscriptionFactory $objectFactory
     * @param \Lofmp\FavoriteSeller\Model\ResourceModel\Subscription\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Api\SearchResultsInterfaceFactory $searchResultsFactory
     * @param SellerRatingsRepositoryInterface $sellerRatingRepo
     * @param SellerFactory $sellerFactory
     * @param Data $data
     * @param Seller $seller
     */
    public function __construct(
        SubscriptionFactory $objectFactory,
        CollectionFactory $collectionFactory,
        SearchResultsInterfaceFactory $searchResultsFactory,
        SellerRatingsRepositoryInterface $sellerRatingRepo,
        SellerFactory $sellerFactory,
        Data $data,
        Seller $seller
    )
    {
        $this->objectFactory        = $objectFactory;
        $this->collectionFactory    = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->sellerRatingRepo = $sellerRatingRepo;
        $this->sellerFactory = $sellerFactory;
        $this->data = $data;
        $this->seller = $seller;
    }

    /**
     * @param \Lofmp\FavoriteSeller\Api\Data\SubscriptionCustomerInterface $object
     * @return \Lofmp\FavoriteSeller\Api\Data\SubscriptionCustomerInterface|mixed
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(SubscriptionCustomerInterface $object)
    {
        try
        {
            $object->save();
        }
        catch(\Exception $e)
        {
            throw new CouldNotSaveException(__($e->getMessage()));
        }
        return $object;
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id)
    {
        $object = $this->objectFactory->create();
        $object->load($id);
        if (!$object->getId()) {
            throw new NoSuchEntityException(__('Object with id "%1" does not exist.', $id));
        }
        return $object;
    }

    /**
     * @param \Lofmp\FavoriteSeller\Api\Data\SubscriptionCustomerInterface $object
     * @return bool|mixed
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(SubscriptionCustomerInterface $object)
    {
        try {
            $object->delete();
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * @param $id
     * @return bool|mixed
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById($id)
    {
        return $this->delete($this->getById($id));
    }

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return mixed
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $collection = $this->collectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                $fields[] = $filter->getField();
                $conditions[] = [$condition => $filter->getValue()];
            }
            if ($fields) {
                $collection->addFieldToFilter($fields, $conditions);
            }
        }
        $searchResults->setTotalCount($collection->getSize());
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
        $objects = [];
        foreach ($collection as $objectModel) {
            $objects[] = $objectModel;
        }
        $searchResults->setItems($objects);
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function addSeller($customer_id,$seller_id)
    {
        if ($seller_id == null) {
            return ;
        }
        $error = "";
        try {
            $subscriptionModel = $this->objectFactory->create();
            $subscriptionModel->setCustomerId($customer_id);
            $subscriptionModel->setSellerId($seller_id);
            $subscriptionModel->save();
        }
            catch (\Exception $e) {
            $error = $e->getMessage();
        }
        if (!$subscriptionModel->getId()) return ['favorite' => [],'error' => $error];
        return [
            'favorite' => $this->getById($subscriptionModel->getId())->getData(),
            'error' => $error
        ];
    }
    /**
     * @inheritDoc
     */
    public function removeSellers($customer_id,$seller_ids)
    {
        $error = [];
        $result = [];
        $subscriptionCollection = $this->collectionFactory->create();
        $subscriptions = $subscriptionCollection
                            ->addFieldToFilter('customer_id', $customer_id)
                            ->addFieldToFilter('seller_id', ['IN' => $seller_ids])
                            ->load();
        foreach ($subscriptions as $subscription) {
            try {
                $data = $subscription->getData();
                $subscription->delete();
                $result[] = $data;
            } catch (\Exception $e) {
                $error[] = $e->getMessage();
            }
        }
        return [
            'favorite' => $result,
            'error' => $error
        ];
    }

    /**
     * @inheritDoc
     */
    public function customerGetList($customer_id,SearchCriteriaInterface $criteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(SubscriptionCustomerInterface::CUSTOMER_ID,['eq' => $customer_id]);
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                $fields[] = $filter->getField();
                $conditions[] = [$condition => $filter->getValue()];
            }
            if ($fields) {
                $collection->addFieldToFilter($fields, $conditions);
            }
        }
        $searchResults->setTotalCount($collection->getSize());
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
        $objects = [];
        foreach ($collection as $objectModel) {
            $objectModel->setData("seller",$this->getSellerInfo($objectModel->getSellerId()));
            $objects[] = $objectModel;
        }
        $searchResults->setItems($objects);
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function sellerGetList($customer_id,SearchCriteriaInterface $criteria)
    {
        $check = $this->seller->checkSellerExist($customer_id);
        if($check == true){
            throw new NoSuchEntityException(__('Customer not Seller', $customer_id));
        }
        $sellerId = $this->data->getSellerByCustomerId($customer_id)->getId();

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(SubscriptionCustomerInterface::SELLER_ID,['eq' => $sellerId]);
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                $fields[] = $filter->getField();
                $conditions[] = [$condition => $filter->getValue()];
            }
            if ($fields) {
                $collection->addFieldToFilter($fields, $conditions);
            }
        }
        $searchResults->setTotalCount($collection->getSize());
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
        $searchResults->setItems($collection->getItems());
        return $searchResults;
    }

    /**
     * get seller informations
     * @param int $seller_id
     * @return array
     */
    protected function getSellerInfo($seller_id)
    {
        $sellerInfo = [];
        $sellerInfo['seller_id'] = $seller_id;
        /**
         * @var \Lof\MarketPlace\Model\Seller
         */
        $sellerModel = $this->sellerFactory->create()->load($seller_id);
        $sellerInfo['thumbnail'] = $sellerModel->getThumbnailUrl() ;
        $sellerInfo['name'] = $sellerModel->getName() ;
        $sellerInfo['description'] = $sellerModel->getDescription();
        $sellerInfo['url'] = $sellerModel->getUrl();
        $sellerInfo['rating'] = $this->sellerRatingRepo->getSummaryRatingsBySellerId($seller_id)->__toArray();
        return $sellerInfo;
    }

    /**
     * @inheritDoc
     */
    public function checkSeller($customer_id,$seller_id)
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(SubscriptionCustomerInterface::SELLER_ID, $seller_id)
        ->addFieldToFilter(SubscriptionCustomerInterface::CUSTOMER_ID,$customer_id);
        return (bool)$collection->getSize();
    }
}
