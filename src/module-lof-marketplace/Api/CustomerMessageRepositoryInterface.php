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

interface CustomerMessageRepositoryInterface
{

    /**
     * @param int $customerId
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Lof\MarketPlace\Api\Data\MessageSearchResultsInterface
     */
    public function getMyList(
        int $customerId,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * @param int $customerId
     * @param int $messageId
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Lof\MarketPlace\Api\Data\MessageSearchResultsInterface
     */
    public function getMyDetails(
        int $customerId,
        int $messageId,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * @param int $customerId
     * @param string $sellerUrl
     * @param string $subject
     * @param string $content
     * @return \Lof\MarketPlace\Api\Data\MessageInterface
     */
    public function sendMessage(int $customerId, string $sellerUrl, string $subject, string $content);

    /**
     * reply Message
     *
     * @param int $customerId
     * @param int $messageId
     * @param string $message
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Lof\MarketPlace\Api\Data\MessageDetailInterface
     */
    public function replyMessage(int $customerId, int $messageId, string $message);

    /**
     * delete customer Message
     *
     * @param int $customerId
     * @param int $messageId
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return bool true on success
     */
    public function deleteMessage(int $customerId, int $messageId);

}
