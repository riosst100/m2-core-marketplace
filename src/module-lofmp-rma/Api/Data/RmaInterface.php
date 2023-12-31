<?php
/**
 * LandOfCoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   LandOfCoder
 * @package    Lofmp_Rma
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\Rma\Api\Data;

use Lofmp\Rma\Api;

/**
 * @method Api\Data\RmaSearchResultsInterface getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
 */
interface RmaInterface extends DataInterface
{
    const KEY_RMA_ID = 'rma_id';
    const KEY_INCREMENT_ID = 'increment_id';
    const KEY_GUEST_ID = 'guest_id';
    const KEY_FIRSTNAME = 'firstname';
    const KEY_LASTNAME = 'lastname';
    const KEY_EMAIL = 'email';
    const KEY_CUSTOMER_ID = 'customer_id';
    const KEY_SELLER_ID = 'seller_id';
    const KEY_ORDER_ID = 'order_id';
    const KEY_STATUS_ID = 'status_id';
    const KEY_STORE_ID = 'store_id';
    const KEY_TRACKING_CODE = 'tracking_code';
    const KEY_IS_RESOLVED = 'is_resolved';
    const KEY_CREATED_AT = 'created_at';
    const KEY_UPDATED_AT = 'updated_at';
    const KEY_IS_GIFT = 'is_gift';
    const KEY_IS_ADMIN_READ = 'is_admin_read';
    const KEY_USER_ID = 'user_id';
    const KEY_LAST_REPLY_NAME = 'last_reply_name';
    const KEY_TICKET_ID = 'ticket_id';
    const KEY_EXCHANGE_ORDER_IDS = 'order_ids';
    const KEY_CREDIT_MEMO_IDS = 'credit_memo_ids';
    const KEY_RETURN_ADDRESS = 'return_address';
    const KEY_RETURN_ADDRESS_HTML = 'return_address_html';

    const MESSAGE_CODE = 'RMA-';

    /**
     * @return int
     */
    public function getRmaId();

    /**
     * @param int $rmaId
     * @return $this
     */
    public function setRmaId($rmaId);

    /**
     * @return string
     */
    public function getIncrementId();

    /**
     * @param string $incrementId
     * @return $this
     */
    public function setIncrementId($incrementId);

    /**
     * @return int
     */
    public function getCustomerId();

    /**
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * @return int
     */
    public function getOrderId();

    /**
     * @param int $orderId
     * @return $this
     */
    public function setOrderId($orderId);

    /**
     * @return int
     */
    public function getStatusId();

    /**
     * @param int $statusId
     * @return $this
     */
    public function setStatusId($statusId);

    /**
     * @return int
     */
    public function getStoreId();

    /**
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId);

    /**
     * @return string
     */
    public function getTrackingCode();

    /**
     * @param string $trackingCode
     * @return $this
     */
    public function setTrackingCode($trackingCode);

    /**
     * @return boolean|int
     */
    public function getIsResolved();

    /**
     * @param boolean $isResolved
     * @return $this
     */
    public function setIsResolved($isResolved);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $date
     * @return $this
     */
    public function setCreatedAt($date);

    /**
     * @return string
     */
    public function getUpdatedAt();

    /**
     * @param string $date
     * @return $this
     */
    public function setUpdatedAt($date);

    /**
     * @return bool|null
     */
    public function getIsGift();

    /**
     * @param bool $isGift
     * @return $this
     */
    public function setIsGift($isGift);

    /**
     * @return bool|null
     */
    public function getIsAdminRead();

    /**
     * @param bool $isAdminRead
     * @return $this
     */
    public function setIsAdminRead($isAdminRead);

    /**
     * @return int
     */
    public function getUserId();

    /**
     * @param int $userId
     * @return $this
     */
    public function setUserId($userId);

    /**
     * @return string
     */
    public function getLastReplyName();

    /**
     * @param string $lastReplyName
     * @return $this
     */
    public function setLastReplyName($lastReplyName);

    /**
     * @return string
     */
    public function getReturnAddress();

    /**
     * @param string $address
     * @return $this
     */
    public function setReturnAddress($address);

    /**
     * @return string
     */
    public function getReturnAddressHtml();

    /**
     * @param string $address
     * @return $this
     */
    public function setReturnAddressHtml($address_html);

    /**
     * @return string
     */
    public function getCode();
}
