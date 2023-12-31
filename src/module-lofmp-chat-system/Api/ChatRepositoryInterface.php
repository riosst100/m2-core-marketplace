<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lofmp\ChatSystem\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface ChatRepositoryInterface
{

    /**
     * Save Chat
     * @param \Lofmp\ChatSystem\Api\Data\ChatInterface $chat
     * @return \Lofmp\ChatSystem\Api\Data\ChatInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Lofmp\ChatSystem\Api\Data\ChatInterface $chat
    );

    /**
     * Retrieve Chat
     * @param int $chatId
     * @return \Lofmp\ChatSystem\Api\Data\ChatInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($chatId);

    /**
     * Retrieve Chat matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lofmp\ChatSystem\Api\Data\ChatSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Chat
     * @param \Lofmp\ChatSystem\Api\Data\ChatInterface $chat
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Lofmp\ChatSystem\Api\Data\ChatInterface $chat
    );

    /**
     * Delete Chat by ID
     * @param int $chatId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($chatId);

    /**
     * Delete Chat by ID
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function clear();

    /**
     * seller send Chat message
     * @param int $customerId
     * @param int $chatId
     * @param \Lofmp\ChatSystem\Api\Data\SubmitChatInterface $chat
     * @return \Lofmp\ChatSystem\Api\Data\ChatInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sellerSendMessage(
        int $customerId,
        int $chatId,
        \Lofmp\ChatSystem\Api\Data\SubmitChatInterface $chat
    );

    /**
     * Retrieve Chat
     * @param int $customerId
     * @param int $chatId
     * @return \Lofmp\ChatSystem\Api\Data\ChatInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sellerGetChat(int $customerId, $chatId);

    /**
     * Retrieve Chat matching the specified criteria.
     * @param int $customerId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lofmp\ChatSystem\Api\Data\ChatSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sellerGetList(
        int $customerId,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Chat by ID
     * @param int $customerId
     * @param int $chatId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sellerDeleteById(int $customerId, $chatId);

    /**
     * Delete Chat by ID
     * @param int $customerId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sellerClear(int $customerId);

    /**
     * Save Chat
     * @param int $customerId
     * @param string $sellerUrl
     * @param \Lofmp\ChatSystem\Api\Data\SubmitChatInterface $chat
     * @return \Lofmp\ChatSystem\Api\Data\ChatInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function customerSendMessage(
        int $customerId,
        string $sellerUrl,
        \Lofmp\ChatSystem\Api\Data\SubmitChatInterface $chat
    );

    /**
     * Retrieve Chat
     * @param int $customerId
     * @param int $chatId
     * @return \Lofmp\ChatSystem\Api\Data\ChatInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function customerChatById(int $customerId, $chatId);

    /**
     * Retrieve Chat matching the specified criteria.
     * @param int $customerId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lofmp\ChatSystem\Api\Data\ChatSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function customerChatList(
        int $customerId,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );
}

