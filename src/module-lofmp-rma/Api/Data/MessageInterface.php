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
use Magento\Framework\Api\SearchCriteriaInterface;

interface MessageInterface extends DataInterface
{
    const COMMENT_PUBLIC = 'public';
    const COMMENT_INTERNAL = 'internal';

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
     * @return int
     */
    public function getUserId();

    /**
     * @param int $userId
     * @return $this
     */
    public function setUserId($userId);

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
     * @return string
     */
    public function getCustomerName();

    /**
     * @param string $customerName
     * @return $this
     */
    public function setCustomerName($customerName);

    /**
     * @return string
     */
    public function getText();

    /**
     * @param string $text
     * @return $this
     */
    public function setText($text);

    /**
     * @return bool|null
     */
    public function getIsHtml();

    /**
     * @param bool $isHtml
     * @return $this
     */
    public function setIsHtml($isHtml);

    /**
     * @return bool|null
     */
    public function getInternal();

    /**
     * @param bool $isVisibleInFrontend
     * @return $this
     */
    public function setInternal($internal);

    /**
     * @return bool|null
     */
    public function getIsCustomerNotified();

    /**
     * @param bool $isCustomerNotified
     * @return $this
     */
    public function setIsCustomerNotified($isCustomerNotified);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * @return string
     */
    public function getUpdatedAt();

    /**
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt);

    /**
     * @return int
     */
    public function getEmailId();

    /**
     * @param int $emailId
     * @return $this
     */
    public function setEmailId($emailId);

    /**
     * @return bool|null
     */
    public function getIsRead();

    /**
     * @param bool $isRead
     * @return $this
     */
    public function setIsRead($isRead);
}
