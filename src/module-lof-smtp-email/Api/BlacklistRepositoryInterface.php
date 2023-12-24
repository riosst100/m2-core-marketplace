<?php
/**
 * Copyright © landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\SmtpEmail\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface BlacklistRepositoryInterface
{

    /**
     * Save Blacklist
     * @param \Lof\SmtpEmail\Api\Data\BlacklistInterface $blacklist
     * @return \Lof\SmtpEmail\Api\Data\BlacklistInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Lof\SmtpEmail\Api\Data\BlacklistInterface $blacklist
    );

    /**
     * Retrieve Blacklist
     * @param string $blacklistId
     * @return \Lof\SmtpEmail\Api\Data\BlacklistInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($blacklistId);

    /**
     * Retrieve Blacklist matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lof\SmtpEmail\Api\Data\BlacklistSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Blacklist
     * @param \Lof\SmtpEmail\Api\Data\BlacklistInterface $blacklist
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Lof\SmtpEmail\Api\Data\BlacklistInterface $blacklist
    );

    /**
     * Delete Blacklist by ID
     * @param string $blacklistId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($blacklistId);
}

