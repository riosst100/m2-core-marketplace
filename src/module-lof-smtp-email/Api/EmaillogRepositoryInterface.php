<?php
/**
 * Copyright © landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\SmtpEmail\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface EmaillogRepositoryInterface
{

    /**
     * Save Emaillog
     * @param \Lof\SmtpEmail\Api\Data\EmaillogInterface $emaillog
     * @return \Lof\SmtpEmail\Api\Data\EmaillogInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Lof\SmtpEmail\Api\Data\EmaillogInterface $emaillog
    );

    /**
     * Retrieve Emaillog
     * @param string $emaillogId
     * @return \Lof\SmtpEmail\Api\Data\EmaillogInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($emaillogId);

    /**
     * Retrieve Emaillog matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lof\SmtpEmail\Api\Data\EmaillogSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Emaillog
     * @param \Lof\SmtpEmail\Api\Data\EmaillogInterface $emaillog
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Lof\SmtpEmail\Api\Data\EmaillogInterface $emaillog
    );

    /**
     * Delete Emaillog by ID
     * @param string $emaillogId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($emaillogId);
}

