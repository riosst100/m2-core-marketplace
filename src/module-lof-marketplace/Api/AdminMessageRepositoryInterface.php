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

namespace Lof\MarketPlace\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface AdminMessageRepositoryInterface
{

    /**
     * @param int $customerId
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Lof\MarketPlace\Api\Data\AdminMessageSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getMyList(
        int $customerId,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * get message admin by id
     *
     * @param int $messageId
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Lof\MarketPlace\Api\Data\AdminMessageInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getMyMessage(
        int $customerId,
        int $messageId
    );

    /**
     * get message admin details by id
     *
     * @param int $customerId
     * @param int $messageId
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Lof\MarketPlace\Api\Data\MessageDetailSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getMyDetails(
        int $customerId,
        int $messageId,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * send message to admin
     *
     * @param int $customerId
     * @param string $subject
     * @param string $message
     * @return \Lof\MarketPlace\Api\Data\AdminMessageInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sendMessage(int $customerId, string $subject, string $message);

    /**
     * reply Message Admin
     *
     * @param int $customerId
     * @param int $messageId
     * @param string $message
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Lof\MarketPlace\Api\Data\MessageDetailInterface
     */
    public function replyMessage(int $customerId, int $messageId, string $message);

    /**
    * @param int $customerId
    * @param int $messageId
    * @param string $status - available status: sent, read, unread, draft
    * @return \Lof\MarketPlace\Api\Data\AdminMessageInterface
    * @throws \Magento\Framework\Exception\LocalizedException
    */
    public function setIsRead(
        int $customerId,
        int $messageId,
        string $status = null
    );

    /**
     * Save Message
     * @param \Lof\MarketPlace\Api\Data\AdminMessageInterface $message
     * @return \Lof\MarketPlace\Api\Data\AdminMessageInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Lof\MarketPlace\Api\Data\AdminMessageInterface $message
    );

    /**
     * Save Message Detail
     * @param \Lof\MarketPlace\Api\Data\MessageDetailInterface $messageDetail
     * @return \Lof\MarketPlace\Api\Data\MessageDetailInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveDetail(
        \Lof\MarketPlace\Api\Data\MessageDetailInterface $messageDetail
    );

    /**
     * Retrieve Message
     * @param int $messageId
     * @return \Lof\MarketPlace\Api\Data\AdminMessageInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($messageId);

    /**
     * Retrieve Message matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lof\MarketPlace\Api\Data\AdminMessageSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * get message admin details by id
     *
     * @param int $messageId
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Lof\MarketPlace\Api\Data\MessageDetailSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getDetails(
        int $messageId,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Message
     * @param \Lof\MarketPlace\Api\Data\AdminMessageInterface $message
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Lof\MarketPlace\Api\Data\AdminMessageInterface $message
    );

    /**
     * Delete Message by ID
     * @param string $messageId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($messageId);
}
