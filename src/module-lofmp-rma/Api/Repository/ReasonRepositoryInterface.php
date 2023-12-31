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

interface ReasonRepositoryInterface
{
    /**
     * Save reason
     * @param \Lofmp\Rma\Api\Data\ReasonInterface $reason
     * @return \Lofmp\Rma\Api\Data\ReasonInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Lofmp\Rma\Api\Data\ReasonInterface $reason
    );

    /**
     * Retrieve reason
     * @param string $reasonId
     * @return \Lofmp\Rma\Api\Data\ReasonInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($reasonId);

    /**
     * Retrieve reason matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lofmp\Rma\Api\Data\ReasonSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete reason
     * @param \Lofmp\Rma\Api\Data\ReasonInterface $reason
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Lofmp\Rma\Api\Data\ReasonInterface $reason
    );

    /**
     * Delete reason by ID
     * @param string $reasonId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($reasonId);
}
