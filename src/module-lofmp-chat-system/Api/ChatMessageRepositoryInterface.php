<?php

/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Lofmp\ChatSystem\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface ChatMessageRepositoryInterface
{

    /**
     * Save ChatMessage
     * @param \Lofmp\ChatSystem\Api\Data\ChatMessageInterface $chatMessage
     * @return \Lofmp\ChatSystem\Api\Data\ChatMessageInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Lofmp\ChatSystem\Api\Data\ChatMessageInterface $chatMessage
    );

    /**
     * Retrieve ChatMessage matching the specified criteria.
     * @param int $customerId
     * @param int $messageId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lofmp\ChatSystem\Api\Data\ChatMessageSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sellerGetChatMessage(
        int $customerId,
        int $messageId
    );

    /**
     * @param int $customerId
     * @param int $chatId
     * @param SearchCriteriaInterface $searchCriteria
     * @return mixed
     */
    public function sellerGetListByChatId(
        int $customerId,
        int $chatId,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete ChatMessage
     * @param \Lofmp\ChatSystem\Api\Data\ChatMessageInterface $chatMessage
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Lofmp\ChatSystem\Api\Data\ChatMessageInterface $chatMessage
    );

    /**
     * Delete ChatMessage by ID
     * @param int $messageId
     * @param int $customerId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sellerDeleteByMessageId(int $customerId, $messageId);

    /**
     * @param int $customerId
     * @param int $chatId
     * @param Data\ChatMessageInterface $message
     * @return mixed
     */
    public function sellerSendCustomerChatMessage(
        int$customerId,
        int $chatId,
        \Lofmp\ChatSystem\Api\Data\ChatMessageInterface $message
    );

}
