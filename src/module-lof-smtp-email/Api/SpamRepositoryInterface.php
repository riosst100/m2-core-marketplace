<?php
/**
 * Copyright © landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\SmtpEmail\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface SpamRepositoryInterface
{

    /**
     * Save Spam
     * @param \Lof\SmtpEmail\Api\Data\SpamInterface $spam
     * @return \Lof\SmtpEmail\Api\Data\SpamInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Lof\SmtpEmail\Api\Data\SpamInterface $spam
    );

    /**
     * Retrieve Spam
     * @param string $spamId
     * @return \Lof\SmtpEmail\Api\Data\SpamInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($spamId);

    /**
     * Retrieve Spam matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lof\SmtpEmail\Api\Data\SpamSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Spam
     * @param \Lof\SmtpEmail\Api\Data\SpamInterface $spam
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Lof\SmtpEmail\Api\Data\SpamInterface $spam
    );

    /**
     * Delete Spam by ID
     * @param string $spamId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($spamId);
}

