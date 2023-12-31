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

interface QuickResponseRepositoryInterface
{
    /**
     * Save QuickResponse
     * @param \Lofmp\Rma\Api\Data\QuickResponseInterface $quickResponse
     * @return \Lofmp\Rma\Api\Data\QuickResponseInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Lofmp\Rma\Api\Data\QuickResponseInterface $quickResponse
    );

    /**
     * Retrieve QuickResponse
     * @param string $quickresponseId
     * @return \Lofmp\Rma\Api\Data\QuickResponseInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($quickresponseId);

    /**
     * Retrieve QuickResponse matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lofmp\Rma\Api\Data\QuickResponseSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete QuickResponse
     * @param \Lofmp\Rma\Api\Data\QuickResponseInterface $quickResponse
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Lofmp\Rma\Api\Data\QuickResponseInterface $quickResponse
    );

    /**
     * Delete QuickResponse by ID
     * @param string $quickresponseId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($quickresponseId);
}
