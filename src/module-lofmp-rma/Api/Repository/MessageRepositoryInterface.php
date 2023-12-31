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

namespace Lofmp\Rma\Api\Repository;

use Magento\Framework\Api\SearchCriteriaInterface;

interface MessageRepositoryInterface
{
    /**
     * Save message
     * @param \Lofmp\Rma\Api\Data\MessageInterface $message
     * @return \Lofmp\Rma\Api\Data\MessageInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Lofmp\Rma\Api\Data\MessageInterface $message
    );

    /**
     * Retrieve message
     * @param string $messageId
     * @return \Lofmp\Rma\Api\Data\MessageInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($messageId);

    /**
     * Retrieve message matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lofmp\Rma\Api\Data\MessageSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete message
     * @param \Lofmp\Rma\Api\Data\MessageInterface $message
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Lofmp\Rma\Api\Data\MessageInterface $message
    );

    /**
     * Delete message by ID
     * @param string $messageId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($messageId);
}
