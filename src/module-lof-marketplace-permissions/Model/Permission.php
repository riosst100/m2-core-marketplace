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
 * @package    Lof_MarketPermissions
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
namespace Lof\MarketPermissions\Model;

use Lof\MarketPermissions\Api\Data\PermissionInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Permission.
 */
class Permission extends AbstractModel implements PermissionInterface
{
    /**
     * Cache tag.
     */
    const CACHE_TAG = 'lof_marketplace_permissions';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'marketplace_permissions';

    /**
     * Initialize resource model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Lof\MarketPermissions\Model\ResourceModel\Permission::class);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        return $this->setData(self::PERMISSION_ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getData(self::PERMISSION_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getRoleId()
    {
        return $this->getData(self::ROLE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setRoleId($id)
    {
        return $this->setData(self::ROLE_ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceId()
    {
        return $this->getData(self::RESOURCE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setResourceId($id)
    {
        return $this->setData(self::RESOURCE_ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getPermission()
    {
        return $this->getData(self::PERMISSION);
    }

    /**
     * {@inheritdoc}
     */
    public function setPermission($permission)
    {
        return $this->setData(self::PERMISSION, $permission);
    }
}
