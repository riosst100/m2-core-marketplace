<?php
/**
 * Copyright © Landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\MarketPlace\Model\Api;

use Lof\MarketPlace\Api\SubmitSellerReviewRepositoryInterface;
use Lof\MarketPlace\Api\SellerRatingsRepositoryInterface;
use Lof\MarketPlace\Api\Data\SubmitSellerReviewInterface;
use Lof\MarketPlace\Model\Rating;
use Lof\MarketPlace\Model\RatingFactory;
use Lof\MarketPlace\Model\SellerFactory;
use Lof\MarketPlace\Helper\Data;
use Lof\MarketPlace\Model\Sender;
use Lof\MarketPlace\Helper\Rating as RatingHelper;
use Lof\MarketPlace\Model\ResourceModel\Rating as RatingResource;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Class SubmitSellerReviewRepository
 */
class SubmitSellerReviewRepository implements SubmitSellerReviewRepositoryInterface
{

    /**
     * @var SellerFactory
     */
    protected $sellerFactory;

     /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var Sender
     */
    protected $sender;

    /**
     * @var SellerRatingsRepositoryInterface
     */
    protected $sellerRatingRepository;

    /**
     * @var RatingFactory
     */
    protected $ratingFactory;

    /**
     * @var RatingHelper
     */
    protected $ratingHelper;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var RatingResource
     */
    private $resource;

    /**
     * @var mixed|array
     */
    protected $_sellers = [];

    /**
     * submit seller rating constructor.
     * @param SellerRatingsRepositoryInterface $sellerRatingRepository
     * @param RatingFactory $ratingFactory
     * @param RatingHelper $ratingHelper
     * @param SellerFactory $sellerFactory
     * @param Data $helperData
     * @param Sender $sender
     * @param CustomerRepositoryInterface $customerRepository
     * @param StoreManagerInterface $storeManager
     * @param RatingResource $resource
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        SellerRatingsRepositoryInterface $sellerRatingRepository,
        RatingFactory $ratingFactory,
        RatingHelper $ratingHelper,
        SellerFactory $sellerFactory,
        Data $helperData,
        Sender $sender,
        CustomerRepositoryInterface $customerRepository,
        StoreManagerInterface $storeManager,
        RatingResource $resource
    ) {
        $this->sellerRatingRepository = $sellerRatingRepository;
        $this->storeManager = $storeManager;
        $this->sellerFactory = $sellerFactory;
        $this->helperData = $helperData;
        $this->ratingHelper = $ratingHelper;
        $this->ratingFactory = $ratingFactory;
        $this->sender = $sender;
        $this->customerRepository = $customerRepository;
        $this->resource = $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function save(int $customerId, string $sellerUrl, SubmitSellerReviewInterface $rating)
    {
        $validateRating = $this->validateRating($rating);
        if (!$validateRating) {
            throw new CouldNotSaveException(__(
                'Could not rating for this seller. Wrong input data.'
            ));
        }
        $seller = $this->getSellerByUrl($sellerUrl, $customerId);

        if ($seller && $seller->getId()) {
            /** @var \Magento\Customer\Api\Data\CustomerInterface $customer*/
            $customer = $this->customerRepository->getById($customerId);
            /** @var \Lof\MarketPlace\Model\Rating $rating */
            $ratingData = $rating->getData();
            $ratingData["customer_id"] = $customerId;
            $ratingData["seller_id"] = $seller->getId();
            $ratingData["nickname"] = $customer->getFirstname() . " ".$customer->getLastname();
            $ratingData["email"] = empty($ratingData["email"]) ? $customer->getEmail() : $ratingData["email"];
            $ratingData["rating"] = ((int)$ratingData["rate1"] + (int)$ratingData["rate2"] + (int)$ratingData["rate3"]) / 3;
            $ratingData["verified_buyer"] = $this->ratingHelper->checkPurchasedOrder((int)$seller->getId(), $customerId, $ratingData["email"]);
            $ratingHelper = $this->ratingHelper->setVerifiedBuyer($ratingData["verified_buyer"]);

            if ($this->helperData->getConfig('general_settings/rating_approval')) {
                $ratingData['status'] = Rating::STATUS_PENDING;
                $message = __('Your rating has been submitted for approval.');
            } else {
                $ratingData['status'] = Rating::STATUS_ACCEPT;
                $message = __('Your rating has been submitted successfully!');
            }

            if ($ratingHelper->checkAllowRating((int)$seller->getId(), $ratingData['email'], $customerId)) {
                $ratingData = $this->helperData->xss_clean_array($ratingData);
                /** save rating Detail */
                $ratingModel = $this->ratingFactory->create();
                $ratingModel->setData($ratingData);
                $this->resource->save($ratingModel);

                $ratingData["namestore"] = $this->helperData->getStoreName();
                $ratingData["urllogin"] = $this->helperData->getBaseStoreUrl() . 'customer/account/login';
                $ratingData["message"] = $message;
                $ratingData["seller_email"] = $seller->getEmail();
                $ratingData["seller_name"] = $seller->getName();

                /** Send notification email */
                if ($this->helperData->getConfig('email_settings/enable_send_email')) {
                    $this->sender->newRating($ratingData);
                }

                return $this->sellerRatingRepository->getById((int)$ratingModel->getId());
            } else {
                throw new CouldNotSaveException(__(
                    'The rating for sellerUrl %1 is not available for you at now. Maybe you should purchase the seller products before or your account was limited!'
                ));
            }
        } else {
            throw new NoSuchEntityException(__('Seller with sellerUrl "%1" does not exist.', $sellerUrl));
        }
    }

    /**
     * validate data
     *
     * @param SubmitSellerReviewInterface $rating
     * @return bool
     */
    protected function validateRating(SubmitSellerReviewInterface $rating)
    {
        $flag = true;
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

    /**
     * get seller by sellerUrl
     *
     * @param string $sellerUrl
     * @param int $customerId
     * @return \Lof\MarketPlace\Model\Seller
     */
    protected function getSellerByUrl(string $sellerUrl, int $customerId)
    {
        if (!isset($this->_sellers[$sellerUrl])) {
            $seller = $this->sellerFactory->create()->getCollection()
                    ->addFieldToFilter('url_key', ['eq' => $sellerUrl])
                    ->addFieldToFilter("status", \Lof\MarketPlace\Model\Seller::STATUS_ENABLED)
                    ->addFieldToFilter("customer_id", ["neq" => $customerId])
                    ->getFirstItem();
            $this->_sellers[$sellerUrl] = $seller;
        }
        return $this->_sellers[$sellerUrl];
    }
}
