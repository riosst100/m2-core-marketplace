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

interface RmaRepositoryInterface
{
    /**
     * Save rma
     * @param \Lofmp\Rma\Api\Data\RmaInterface $rma
     * @return \Lofmp\Rma\Api\Data\RmaInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Lofmp\Rma\Api\Data\RmaInterface $rma);

    /**
     * Retrieve rma
     * @param string $rmaId
     * @return \Lofmp\Rma\Api\Data\RmaInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($rmaId);

    /**
     * Retrieve rma matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lofmp\Rma\Api\Data\RmaSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete rma
     * @param \Lofmp\Rma\Api\Data\RmaInterface $rma
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Lofmp\Rma\Api\Data\RmaInterface $rma);

    /**
     * Delete rma by ID
     * @param string $rmaId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($rmaId);
}
