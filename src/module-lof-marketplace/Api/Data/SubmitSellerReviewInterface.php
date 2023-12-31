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

interface SubmitSellerReviewInterface
{
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
     * Get email
     * @return string|null
     */
    public function getEmail();

    /**
     * Set email
     * @param string $email
     * @return $this
     */
    public function setEmail($email);

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
}
