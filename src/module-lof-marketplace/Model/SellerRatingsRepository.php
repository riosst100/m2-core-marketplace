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
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Model;

use Lof\MarketPlace\Api\Data\RatingInterfaceFactory;
use Lof\MarketPlace\Api\Data\SummaryRatingInterfaceFactory;
use Lof\MarketPlace\Api\Data\RatingSearchResultsInterfaceFactory;
use Lof\MarketPlace\Api\SellerRatingsRepositoryInterface;
use Lof\MarketPlace\Model\ResourceModel\Rating as ResourceModelRating;
use Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory as SellerCollectionFactory;
use Lof\MarketPlace\Model\ResourceModel\Rating\CollectionFactory as RatingCollectionFactory;
use Lof\MarketPlace\Helper\Data;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SellerRatingsRepository implements SellerRatingsRepositoryInterface
{
    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;

    /**
     * @var RatingInterfaceFactory
     */
    protected $dataFactory;

    /**
     * @var RatingSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var ExtensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;

    /**
     * @var ResourceModelRating
     */
    protected $resource;

    /**
     * @var RatingCollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var RatingFactory
     */
    protected $modelFactory;

    /**
     * @var SellerCollectionFactory
     */
    protected $sellerCollectionFactory;

    /**
     * @var SummaryRatingInterfaceFactory
     */
    protected $summaryRatingData;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * SellerRatingsRepository constructor.
     *
     * @param ResourceModelRating $resource
     * @param RatingFactory $modelFactory
     * @param RatingInterfaceFactory $dataFactory
     * @param RatingCollectionFactory $collectionFactory
     * @param RatingSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param SellerCollectionFactory $sellerCollectionFactory
     * @param SummaryRatingInterfaceFactory $summaryRatingData
     * @param Data $helperData
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ResourceModelRating $resource,
        RatingFactory $modelFactory,
        RatingInterfaceFactory $dataFactory,
        RatingCollectionFactory $collectionFactory,
        RatingSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        SellerCollectionFactory $sellerCollectionFactory,
        SummaryRatingInterfaceFactory $summaryRatingData,
        Data $helperData
    ) {
        $this->resource = $resource;
        $this->modelFactory = $modelFactory;
        $this->dataFactory = $dataFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->sellerCollectionFactory = $sellerCollectionFactory;
        $this->summaryRatingData = $summaryRatingData;
        $this->helperData = $helperData;
    }

    /**
     * @param int $sellerId
     * @return mixed|array
     */
    public function getSellerRatings($sellerId)
    {
        $sellerrating = $this->modelFactory->create();
        $data = $sellerrating->getCollection()->addFieldToFilter('seller_id', $sellerId)->getData();
        foreach ($data as $value) {
            $value["rating_id"] = (int)$value["rating_id"];
            $value["seller_id"] = (int)$value["seller_id"];
            $value["customer_id"] = (int)$value["customer_id"];
            $value["rate1"] = (int)$value["rate1"];
            $value["rate2"] = (int)$value["rate2"];
            $value["rate3"] = (int)$value["rate3"];
            $value["rating"] = (int)$value["rating"];
        }
        return [
            "total_count" => count($data),
            "items" => $data
        ];
    }

    /**
     * @param int $id
     * @return mixed|array
     */
    public function getSellerRatingsById($id)
    {
        $sellerrating = $this->modelFactory->create();
        $data = $sellerrating->getCollection()->addFieldToFilter('seller_id', $id)->getData();
        foreach ($data as &$value) {
            $value["rating_id"] = (int)$value["rating_id"];
            $value["seller_id"] = (int)$value["seller_id"];
            $value["customer_id"] = (int)$value["customer_id"];
            $value["rate1"] = (int)$value["rate1"];
            $value["rate2"] = (int)$value["rate2"];
            $value["rate3"] = (int)$value["rate3"];
            $value["rating"] = (int)$value["rating"];
        }

        $res = [
            "code" => 0,
            "message" => "Get data success",
            "result" => [
                "rating" => $data
            ]
        ];

        return $res;
    }

    /**
     * {@inheritdoc}
     */
    public function getById(
        int $ratingId
    ) {
        $sellerRating = $this->modelFactory->create();
        $this->resource->load($sellerRating, $ratingId);
        $sellerRating->setEmail(null);
        $sellerRating->setAdminNote(null);
        return $sellerRating;
    }

    /**
     * {@inheritdoc}
     */
    public function get(
        int $ratingId
    ) {
        $sellerRating = $this->modelFactory->create();
        $this->resource->load($sellerRating, $ratingId);
        if (!$sellerRating->getId()) {
            throw new NoSuchEntityException(__('Rating with id "%1" does not exist.', $ratingId));
        }
        return $sellerRating;
    }

    /**
     * {@inheritdoc}
     */
    public function getMyRating(
        int $customerId,
        int $ratingId
    ) {
        $seller = $this->getSellerByCustomer($customerId);
        if ($seller && $seller->getId()) {
            $sellerRating = $this->modelFactory->create();
            $this->resource->load($sellerRating, $ratingId);
            if (!$sellerRating->getId() || $sellerRating->getSellerId() != $seller->getId()) {
                throw new NoSuchEntityException(__('Rating with id "%1" does not exist.', $ratingId));
            }
            return $sellerRating;
        } else {
            throw new NoSuchEntityException(__('Seller does not exist.'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getListByUrl(
        string $sellerUrl,
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $seller = $this->getSellerByUrl($sellerUrl);

        if ($seller && $seller->getId()) {
            return $this->getList((int)$seller->getId(), $criteria);
        } else {
            throw new NoSuchEntityException(__('Seller with seller Url "%1" does not exist.', $sellerUrl));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        int $sellerId,
        \Magento\Framework\Api\SearchCriteriaInterface $criteria,
        $showEmail = false
    ) {
        $collection = $this->collectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        $collection->addFieldToFilter('seller_id', $sellerId);
        $collection->addFieldToFilter('status', Rating::STATUS_ACCEPT);

        $items = [];
        foreach ($collection->getItems() as $item) {

            if (!$showEmail) {
                $item->setEmail(null);
            }
            $item->setAdminNote(null);
            $items[] = $item;
        }

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function getSummaryRatingsBySellerId(
        int $sellerId
    )
    {
        $count = $total_rate = 0;
        $rate1 = $rate2 = $rate3 = $rate4 = $rate5 = 0;
        $ratingCollection = $this->getRatingCollection($sellerId);
        $totalCount = $ratingCollection->getSize();

        foreach ($ratingCollection as $rating) {
            if ($rating->getData('rate1') > 0) {
                $count++;
                $total_rate = $total_rate + $rating->getData('rate1');
                if ($rating->getData('rate1') == 1) {
                    $rate1++;
                } elseif ($rating->getData('rate1') == 2) {
                    $rate2++;
                } elseif ($rating->getData('rate1') == 3) {
                    $rate3++;
                } elseif ($rating->getData('rate1') == 4) {
                    $rate4++;
                } elseif ($rating->getData('rate1') == 5) {
                    $rate5++;
                }
            }

            if ($rating->getData('rate2') > 0) {
                $count++;
                $total_rate = $total_rate + $rating->getData('rate2');
                if ($rating->getData('rate2') == 1) {
                    $rate1++;
                } elseif ($rating->getData('rate2') == 2) {
                    $rate2++;
                } elseif ($rating->getData('rate2') == 3) {
                    $rate3++;
                } elseif ($rating->getData('rate2') == 4) {
                    $rate4++;
                } elseif ($rating->getData('rate2') == 5) {
                    $rate5++;
                }
            }

            if ($rating->getData('rate3') > 0) {
                $count++;
                $total_rate = $total_rate + $rating->getData('rate3');
                if ($rating->getData('rate3') == 1) {
                    $rate1++;
                } elseif ($rating->getData('rate3') == 2) {
                    $rate2++;
                } elseif ($rating->getData('rate3') == 3) {
                    $rate3++;
                } elseif ($rating->getData('rate3') == 4) {
                    $rate4++;
                } elseif ($rating->getData('rate3') == 5) {
                    $rate5++;
                }
            }
        }
        if ($count > 0) {
            $average = ($total_rate / $count);
        } else {
            $average = 0;
        }

        $perRate = round($average / 5 * 100);

        $summaryData = $this->summaryRatingData->create();
        $summaryData->setCount($count);
        $summaryData->setTotalRate($total_rate);
        $summaryData->setAverage($average);
        $summaryData->setTotalCount($totalCount);
        $summaryData->setPerRate($perRate);
        $summaryData->setRateOne($rate1);
        $summaryData->setRateTwo($rate2);
        $summaryData->setRateThree($rate3);
        $summaryData->setRateFour($rate4);
        $summaryData->setRateFive($rate5);

        return $summaryData;
    }

    /**
     * {@inheritdoc}
     */
    public function getSummaryRatings(
        string $sellerUrl
    )
    {
        $seller = $this->getSellerByUrl($sellerUrl);

        if ($seller && $seller->getId()) {
            return $this->getSummaryRatingsBySellerId($seller->getId());
        } else {
            throw new NoSuchEntityException(__('Seller with seller Url "%1" does not exist.', $sellerUrl));
        }
    }

    /**
     * @param int $sellerId
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    public function getRatingCollection($sellerId)
    {
        return $this->collectionFactory->create()
                ->addFieldToFilter('seller_id', $sellerId);
    }

    /**
     * Get seller by Url
     *
     * @param string $sellerUrl
     * @return \Lof\MarketPlace\Model\Seller
     */
    protected function getSellerByUrl($sellerUrl)
    {
        $collection = $this->sellerCollectionFactory->create();
        $seller = $collection->addFieldToFilter("url_key", $sellerUrl)
                    ->addFieldToFilter("status", Seller::STATUS_ENABLED)
                    ->getFirstItem();
        return $seller;
    }

    /**
     * Get seller by customerId
     *
     * @param int $customerId
     * @return \Lof\MarketPlace\Model\Seller
     */
    protected function getSellerByCustomer(int $customerId)
    {
        $collection = $this->sellerCollectionFactory->create();
        $seller = $collection->addFieldToFilter("customer_id", $customerId)
                    ->addFieldToFilter("status", Seller::STATUS_ENABLED)
                    ->getFirstItem();
        return $seller;
    }

    /**
     * {@inheritdoc}
     */
    public function getRatingsList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    )
    {
        $collection = $this->collectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function sellerGetList(
        int $customerId,
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    )
    {
        $seller = $this->getSellerByCustomer($customerId);

        if ($seller && $seller->getId()) {
            return $this->getList((int)$seller->getId(), $criteria, true);
        } else {
            throw new NoSuchEntityException(__('Seller does not exist.'));
        }
    }

    /**
     * @inheritdoc
     */
    public function save(\Lof\MarketPlace\Api\Data\RatingInterface $rating)
    {
        try {
            $statusAvailable = [Rating::STATUS_ACCEPT, Rating::STATUS_PENDING];
            $status = $rating->getStatus();
            if (!$this->validateRating($rating) || !in_array($status, $statusAvailable) ) {
                throw new CouldNotSaveException(__(
                    'Could not save the rating: rating data is not valid.'
                ));
            }
            /** @var \Lof\MarketPlace\Model\Rating $rating */
            $this->resource->save($rating);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the rating: %1',
                $exception->getMessage()
            ));
        }
        return $rating;
    }

    /**
     * @inheritdoc
     */
    public function saveMyRating(int $customerId, \Lof\MarketPlace\Api\Data\RatingInterface $rating)
    {
        $seller = $this->getSellerByCustomer($customerId);
        if ($seller && $seller->getId()) {
            try {
                $statusAvailable = [Rating::STATUS_ACCEPT, Rating::STATUS_PENDING];
                $status = $rating->getStatus();
                if (($rating->getSellerId() != $seller->getId()) || !$rating->getRatingId() || !in_array($status, $statusAvailable)) {
                    throw new CouldNotSaveException(__(
                        'Could not save the rating: rating data is not valid, missing seller_id, rating id or not validate rating data.'
                    ));
                }
                $foundRating = $this->get((int)$rating->getRatingId());
                $updateData = [
                    "answer" => strip_tags($rating->getAnswer()),
                    "status" => $status,
                    "is_recommended" => $rating->getIsRecommended(),
                    "verified_buyer" => $rating->getVerifiedBuyer()
                ];
                /** @var \Lof\MarketPlace\Model\Rating $foundRating*/
                $foundRating->setData($updateData);
                $this->resource->save($foundRating);
                return $foundRating;
            } catch (\Exception $exception) {
                throw new CouldNotSaveException(__(
                    'Could not save the rating: %1',
                    $exception->getMessage()
                ));
            }
        } else {
            throw new NoSuchEntityException(__('Seller does not exist.'));
        }
    }

    /**
     * @inheritDoc
     */
    public function delete(\Lof\MarketPlace\Api\Data\RatingInterface $rating)
    {
        try {
            $ratingModel = $this->modelFactory->create();
            $this->resource->load($ratingModel, $rating->getRatingId());
            $this->resource->delete($ratingModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the seller rating: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById(int $ratingId)
    {
        return $this->delete($this->get($ratingId));
    }

    /**
     * @inheritdoc
     */
    public function updateRating(int $customerId, int $ratingId, string $type)
    {
        if ($this->helperData->getConfig("general_settings/allow_update_rating")) {
            try {
                $actions = ["plus", "minus", "report"];
                $rating = $this->getById($ratingId);
                if (!$rating->getRatingId() || !in_array($type, $actions) ) {
                    throw new CouldNotSaveException(__(
                        'Rating is not exists or action type is not available. Only support Action Type: plus, minus, report.'
                    ));
                }
                /** @var \Lof\MarketPlace\Model\Rating $rating */
                $plusReview = (int)$rating->getPlusReview();
                $minusReview = (int)$rating->getMinusReview();
                $reportAbuse = (int)$rating->getReportAbuse();
                switch ($type) {
                    case "plus":
                        $plusReview += 1;
                    break;
                    case "minus":
                        $minusReview += 1;
                    break;
                    case "report":
                        $reportAbuse += 1;
                    break;
                }
                $updateData = [
                    "plus_review" => $plusReview,
                    "minus_review" => $minusReview,
                    "report_abuse" => $reportAbuse
                ];
                $rating->setData($updateData);
                $this->resource->save($rating);
                return $rating;
            } catch (\Exception $exception) {
                throw new CouldNotSaveException(__(
                    'Could not save the rating: %1',
                    $exception->getMessage()
                ));
            }
        } else {
            throw new CouldNotSaveException(__(
                'The feature is not available at now.'
            ));
        }
    }

    /**
     * validate data
     *
     * @param \Lof\MarketPlace\Api\Data\RatingInterface $rating
     * @return bool
     */
    protected function validateRating(\Lof\MarketPlace\Api\Data\RatingInterface $rating)
    {
        $flag = true;
        if (empty($rating->getSellerId())) {
            $flag = false;
        }
        if (empty($rating->getCustomerId()) && empty($rating->getEmail())) {
            $flag = false;
        }
        if (empty($rating->getTitle())) {
            $flag = false;
        }
        if (empty($rating->getDetail())) {
            $flag = false;
        }
        if (0 >= (int)$rating->getRate1() || 5 < (int)$rating->getRate1()) {
            $flag = false;
        }
        if (0 >= (int)$rating->getRate2() || 5 < (int)$rating->getRate2()) {
            $flag = false;
        }
        if (0 >= (int)$rating->getRate3() || 5 < (int)$rating->getRate3()) {
            $flag = false;
        }
        if ($email = $rating->getEmail()) {
            if (!$this->helperData->validateEmailAddress($email)) {
                $flag = false;
            }
        }
        return $flag;
    }
}
