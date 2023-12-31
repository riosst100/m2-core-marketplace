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
 * Interface QuickrfqRepositoryInterface
 * @package Lof\Quickrfq\Api
 */
interface QuickrfqRepositoryInterface
{

    /**
     * Save Quickrfq
     * @param \Lof\Quickrfq\Api\Data\QuickrfqInterface $quickrfq
     * @return \Lof\Quickrfq\Api\Data\QuickrfqInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Lof\Quickrfq\Api\Data\QuickrfqInterface $quickrfq
    );

    /**
     * Update Quickrfq
     * @param string $quickrfq_id
     * @param \Lof\Quickrfq\Api\Data\QuickrfqInterface $quickrfq
     * @return \Lof\Quickrfq\Api\Data\QuickrfqInterface
     */
    public function update(
        string $quickrfq_id,
        \Lof\Quickrfq\Api\Data\QuickrfqInterface $quickrfq
    );

    /**
     * Retrieve Quickrfq
     * @param string $quickrfqId
     * @return \Lof\Quickrfq\Api\Data\QuickrfqInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($quickrfqId);

    /**
     * Retrieve Quickrfq matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lof\Quickrfq\Api\Data\QuickrfqSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Quickrfq
     * @param \Lof\Quickrfq\Api\Data\QuickrfqInterface $quickrfq
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Lof\Quickrfq\Api\Data\QuickrfqInterface $quickrfq
    );

    /**
     * Delete Quickrfq by ID
     * @param string $quickrfqId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($quickrfqId);
}
