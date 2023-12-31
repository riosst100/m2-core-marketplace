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

interface SellerVacationInterface
{
    const VACATION_ID = 'vacation_id';
    const STATUS = 'status';
    const VACATION_MESSAGE = 'vacation_message';
    const FROM_DATE = 'from_date';
    const TO_DATE = 'to_date';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const TEXT_ADD_CART = 'text_add_cart';
    const SELLER_ID = 'seller_id';

    /**
     * get seller_id
     * @return string|null
     */
    public function getSellerId();

    /**
     * Set seller_id
     * @param string $seller_id
     * @return string|null
     */
    public function setSellerId($seller_id);

    /**
     * get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $created_at
     * @return string|null
     */
    public function setCreatedAt($created_at);

    /**
     * get updated_at
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set updated_at
     * @param string $updated_at
     * @return string|null
     */
    public function setUpdatedAt($updated_at);

    /**
     * get vacation_id
     * @return string|null
     */
    public function getVacationId();

    /**
     * Set vacation_id
     * @param string $vacation_id
     * @return string|null
     */
    public function setVacationId($vacation_id);

    /**
     * get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return string|null
     */
    public function setStatus($status);

    /**
     * get vacation_message
     * @return string|null
     */
    public function getVacationMessage();

    /**
     * Set status
     * @param string $vacation_message
     * @return string|null
     */
    public function setVacationMessage($vacation_message);

    /**
     * get from_date
     * @return string|null
     */
    public function getFromDate();

    /**
     * Set status
     * @param string $from_date
     * @return string|null
     */
    public function setFromDate($from_date);

    /**
     * get text add cart
     * @return string|null
     */
    public function getToDate();

    /**
     * Set text add cart
     * @param string $to_date
     * @return string|null
     */
    public function setToDate($to_date);

    /**
     * get text add cart
     * @return string|null
     */
    public function getTextAddCart();

    /**
     * Set text add cart
     * @param string $text_add_cart
     * @return string|null
     */
    public function setTextAddCart($text_add_cart);

}
