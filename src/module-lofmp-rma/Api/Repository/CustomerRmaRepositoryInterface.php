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

interface CustomerRmaRepositoryInterface
{

    /**
     * Save rma
     * @param int $customerId
     * @param \Lofmp\Rma\Api\Data\RmaInterface $rma
     * @return \Lofmp\Rma\Api\Data\RmaInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save($customerId, \Lofmp\Rma\Api\Data\RmaInterface $rma);

    /**
     * Save rma New Api
     * @param int $customerId
     * @param \Lofmp\Rma\Api\Data\RmaFrontendInterface $rma
     * @return \Lofmp\Rma\Api\Data\RmaInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveRma($customerId, \Lofmp\Rma\Api\Data\RmaFrontendInterface $rma);

    /**
     * Save Bundle RMA
     * @param int $customerId
     * @param \Lofmp\Rma\Api\Data\RmaInterface $rma
     * @return \Lofmp\Rma\Api\Data\RmaInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveBundle($customerId, \Lofmp\Rma\Api\Data\RmaInterface $rma);

    /**
     * Retrieve rma
     * @param int $customerId
     * @param string $rmaId
     * @return \Lofmp\Rma\Api\Data\RmaInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($customerId, $rmaId);

    /**
     * Retrieve rma matching the specified criteria.
     * @param int $customerId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lofmp\Rma\Api\Data\RmaSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        $customerId,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );
}
