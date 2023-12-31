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
use Lof\MarketPermissions\Api\Data\RoleInterface;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\MessageQueue\PoisonPill\PoisonPillPutInterface;
use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

/**
 * Role data transfer object.
 */
class Role extends AbstractExtensibleModel implements RoleInterface
{
    /**
     * Cache tag for seller role
     */
    const CACHE_TAG = 'lof_marketplace_role';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'marketplace_role';

    /**
     * Role permissions.
     *
     * @var PermissionInterface[]
     */
    private $permissions = [];

    /**
     * @var mixed
     */
    private $pillPut;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param AbstractResource $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     * @param PoisonPillPutInterface|null $pillPut
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = [],
        PoisonPillPutInterface $pillPut = null
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $resource,
            $resourceCollection,
            $data
        );

        $this->pillPut = $pillPut ?: ObjectManager::getInstance()->get(PoisonPillPutInterface::class);
    }

    /**
     * Initialize resource model.
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(\Lof\MarketPermissions\Model\ResourceModel\Role::class);
    }

    /**
     * @inheritDoc
     */
    public function setId($id)
    {
        return $this->setData(self::ROLE_ID, $id);
    }

    /**
     * @inheritDoc
     */
    public function setRoleName($name)
    {
        return $this->setData(self::ROLE_NAME, $name);
    }

    /**
     * @inheritDoc
     */
    public function setSellerId($id)
    {
        return $this->setData(self::SELLER_ID, $id);
    }

    /**
     * @inheritDoc
     */
    public function setPermissions(array $permissions)
    {
        $this->permissions = $permissions;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->getData(self::ROLE_ID);
    }

    /**
     * @inheritDoc
     */
    public function getRoleName()
    {
        return $this->getData(self::ROLE_NAME);
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
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * @inheritDoc
     */
    public function afterSave()
    {
        $this->pillPut->put();
        return parent::afterSave();
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes()
    {
        if (!$this->_getExtensionAttributes()) {
            $this->setExtensionAttributes(
                $this->extensionAttributesFactory->create(get_class($this))
            );
        }
        return $this->_getExtensionAttributes();
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(\Lof\MarketPermissions\Api\Data\RoleExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
