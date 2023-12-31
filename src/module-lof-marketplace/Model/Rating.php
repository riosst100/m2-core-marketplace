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

use Lof\MarketPlace\Api\Data\RatingInterface;

class Rating extends \Magento\Framework\Model\AbstractModel implements RatingInterface
{
    const STATUS_ACCEPT = 'accept';
    const STATUS_PENDING = 'pending';

    /**
     * @var string
     */
    protected $_eventPrefix = 'lof_marketplace_rating';
    /**
     * Name of the event object
     *
     * @var string
     */
    protected $_eventObject = 'rating';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(\Lof\MarketPlace\Model\ResourceModel\Rating::class);
    }

    /**
     * Get rating_id
     * @return int|null
     */
    public function getRatingId()
    {
        return $this->getData(self::RATING_ID);
    }

    /**
     * Set rating_id
     * @param int $rating
     * @return $this
     */
    public function setRatingId($rating_id)
    {
        $this->setId((int)$rating_id);
        return $this->setData(self::RATING_ID, $rating_id);
    }

    /**
     * Get seller_id
     * @return int|null
     */
    public function getSellerId()
    {
        return $this->getData(self::SELLER_ID);
    }

    /**
     * Set seller_id
     * @param int $seller_id
     * @return $this
     */
    public function setSellerId($seller_id)
    {
        return $this->setData(self::SELLER_ID, $seller_id);
    }

    /**
     * Get customer_id
     * @return int|null
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * Set customer_id
     * @param int $customer_id
     * @return $this
     */
    public function setCustomerId($customer_id)
    {
        return $this->setData(self::CUSTOMER_ID, $customer_id);
    }

    /**
     * Get rate1
     * @return int|null
     */
    public function getRate1()
    {
        return $this->getData(self::RATE1);
    }

    /**
     * Set rate1
     * @param int $rate1
     * @return $this
     */
    public function setRate1($rate1)
    {
        return $this->setData(self::RATE1, $rate1);
    }

    /**
     * Get rate2
     * @return int|null
     */
    public function getRate2()
    {
        return $this->getData(self::RATE2);
    }

    /**
     * Set rate2
     * @param int $rate2
     * @return $this
     */
    public function setRate2($rate2)
    {
        return $this->setData(self::RATE2, $rate2);
    }

    /**
     * Get rate3
     * @return int|null
     */
    public function getRate3()
    {
        return $this->getData(self::RATE3);
    }

    /**
     * Set rate3
     * @param int $rate3
     * @return $this
     */
    public function setRate3($rate3)
    {
        return $this->setData(self::RATE3, $rate3);
    }

    /**
     * Get rating
     * @return int|null
     */
    public function getRating()
    {
        return $this->getData(self::RATING);
    }

    /**
     * Set rating
     * @param int $rating
     * @return $this
     */
    public function setRating($rating)
    {
        return $this->setData(self::RATING, $rating);
    }

    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * @inheritDoc
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * @inheritDoc
     */
    public function getDetail()
    {
        return $this->getData(self::DETAIL);
    }

    /**
     * @inheritDoc
     */
    public function setDetail($detail)
    {
        return $this->setData(self::DETAIL, $detail);
    }

    /**
     * @inheritDoc
     */
    public function getNickname()
    {
        return $this->getData(self::NICKNAME);
    }

    /**
     * @inheritDoc
     */
    public function setNickname($nickname)
    {
        return $this->setData(self::NICKNAME, $nickname);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt($created_at)
    {
        return $this->setData(self::CREATED_AT, $created_at);
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt($updated_at)
    {
        return $this->setData(self::UPDATED_AT, $updated_at);
    }

    /**
     * @inheritdoc
     */
    public function getEmail()
    {
        return $this->getData(self::EMAIL);
    }

    /**
     * @inheritdoc
     */
    public function setEmail($email)
    {
        return $this->setData(self::EMAIL, $email);
    }

    /**
     * Get verified_buyer
     *
     * @return int
     */
    public function getVerifiedBuyer()
    {
        return $this->getData(self::VERIFIED_BUYER);
    }

    /**
     * Set verified_buyer
     *
     * @param int $verified_buyer
     *
     * @return $this
     */
    public function setVerifiedBuyer($verified_buyer)
    {
        return $this->setData(self::VERIFIED_BUYER, $verified_buyer);
    }

    /**
     * Get is_recommended
     *
     * @return bool
     */
    public function getIsRecommended()
    {
        return $this->getData(self::IS_RECOMMENDED);
    }

    /**
     * Set is_recommended
     *
     * @param bool $is_recommended
     *
     * @return $this
     */
    public function setIsRecommended($is_recommended)
    {
        return $this->setData(self::IS_RECOMMENDED, $is_recommended);
    }

    /**
     * Get answer
     *
     * @return string
     */
    public function getAnswer()
    {
        return $this->getData(self::ANSWER);
    }

    /**
     * Set answer
     *
     * @param string $answer
     *
     * @return $this
     */
    public function setAnswer($answer)
    {
        return $this->setData(self::ANSWER, $answer);
    }

     /**
     * Get answer
     *
     * @return string
     */
    public function getAdminNote()
    {
        return $this->getData(self::ADMIN_NOTE);
    }

    /**
     * Set admin_note
     *
     * @param string $admin_note
     *
     * @return $this
     */
    public function setAdminNote($admin_note)
    {
        return $this->setData(self::ADMIN_NOTE, $admin_note);
    }

    /**
     * Get like_about
     *
     * @return string
     */
    public function getLikeAbout()
    {
        return $this->getData(self::LIVE_ABOUT);
    }

    /**
     * Set like_about
     *
     * @param string $like_about
     *
     * @return $this
     */
    public function setLikeAbout($like_about)
    {
        return $this->setData(self::LIVE_ABOUT, $like_about);
    }

    /**
     * Get not_like_about
     *
     * @return string
     */
    public function getNotLikeAbout()
    {
        return $this->getData(self::NOT_LIKE_ABOUT);
    }

    /**
     * Set not_like_about
     *
     * @param string $not_like_about
     *
     * @return $this
     */
    public function setNotLikeAbout($not_like_about)
    {
        return $this->setData(self::NOT_LIKE_ABOUT, $not_like_about);
    }

    /**
     * Get guest_email
     *
     * @return string
     */
    public function getGuestEmail()
    {
        $guest_email = $this->getData(self::GUEST_EMAIL);
        if (!$guest_email && !$this->getCustomerId() && $this->getEmail()) {
            $this->setGuestEmail($this->getEmail());
            $guest_email = $this->getEmail();
        }
        return $guest_email;
    }

    /**
     * Set guest_email
     *
     * @param string $guest_email
     *
     * @return $this
     */
    public function setGuestEmail($guest_email)
    {
        return $this->setData(self::GUEST_EMAIL, $guest_email);
    }

    /**
     * Get plus_review
     *
     * @return int
     */
    public function getPlusReview()
    {
        return $this->getData(self::PLUS_REVIEW);
    }

    /**
     * Set plus_review
     *
     * @param int $plus_review
     *
     * @return $this
     */
    public function setPlusReview($plus_review)
    {
        return $this->setData(self::PLUS_REVIEW, $plus_review);
    }

    /**
     * Get minus_review
     *
     * @return int
     */
    public function getMinusReview()
    {
        return $this->getData(self::MINUS_REVIEW);
    }

    /**
     * Set minus_review
     *
     * @param int $minus_review
     *
     * @return $this
     */
    public function setMinusReview($minus_review)
    {
        return $this->setData(self::MINUS_REVIEW, $minus_review);
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->getData(self::COUNTRY);
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return $this
     */
    public function setCountry($country)
    {
        return $this->setData(self::COUNTRY, $country);
    }

    /**
     * @inheritDoc
     */
    public function getReportAbuse()
    {
        return $this->getData(self::REPORT_ABUSE);
    }

    /**
     * @inheritDoc
     *
     * @return $this
     */
    public function setReportAbuse($reportAbuse)
    {
        return $this->setData(self::REPORT_ABUSE, $reportAbuse);
    }
}
