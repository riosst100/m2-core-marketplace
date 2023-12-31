<?php
/**
 * Copyright © asdfasd All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\MarketPlace\Model;

use Lof\MarketPlace\Api\Data\SettingInterface;
use Magento\Framework\Model\AbstractModel;

class Setting extends AbstractModel implements SettingInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Lof\MarketPlace\Model\ResourceModel\Setting::class);
    }

    /**
     * @inheritDoc
     */
    public function getSettingId()
    {
        return $this->getData(self::SETTING_ID);
    }

    /**
     * @inheritDoc
     */
    public function setSettingId($settingId)
    {
        return $this->setData(self::SETTING_ID, $settingId);
    }

    /**
     * @inheritDoc
     */
    public function getSellerId()
    {
        return $this->getData(self::SELLER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setSellerId($sellerId)
    {
        return $this->setData(self::SELLER_ID, $sellerId);
    }

    /**
     * @inheritDoc
     */
    public function getGroup()
    {
        return $this->getData(self::GROUP);
    }

    /**
     * @inheritDoc
     */
    public function setGroup($group)
    {
        return $this->setData(self::GROUP, $group);
    }

    /**
     * @inheritDoc
     */
    public function getKey()
    {
        return $this->getData(self::KEY);
    }

    /**
     * @inheritDoc
     */
    public function setKey($key)
    {
        return $this->setData(self::KEY, $key);
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        return $this->getData(self::VALUE);
    }

    /**
     * @inheritDoc
     */
    public function setValue($value)
    {
        return $this->setData(self::VALUE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getScope()
    {
        return $this->getData(self::SCOPE);
    }

    /**
     * @inheritDoc
     */
    public function setScope($scope)
    {
        return $this->setData(self::SCOPE, $scope);
    }

    /**
     * @inheritDoc
     */
    public function getScopeId()
    {
        return $this->getData(self::SCOPE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setScopeId($scopeId)
    {
        return $this->setData(self::SCOPE_ID, $scopeId);
    }

    /**
     * @inheritDoc
     */
    public function getPath()
    {
        return $this->getData(self::PATH);
    }

    /**
     * @inheritDoc
     */
    public function setPath($path)
    {
        return $this->setData(self::PATH, $path);
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
}

