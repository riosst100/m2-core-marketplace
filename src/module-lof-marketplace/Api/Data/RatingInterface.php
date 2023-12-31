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

namespace Lof\MarketPlace\Api\Data;

interface RatingInterface
{
    const ID = 'id';
    const RATING_ID = 'rating_id';
    const SELLER_ID = 'seller_id';
    const CUSTOMER_ID = 'customer_id';
    const TITLE = 'title';
    const DETAIL = 'detail';
    const NICKNAME = 'nickname';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const STATUS = 'status';
    const RATE1 = 'rate1';
    const RATE2 = 'rate2';
    const RATE3 = 'rate3';
    const RATING = 'rating';
    const EMAIL = 'email';

    const VERIFIED_BUYER = 'verified_buyer';
    const IS_RECOMMENDED = 'is_recommended';
    const IS_HIDDEN = 'is_hidden';
    const ANSWER = 'answer';
    const ADMIN_NOTE = 'admin_note';
    const LIVE_ABOUT = 'like_about';
    const NOT_LIKE_ABOUT = 'not_like_about';
    const GUEST_EMAIL = 'guest_email';
    const PLUS_REVIEW = 'plus_review';
    const MINUS_REVIEW = 'minus_review';
    const REPORT_ABUSE = 'report_abuse';
    const COUNTRY = 'country';

    /**
     * Get rating_id
     * @return int|null
     */
    public function getRatingId();

    /**
     * Set rating_id
     * @param int $rating
     * @return $this
     */
    public function setRatingId($rating_id);

    /**
     * Get seller_id
     * @return int|null
     */
    public function getSellerId();

    /**
     * Set seller_id
     * @param int $seller_id
     * @return $this
     */
    public function setSellerId($seller_id);

    /**
     * Get customer_id
     * @return int|null
     */
    public function getCustomerId();

    /**
     * Set customer_id
     * @param int $customer_id
     * @return $this
     */
    public function setCustomerId($customer_id);

    /**
     * Get rate1
     * @return int|null
     */
    public function getRate1();

    /**
     * Set rate1
     * @param int $rate1
     * @return $this
     */
    public function setRate1($rate1);

    /**
     * Get rate2
     * @return int|null
     */
    public function getRate2();

    /**
     * Set rate2
     * @param int $rate2
     * @return $this
     */
    public function setRate2($rate2);

    /**
     * Get rate3
     * @return int|null
     */
    public function getRate3();

    /**
     * Set rate3
     * @param int $rate3
     * @return $this
     */
    public function setRate3($rate3);

    /**
     * Get rating
     * @return int|null
     */
    public function getRating();

    /**
     * Set rating
     * @param int $rating
     * @return $this
     */
    public function setRating($rating);

    /**
     * Get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Get title
     * @return string|null
     */
    public function getTitle();

    /**
     * Set title
     * @param string $title
     * @return $this
     */
    public function setTitle($title);

    /**
     * Get detail
     * @return string|null
     */
    public function getDetail();

    /**
     * Set detail
     * @param string $detail
     * @return $this
     */
    public function setDetail($detail);

    /**
     * Get nickname
     * @return string|null
     */
    public function getNickname();

    /**
     * Set nickname
     * @param string $nickname
     * @return $this
     */
    public function setNickname($nickname);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $created_at
     * @return $this
     */
    public function setCreatedAt($created_at);

    /**
     * Get verified_buyer
     *
     * @return int
     */
    public function getVerifiedBuyer();

    /**
     * Set verified_buyer
     *
     * @param int $verified_buyer
     *
     * @return $this
     */
    public function setVerifiedBuyer($verified_buyer);

    /**
     * Get is_recommended
     *
     * @return bool
     */
    public function getIsRecommended();

    /**
     * Set is_recommended
     *
     * @param bool $is_recommended
     *
     * @return $this
     */
    public function setIsRecommended($is_recommended);

    /**
     * Get answer
     *
     * @return string
     */
    public function getAnswer();

    /**
     * Set answer
     *
     * @param string $answer
     *
     * @return $this
     */
    public function setAnswer($answer);

    /**
     * Get admin_note
     *
     * @return string
     */
    public function getAdminNote();

    /**
     * Set admin_note
     *
     * @param string $admin_note
     *
     * @return $this
     */
    public function setAdminNote($admin_note);

    /**
     * Get like_about
     *
     * @return string
     */
    public function getLikeAbout();

    /**
     * Set like_about
     *
     * @param string $like_about
     *
     * @return $this
     */
    public function setLikeAbout($like_about);

    /**
     * Get not_like_about
     *
     * @return string
     */
    public function getNotLikeAbout();

    /**
     * Set not_like_about
     *
     * @param string $not_like_about
     *
     * @return $this
     */
    public function setNotLikeAbout($not_like_about);

    /**
     * Get guest_email
     *
     * @return string|null
     */
    public function getGuestEmail();

    /**
     * Set guest_email
     *
     * @param string|null $guest_email
     *
     * @return $this
     */
    public function setGuestEmail($guest_email);

    /**
     * Get email
     *
     * @return string|null
     */
    public function getEmail();

    /**
     * Set email
     *
     * @param string|null $email
     *
     * @return $this
     */
    public function setEmail($email);

    /**
     * Get plus_review
     *
     * @return int
     */
    public function getPlusReview();

    /**
     * Set plus_review
     *
     * @param int $plus_review
     *
     * @return $this
     */
    public function setPlusReview($plus_review);

    /**
     * Get minus_review
     *
     * @return int
     */
    public function getMinusReview();

    /**
     * Set minus_review
     *
     * @param int $minus_review
     *
     * @return $this
     */
    public function setMinusReview($minus_review);

    /**
     * Get report_abuse
     * @return int|null
     */
    public function getReportAbuse();

    /**
     * Set report_abuse
     * @param int $reportAbuse
     * @return $this
     */
    public function setReportAbuse($reportAbuse);

    /**
     * Get updated_at
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set updated_at
     * @param string $updated_at
     * @return $this
     */
    public function setUpdatedAt($updated_at);
}
