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
 * @package    Lofmp_SellerMembership
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerMembership\Api;

interface CancelrequestRepositoryInterface
{
    /**
     * Save SellerMembership cancelrequest
     * @param \Lofmp\SellerMembership\Api\Data\CancelrequestInterface $cancelrequest
     * @return \Lofmp\SellerMembership\Api\Data\CancelrequestInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Lofmp\SellerMembership\Api\Data\CancelrequestInterface $cancelrequest
    );

    /**
     * Retrieve SellerMembership Cancelrequest
     * @param string $entityId
     * @return \Lofmp\SellerMembership\Api\Data\CancelrequestInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($entityId);

    /**
     * Retrieve SellerMembership matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lofmp\SellerMembership\Api\Data\CancelrequestSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete SellerMembership cancelrequest
     * @param \Lofmp\SellerMembership\Api\Data\CancelrequestInterface $cancelrequest
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Lofmp\SellerMembership\Api\Data\CancelrequestInterface $cancelrequest
    );

    /**
     * Delete SellerMembership cancelrequest by ID
     * @param string $entityId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($entityId);

    /**
     * Save SellerMembership cancelrequest
     * @param int $customerId
     * @param \Lofmp\SellerMembership\Api\Data\CancelrequestInterface $cancelrequest
     * @return \Lofmp\SellerMembership\Api\Data\CancelrequestInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveByCustomer(
        $customerId,
        \Lofmp\SellerMembership\Api\Data\CancelrequestInterface $cancelrequest
    );
}
