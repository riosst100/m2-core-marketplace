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
 * @package    Lof_Quickrfq
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
declare(strict_types=1);

namespace Lof\Quickrfq\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Interface CustomerRepositoryInterface
 * @package Lof\Quickrfq\Api
 */
interface CustomerRepositoryInterface
{

    /**
     * Save Quickrfq
     * @param int $customerId
     * @param \Lof\Quickrfq\Api\Data\QuickrfqInterface $quickrfq
     * @return \Lof\Quickrfq\Api\Data\QuickrfqInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        $customerId,
        \Lof\Quickrfq\Api\Data\QuickrfqInterface $quickrfq
    );

    /**
     * Retrieve Quickrfq
     * @param int $customerId
     * @param string $quickrfqId
     * @return \Lof\Quickrfq\Api\Data\QuickrfqInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($customerId, $quickrfqId);

    /**
     * Retrieve Quickrfq matching the specified criteria.
     * @param int $customerId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lof\Quickrfq\Api\Data\QuickrfqSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        $customerId,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Retrieve quote messages matching the specified criteria.
     * @param int $customerId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lof\Quickrfq\Api\Data\MessageSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getListMessage(
        $customerId,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Save quote message
     * @param int $customerId
     * @param \Lof\Quickrfq\Api\Data\MessageInterface $message
     * @return \Lof\Quickrfq\Api\Data\MessageInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveMessage(
        $customerId,
        \Lof\Quickrfq\Api\Data\MessageInterface $message
    );
    
}
