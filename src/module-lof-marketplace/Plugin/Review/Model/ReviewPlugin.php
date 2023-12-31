<?php

/**
 * Lof
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Lof.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Lof
 * @package     Lof_MarketPlace
 * @copyright   Copyright (c) 2021 Lof (https://landofcoder.com/)
 * @license     https://landofcoder.com/LICENSE.txt
 */

namespace Lof\MarketPlace\Plugin\Review\Model;

use Lof\MarketPlace\Helper\Seller;

/**
 * Class Customer
 * @package Lof\MarketPlace\Plugin\Review
 */
class ReviewPlugin
{
    /**
     * @var  \Lof\MarketPlace\Helper\Data
     */
    protected $helper;

    /**
     * @var Seller
     */
    protected $sellerHelper;

    /**
     * @var   \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * @var \Magento\Review\Model\ResourceModel\Rating\Option\Vote\CollectionFactory
     */
    protected $_ratingCollectionFactory;

    /**
     * @var \Lof\MarketPlace\Model\ReviewFactory
     */
    protected $sellerReviewFactory;

    /**
     * @var \Lof\MarketPlace\Model\ResourceModel\Review
     */
    protected $sellerReviewResource;

    /**
     * ReviewProduct constructor.
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param \Magento\Review\Model\ResourceModel\Rating\Option\Vote\CollectionFactory $ratingCollectionFactory
     * @param \Lof\MarketPlace\Model\ReviewFactory $sellerReviewFactory
     * @param \Lof\MarketPlace\Model\ResourceModel\Review $sellerReviewResource
     * @param Seller $sellerHelper
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\ResourceConnection $resource,
        \Lof\MarketPlace\Helper\Data $helper,
        \Magento\Review\Model\ResourceModel\Rating\Option\Vote\CollectionFactory $ratingCollectionFactory,
        \Lof\MarketPlace\Model\ReviewFactory $sellerReviewFactory,
        \Lof\MarketPlace\Model\ResourceModel\Review $sellerReviewResource,
        Seller $sellerHelper
    ) {
        $this->customerSession = $customerSession;
        $this->helper = $helper;
        $this->sellerHelper = $sellerHelper;
        $this->_ratingCollectionFactory = $ratingCollectionFactory;
        $this->_resource = $resource;
        $this->sellerReviewFactory = $sellerReviewFactory;
        $this->sellerReviewResource = $sellerReviewResource;
    }

    /**
     * {@inheritdoc}
     */
    public function afterAggregate(
        \Magento\Review\Model\Review $subject,
        $result
    ) {
        $config_event = $this->helper->getConfig('general_settings/enable');
        if ($config_event) {
            /** @var \Magento\Review\Model\Review $result */
            $reviewCore = $result;
            $reviewMarketplace = $this->sellerReviewFactory->create();
            /** @var \Magento\Framework\DB\Adapter\AdapterInterface $connection */
            $connection = $this->_resource->getConnection();
            if ($reviewCore->getReviewId()) {
                /**
                 * Check exists review
                 */
                $reviewMarketplace->load($reviewCore->getReviewId(), 'review_id');
                $data = $reviewCore->getData();
                if (!$reviewMarketplace->getReviewsellerId() && !empty($data)) {
                    $productId = $reviewCore->getEntityPkValue();
                    $reviewId = $reviewCore->getReviewId();
                    $collection = $this->_ratingCollectionFactory->create()
                        ->setReviewFilter($reviewId)
                        ->setEntityPkFilter($productId);
                    $ratingSummary = 0;
                    $collection->getSelect();
                    if ($collection->getSize()) {
                        $ratingTotal = 0;
                        foreach ($collection as $_item) {
                            $ratingTotal += (float)$_item->getValue();
                        }
                        $ratingSummary = round($ratingTotal / $collection->getSize(), 2);
                    }
                    if ($ratingSummary) {
                        $data['rating'] = $ratingSummary;
                    }
                    $data['product_id'] = $productId;
                    $data['status'] = $data['status_id'];
                    $data['seller_id'] = $this->sellerHelper->getSellerIdByProduct($productId);
                    $data['customer_id'] = $this->getCustomerIdByReview($connection, $reviewCore->getReviewId());
                    $data['customer_id'] = !empty($data['customer_id']) ? (int)$data['customer_id'] : $this->customerSession->getId();
                    try {
                        $reviewMarketplace->setData($data);
                        $this->sellerReviewResource->save($reviewMarketplace);
                    } catch (\Exception $e) {
                        //
                    }
                }
                }
        }
        return $result;
    }

    /**
     * get customer id by review
     *
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     * @param int $reviewId
     * @return int|string|null
     */
    protected function getCustomerIdByReview($connection, $reviewId)
    {
        $reviewDetailTableName = $this->_resource->getTableName('review_detail');
        $select = $connection->select()->from($reviewDetailTableName, 'customer_id')->where('review_id = :review_id');
        $customerId = $connection->fetchOne($select, [':review_id' => $reviewId]);
        return $customerId;
    }
}
