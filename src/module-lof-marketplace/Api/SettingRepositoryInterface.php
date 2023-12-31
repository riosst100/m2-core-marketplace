<?php
/**
 * Copyright © asdfasd All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\MarketPlace\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface SettingRepositoryInterface
{

    /**
     * Save Setting
     * @param \Lof\MarketPlace\Api\Data\SettingInterface $setting
     * @return \Lof\MarketPlace\Api\Data\SettingInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Lof\MarketPlace\Api\Data\SettingInterface $setting
    );

    /**
     * Retrieve Setting
     * @param int $settingId
     * @return \Lof\MarketPlace\Api\Data\SettingInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($settingId);

    /**
     * Save Setting
     * @param int $customerId
     * @param \Lof\MarketPlace\Api\Data\SettingInterface $setting
     * @return \Lof\MarketPlace\Api\Data\SettingInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveMySetting(
        int $customerId,
        \Lof\MarketPlace\Api\Data\SettingInterface $setting
    );

    /**
     * Retrieve Setting
     * @param int $customerId
     * @param int $settingId
     * @return \Lof\MarketPlace\Api\Data\SettingInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getMySetting(int $customerId, $settingId);

    /**
     * Retrieve Setting matching the specified criteria.
     * @param int $customerId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lof\MarketPlace\Api\Data\SettingSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getMyList(
        int $customerId,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Retrieve Setting matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lof\MarketPlace\Api\Data\SettingSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Setting
     * @param \Lof\MarketPlace\Api\Data\SettingInterface $setting
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Lof\MarketPlace\Api\Data\SettingInterface $setting
    );

    /**
     * Delete Setting by ID
     * @param string $settingId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($settingId);
}

