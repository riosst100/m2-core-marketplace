<?php
/**
 * Copyright © asdfasd All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\MarketPlace\Api\Data;

interface SettingInterface
{

    const KEY = 'key';
    const VALUE = 'value';
    const SELLER_ID = 'seller_id';
    const GROUP = 'group';
    const UPDATED_AT = 'updated_at';
    const SCOPE_ID = 'scope_id';
    const SETTING_ID = 'setting_id';
    const PATH = 'path';
    const SCOPE = 'scope';

    /**
     * Get setting_id
     * @return int|null
     */
    public function getSettingId();

    /**
     * Set setting_id
     * @param int $settingId
     * @return \Lof\MarketPlace\Api\Data\SettingInterface
     */
    public function setSettingId($settingId);

    /**
     * Get seller_id
     * @return int|null
     */
    public function getSellerId();

    /**
     * Set seller_id
     * @param int $sellerId
     * @return \Lof\MarketPlace\Api\Data\SettingInterface
     */
    public function setSellerId($sellerId);

    /**
     * Get group
     * @return string|null
     */
    public function getGroup();

    /**
     * Set group
     * @param string $group
     * @return \Lof\MarketPlace\Api\Data\SettingInterface
     */
    public function setGroup($group);

    /**
     * Get key
     * @return string|null
     */
    public function getKey();

    /**
     * Set key
     * @param string $key
     * @return \Lof\MarketPlace\Api\Data\SettingInterface
     */
    public function setKey($key);

    /**
     * Get value
     * @return string|null
     */
    public function getValue();

    /**
     * Set value
     * @param string $value
     * @return \Lof\MarketPlace\Api\Data\SettingInterface
     */
    public function setValue($value);

    /**
     * Get scope
     * @return string|null
     */
    public function getScope();

    /**
     * Set scope
     * @param string $scope
     * @return \Lof\MarketPlace\Api\Data\SettingInterface
     */
    public function setScope($scope);

    /**
     * Get scope_id
     * @return int|null
     */
    public function getScopeId();

    /**
     * Set scope_id
     * @param int $scopeId
     * @return \Lof\MarketPlace\Api\Data\SettingInterface
     */
    public function setScopeId($scopeId);

    /**
     * Get path
     * @return string|null
     */
    public function getPath();

    /**
     * Set path
     * @param string $path
     * @return \Lof\MarketPlace\Api\Data\SettingInterface
     */
    public function setPath($path);

    /**
     * Get updated_at
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set updated_at
     * @param string $updatedAt
     * @return \Lof\MarketPlace\Api\Data\SettingInterface
     */
    public function setUpdatedAt($updatedAt);
}

