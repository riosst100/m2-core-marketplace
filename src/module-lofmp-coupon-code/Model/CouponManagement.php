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

use Lofmp\CouponCode\Api\Data\CouponSearchResultsInterfaceFactory as SearchResultsInterfaceFactory;
use Lofmp\CouponCode\Model\ResourceModel\Coupon as ResourceCoupon;
use Lofmp\CouponCode\Api\CouponManagementInterface;
use Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory;
use Lof\MarketPlace\Model\Seller;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Coupon management object.
 */
class CouponManagement implements CouponManagementInterface
{
    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var SearchResultsInterfaceFactory
     */
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
     * @var \Lofmp\CouponCode\Model\CouponFactory
     */
    protected $couponFactory;

    /**
     * @var \Lofmp\CouponCode\Helper\Seller
     */
    protected $sellerHelper;

    /**
     * @var ResourceCoupon
     */
    protected $resource;

    /**
     * @var \Lofmp\CouponCode\Helper\Data
     */
    protected $helperData;

    /**
     * @var CollectionFactory
     */
    protected $sellerCollectionFactory;

    /**
     * @var array|null
     */
    protected $_seller = [];

    /**
     * @var \Lof\MarketPlace\Model\SellerFactory
     */
    protected $sellerFactory;

    /**
     * Constructs a coupon read service object.
     *
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository Quote repository
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Lofmp\CouponCode\Model\CouponFactory $couponFactory
     * @param \Lofmp\CouponCode\Helper\Seller $sellerHelper
     * @param \Lofmp\CouponCode\Helper\Data $helperData
     * @param ResourceCoupon $resource
     * @param CollectionProcessorInterface $collectionProcessor
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionFactory $sellerCollectionFactory\
     * @param \Lof\MarketPlace\Model\SellerFactory
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Lofmp\CouponCode\Model\CouponFactory $couponFactory,
        \Lofmp\CouponCode\Helper\Seller $sellerHelper,
        \Lofmp\CouponCode\Helper\Data $helperData,
        ResourceCoupon $resource,
        CollectionProcessorInterface $collectionProcessor,
        SearchResultsInterfaceFactory $searchResultsFactory,
        CollectionFactory $sellerCollectionFactory,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->_objectManager = $objectManager;
        $this->couponFactory = $couponFactory;
        $this->sellerHelper = $sellerHelper;
        $this->_helperData = $helperData;
        $this->resource = $resource;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->sellerCollectionFactory = $sellerCollectionFactory;
        $this->sellerFactory = $sellerFactory;
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
     * Get seller info by url
     *
     * @param string $sellerUrl
     * @return \Lof\MarketPlace\Model\Seller|bool|null
     */
    protected function getSellerByUrl ($sellerUrl)
    {
        if (!isset($this->_seller[$sellerUrl])) {
            $seller = $this->sellerCollectionFactory->create()
                                            ->addFieldToFilter("url_key", $sellerUrl)
                                            ->addFieldToFilter("status", Seller::STATUS_ENABLED)
                                            ->getFirstItem();
            $this->_seller[$sellerUrl] = $seller && $seller->getId() ? $seller : null;
        }
        return $this->_seller[$sellerUrl];
    }


    /**
     * {@inheritdoc}
     */
    public function getById($customerId, $id)
    {
        $seller = $this->getCurrentSeller($customerId);
        if (!$seller) {
            throw new NoSuchEntityException(__('seller with id "%1" does not exist.', $customerId));
        }
        $couponModel = $this->couponFactory->create();
        $this->resource->load($couponModel, $id);
        if (!$couponModel->getId() || $couponModel->getSellerId() !== $seller->getId()) {
            throw new NoSuchEntityException(__('Coupon with id "%1" does not exist.', $id));
        }
        return $couponModel;
    }

    /**
     * {@inheritdoc}
     */
    public function get($cartId)
    {
        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        return $quote->getCouponCode();
    }

    /**
     * {@inheritdoc}
     */
    public function set($cartId, $couponCode)
    {
        $flag = false;
        //get infor by couponcode
        $coupon_collection = $this->couponFactory->create()->getCollection();
        $data = $coupon_collection->getByCouponCode($couponCode);
        if(count($data) > 0){
            $rule = $coupon_collection->getRule($data["rule_id"]);
            //get checkout info
            $customer_checkout = $this->_objectManager->get('\Magento\Checkout\Model\Session')->getQuote()->getCustomer();
            $customer_email = $customer_checkout->getEmail();
            $customer_id = $customer_checkout->getId();
            if(isset($rule['is_check_email']) && $rule["is_check_email"]){
                if((isset($data["email"]) && $data["email"] == $customer_email) || (isset($data["customer_id"]) && $data["customer_id"] == $customer_id))
                    $flag = true;
            } else{
                $flag = true;
            }
        }
        if($flag){
            /** @var  \Magento\Quote\Model\Quote $quote */
            $quote = $this->quoteRepository->getActive($cartId);
            if (!$quote->getItemsCount()) {
                throw new NoSuchEntityException(__('Cart %1 doesn\'t contain products', $cartId));
            }
            $quote->getShippingAddress()->setCollectShippingRates(true);

            try {
                $quote->setCouponCode($couponCode);
                $quote->collectTotals()->setCouponCode($couponCode);
                $this->quoteRepository->save($quote->collectTotals());
            } catch (\Exception $e) {
                throw new CouldNotSaveException(__('Could not apply coupon code'));
            }
            if ($quote->getCouponCode() != $couponCode) {
                throw new NoSuchEntityException(__('Coupon code is not valid'));
            }
        }else {
            throw new NoSuchEntityException(__('Coupon code is not valid2'));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($cartId)
    {
        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('Cart %1 doesn\'t contain products', $cartId));
        }
        $quote->getShippingAddress()->setCollectShippingRates(true);
        try {
            $quote->setCouponCode('');
            $this->quoteRepository->save($quote->collectTotals());
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete coupon code'));
        }
        if ($quote->getCouponCode() != '') {
            throw new CouldNotDeleteException(__('Could not delete coupon code'));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getCouponAlias($customerId, $alias)
    {
        $seller = $this->getCurrentSeller($customerId);
        if (!$seller) {
            throw new NoSuchEntityException(__('seller with id "%1" does not exist.', $customerId));
        }
        $coupon_model = $this->couponFactory->create()->getCouponByAlias($alias);
        if (!$coupon_model->getId() || ($coupon_model->getSellerId() != $seller->getId())) {
            throw new NoSuchEntityException(__('Not found coupon code data for alias %1', $alias));
        }
        return $coupon_model;
    }

    /**
     * {@inheritdoc}
     */
    public function getCouponByConditions($customerId, \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        return $this->getListCouponsByType("all", $customerId, $searchCriteria,false);
    }

    /**
     * {@inheritdoc}
     */
    public function getExpiredCoupons($customerId, \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        return $this->getListCouponsByType("expired", $customerId, $searchCriteria,false);
    }

    /**
     * {@inheritdoc}
     */
    public function getUsedCoupons($customerId, \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        return $this->getListCouponsByType("used", $customerId, $searchCriteria,false);
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableCoupons($customerId, \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        return $this->getListCouponsByType("available", $customerId, $searchCriteria,false);
    }

    /**
     * {@inheritdoc}
     */
    public function getCouponByEmail($customerId, $email, $page = 1, $limit = 20)
    {
        $seller = $this->getCurrentSeller($customerId);
        if (!$seller) {
            throw new NoSuchEntityException(__('seller with id "%1" does not exist.', $customerId));
        }
        $collection = $this->couponFactory->create()->getCollection();
        $collection->addFieldToFilter("seller_id", $seller->getId());
        $collection->addFieldToFilter("email", $email);

        $collection->setCurPage((int)$page);
        $collection->setPageSize((int)$limit);

        $searchResults = $this->searchResultsFactory->create();

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model->getData();
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function getCouponByRuleId($customerId, $ruleId, $page = 1, $limit = 20)
    {
        $seller = $this->getCurrentSeller($customerId);
        if (!$seller) {
            throw new NoSuchEntityException(__('seller with id "%1" does not exist.', $customerId));
        }
        $collection = $this->couponFactory->create()->getCollection();
        $collection->addFieldToFilter("seller_id", $seller->getId());
        $collection->addFieldToFilter("rule_id", (int)$ruleId);

        $collection->setCurPage((int)$page);
        $collection->setPageSize((int)$limit);

        $searchResults = $this->searchResultsFactory->create();

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model->getData();
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function putCoupon($customerId, \Lofmp\CouponCode\Api\Data\CouponInterface $coupon){
        $seller = $this->getCurrentSeller($customerId);
        if (!$seller) {
            throw new NoSuchEntityException(__('seller with id "%1" does not exist.', $customerId));
        }
        $coupon->setSellerId((int)$seller->getId());
        $couponModel = $this->couponFactory->create()->setData($coupon);

        try {
            $this->resource->save($couponModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the Coupon Code: %1',
                $exception->getMessage()
            ));
        }
        return $couponModel;

    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        $customerId,
        \Lofmp\CouponCode\Api\Data\CouponInterface $coupon
    ) {
        try {
            $seller = $this->getCurrentSeller($customerId);
            if (!$seller) {
                throw new NoSuchEntityException(__('seller with id "%1" does not exist.', $customerId));
            }
            if ($coupon->getSellerId() != $seller->getId()) {
                throw new NoSuchEntityException(__('You dont have permission to delete coupon code'));
            }
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
    public function deleteById($customerId, $couponId)
    {
        return $this->delete($customerId, $this->getById($customerId, $couponId));
    }

    /**
     * Get list coupons by type, set $isPublicRequest is true if you want to get public coupons
     * @param string $type
     * @param int $customerId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @param bool $isPublicRequest
     */
    protected function getListCouponsByType($type, $customerId, \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria,bool $isPublicRequest)
    {
        if  (!$isPublicRequest) {
            $seller = $this->getCurrentSeller($customerId);
            if (!$seller) {
                throw new NoSuchEntityException(__('seller with id "%1" does not exist.', $customerId));
            }
        }
        $collection = $this->couponFactory->create()->getCollection();

        $this->collectionProcessor->process($searchCriteria, $collection);

        if (!$isPublicRequest) {
            $collection->addFieldToFilter("seller_id", $seller->getId());
        } else {
            $collection->addFieldToFilter("is_public", Coupon::STATUS_PUBLIC);
        }
        if ($type != "all") {
            $collection->joinSalesruleCoupon();
        }
        $today = $this->_helperData->getTimezoneDateTime();
        switch ($type) {
            case "expired":
                $collection->addFieldToFilter("expiration_date", ["lteq" => $today]);
                break;
            case "available":
                $collection->addFieldToFilter('expiration_date', [
                    ['gt' => $today],
                    ['null' => true]
                ]);
                $collection->getSelect()
                            ->where('`times_used` < `usage_per_customer` OR `usage_per_customer` = 0');
                //$collection->addFieldToFilter("times_used", ["eq" => 0]);
                break;
            case "used":
                $collection->addFieldToFilter("times_used", ["gt" => 0]);
                break;
            case "all":
            default:
                break;
        }

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        $items = [];
        foreach ($collection as $model) {
            if ($isPublicRequest) {
                $model->setCustomerId(0);
                $model->setEmail("");
            }
            $result = $model->getData();
            $result = $this->AddExtraData($result, $model);
            $items[] = $result;
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * Add seller infomations and add description for coupon
     * @param array $result
     * @param \Lofmp\CouponCode\Model\Coupon $model
     * @return array
     */

    protected function AddExtraData($result, $model)
    {
        $result[\Lofmp\CouponCode\Api\Data\CouponInterface::DESCRIPTION] = $model->getDescription();
        if  ($model->getSellerId()) {
            $seller_info = $this->sellerFactory->create()->load($model->getSellerId());
            $result['seller']['seller_id'] = $seller_info->getId();
            $result['seller']['shop_title'] = $seller_info->getShopTitle();
            $result['seller']['thumbnail'] = $seller_info->getThumbnailUrl();
            $result['seller']['url'] = $seller_info->getUrl();
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublicCoupons(string $sellerUrl, \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $seller = $this->getSellerByUrl($sellerUrl);
        if (!$seller) {
            throw new NoSuchEntityException(__('seller with url "%1" does not exist.', $sellerUrl));
        }
        $collection = $this->couponFactory->create()->getCollection();

        $this->collectionProcessor->process($searchCriteria, $collection);

        $collection->addFieldToFilter("seller_id", $seller->getId());
        $collection->addFieldToFilter("is_public", Coupon::STATUS_PUBLIC);

        $items = [];
        $collection->load();
        foreach ($collection as $item) {
            /** @var Coupon $item */
            $item->setCustomerId(0);
            $item->setEmail("");
            $result = $item->getData();
            $result = $this->AddExtraData($result,$item);
            $items[] = $result; 
        }
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }


    /**
     * {@inheritdoc}
     */
    public function getAllPublicCoupons(string $type, \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->getListCouponsByType($type,0,$searchCriteria,true);
        return $searchResults;
    }
}
