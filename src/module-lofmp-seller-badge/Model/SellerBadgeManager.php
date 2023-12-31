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
 * @package    Lofmp_SellerBadge
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerBadge\Model;

use Lofmp\SellerBadge\Api\Data\SellerBadgeManagerExtensionInterface;
use Lofmp\SellerBadge\Api\Data\SellerBadgeManagerInterface;
use Lofmp\SellerBadge\Api\Data\SellerBadgeManagerInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Model\AbstractModel;

class SellerBadgeManager extends AbstractModel implements SellerBadgeManagerInterface
{
    /**
     * @var SellerBadgeManagerInterfaceFactory
     */
    protected $sellerbadgemanagerDataFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var string
     */
    protected $_eventPrefix = 'lofmp_sellerbadge_manager';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param SellerBadgeManagerInterfaceFactory $sellerbadgemanagerDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Lofmp\SellerBadge\Model\ResourceModel\SellerBadgeManager $resource
     * @param \Lofmp\SellerBadge\Model\ResourceModel\SellerBadgeManager\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        SellerBadgeManagerInterfaceFactory $sellerbadgemanagerDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Lofmp\SellerBadge\Model\ResourceModel\SellerBadgeManager $resource,
        \Lofmp\SellerBadge\Model\ResourceModel\SellerBadgeManager\Collection $resourceCollection,
        array $data = []
    ) {
        $this->sellerbadgemanagerDataFactory = $sellerbadgemanagerDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve sellerbadgemanager model with sellerbadgemanager data
     * @return SellerBadgeManagerInterface
     */
    public function getDataModel()
    {
        $sellerbadgemanagerData = $this->getData();

        $sellerbadgemanagerDataObject = $this->sellerbadgemanagerDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $sellerbadgemanagerDataObject,
            $sellerbadgemanagerData,
            SellerBadgeManagerInterface::class
        );

        return $sellerbadgemanagerDataObject;
    }

    /**
     * @return array|mixed|string|null
     */
    public function getImage()
    {
        return $this->getData(\Lofmp\SellerBadge\Api\Data\SellerBadgeInterface::IMAGE);
    }

    /**
     * @return array|mixed|string|null
     */
    public function getManagerId()
    {
        return $this->getData(self::MANAGER_ID);
    }

    /**
     * @param string $managerId
     * @return SellerBadgeManagerInterface|SellerBadgeManager
     */
    public function setManagerId($managerId)
    {
        return $this->setData(self::MANAGER_ID, $managerId);
    }

    /**
     * @return array|mixed|string|null
     */
    public function getSellerId()
    {
        return $this->getData(self::SELLER_ID);
    }

    /**
     * @param string $sellerId
     * @return SellerBadgeManagerInterface|SellerBadgeManager
     */
    public function setSellerId($sellerId)
    {
        return $this->setData(self::SELLER_ID, $sellerId);
    }

    /**
     * @return array|mixed|string|null
     */
    public function getIsAssign()
    {
        return $this->getData(self::IS_ASSIGN);
    }

    /**
     * @param string $isAssign
     * @return SellerBadgeManagerInterface|SellerBadgeManager
     */
    public function setIsAssign($isAssign)
    {
        return $this->setData(self::IS_ASSIGN, $isAssign);
    }

    /**
     * @return SellerBadgeManagerExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @param SellerBadgeManagerExtensionInterface $extensionAttributes
     * @return SellerBadgeManager
     */
    public function setExtensionAttributes(SellerBadgeManagerExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * @return array|mixed|string|null
     */
    public function getBadgeId()
    {
        return $this->getData(self::BADGE_ID);
    }

    /**
     * @param string $badgeId
     * @return SellerBadgeManagerInterface|SellerBadgeManager
     */
    public function setBadgeId($badgeId)
    {
        return $this->setData(self::BADGE_ID, $badgeId);
    }
}
