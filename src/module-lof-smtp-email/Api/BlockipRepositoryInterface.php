<?php
/**
 * Copyright © landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\SmtpEmail\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface BlockipRepositoryInterface
{

    /**
     * Save Blockip
     * @param \Lof\SmtpEmail\Api\Data\BlockipInterface $blockip
     * @return \Lof\SmtpEmail\Api\Data\BlockipInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Lof\SmtpEmail\Api\Data\BlockipInterface $blockip
    );

    /**
     * Retrieve Blockip
     * @param string $blockipId
     * @return \Lof\SmtpEmail\Api\Data\BlockipInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($blockipId);

    /**
     * Retrieve Blockip matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lof\SmtpEmail\Api\Data\BlockipSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Blockip
     * @param \Lof\SmtpEmail\Api\Data\BlockipInterface $blockip
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Lof\SmtpEmail\Api\Data\BlockipInterface $blockip
    );

    /**
     * Delete Blockip by ID
     * @param string $blockipId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($blockipId);
}

