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
 * @copyright  Copyright (c) 2020 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */


namespace Lofmp\Rma\Api\Repository;

interface CustomerMessageRepositoryInterface
{


    /**
     * Save message
     * @param int $customerId
     * @param \Lofmp\Rma\Api\Data\MessageInterface $message
     * @return \Lofmp\Rma\Api\Data\MessageInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        $customerId,
        \Lofmp\Rma\Api\Data\MessageInterface $message
    );

    /**
     * Retrieve message
     * @param int $customerId
     * @param string $messageId
     * @return \Lofmp\Rma\Api\Data\MessageInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($customerId, $messageId);

    /**
     * Retrieve message matching the specified criteria.
     * @param int $customerId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lofmp\Rma\Api\Data\MessageSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        $customerId,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Retrieve message matching the rma id (include bundle rma id).
     * @param int $customerId
     * @param int $rmaId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lofmp\Rma\Api\Data\MessageSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getListByRma(
        $customerId,
        $rmaId,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );
}
