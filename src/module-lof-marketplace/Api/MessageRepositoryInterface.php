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

interface MessageRepositoryInterface
{
    /**
    * @param int $customerId
    * @param int $messageId
    * @param string $status - available status: sent, read, unread, draft
    * @return \Lof\MarketPlace\Api\Data\MessageInterface
    * @throws \Magento\Framework\Exception\LocalizedException
    */
    public function setIsRead(
        int $customerId,
        int $messageId,
        string $status = null
    );


    /**
     * Save Message
     * @param \Lof\MarketPlace\Api\Data\MessageInterface $message
     * @return \Lof\MarketPlace\Api\Data\MessageInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Lof\MarketPlace\Api\Data\MessageInterface $message
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
     * @return \Lof\MarketPlace\Api\Data\MessageInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($messageId);

    /**
     * Delete Message
     * @param \Lof\MarketPlace\Api\Data\MessageInterface $message
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Lof\MarketPlace\Api\Data\MessageInterface $message
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
