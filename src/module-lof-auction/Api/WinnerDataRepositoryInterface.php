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
 * @package    Lof_Auction
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
namespace Lof\Auction\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface WinnerDataRepositoryInterface
 * @package Lof\Auction\Api
 */
interface WinnerDataRepositoryInterface
{
    /**
     * Save WinnerData
     * @param \Lof\Auction\Api\Data\WinnerDataInterface $winnerdata
     * @return \Lof\Auction\Api\Data\WinnerDataInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save($winnerdata);

    /**
     * Retrieve WinnerData
     * @param int $entityId
     * @return \Lof\Auction\Api\Data\WinnerDataInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($entityId);

    /**
     * Retrieve WinnerData matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lof\Auction\Api\Data\WinnerDataSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete WinnerData
     * @param int $entityId
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete($entityId);

    /**
     * Delete WinnerData by ID
     * @param int $entityId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($entityId);
}
