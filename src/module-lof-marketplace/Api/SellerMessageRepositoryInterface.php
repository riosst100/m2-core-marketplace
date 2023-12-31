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

interface SellerMessageRepositoryInterface
{

    /**
     * @param int $customerId
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Lof\MarketPlace\Api\Data\MessageSearchResultsInterface
     */
    public function sellerGetList(
        int $customerId,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * @param int $customerId
     * @param int $messageId
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Lof\MarketPlace\Api\Data\MessageSearchResultsInterface
     */
    public function sellerGetDetails(
        int $customerId,
        int $messageId,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * reply Message
     *
     * @param int $customerId
     * @param int $messageId
     * @param string $message
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Lof\MarketPlace\Api\Data\MessageDetailInterface
     */
    public function sellerReplyMessage(int $customerId, int $messageId, string $message);

    /**
     * seller delete Message
     *
     * @param int $customerId
     * @param int $messageId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return bool true on success
     */
    public function sellerDeleteMessage(int $customerId, int $messageId);
}
