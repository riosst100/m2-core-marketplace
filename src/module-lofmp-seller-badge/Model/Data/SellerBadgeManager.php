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

namespace Lofmp\SellerBadge\Model\Data;

use Lofmp\SellerBadge\Api\Data\SellerBadgeManagerInterface;

class SellerBadgeManager extends \Magento\Framework\Api\AbstractExtensibleObject implements SellerBadgeManagerInterface
{
    /**
     * Get manager_id
     * @return string|null
     */
    public function getManagerId()
    {
        return $this->_get(self::MANAGER_ID);
    }

    /**
     * Set manager_id
     * @param string $managerId
     * @return \Lofmp\SellerBadge\Api\Data\SellerBadgeManagerInterface
     */
    public function setManagerId($managerId)
    {
        return $this->setData(self::MANAGER_ID, $managerId);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Lofmp\SellerBadge\Api\Data\SellerBadgeManagerExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Lofmp\SellerBadge\Api\Data\SellerBadgeManagerExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Lofmp\SellerBadge\Api\Data\SellerBadgeManagerExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get badge_id
     * @return string|null
     */
    public function getBadgeId()
    {
        return $this->_get(self::BADGE_ID);
    }

    /**
     * Set badge_id
     * @param string $badgeId
     * @return \Lofmp\SellerBadge\Api\Data\SellerBadgeManagerInterface
     */
    public function setBadgeId($badgeId)
    {
        return $this->setData(self::BADGE_ID, $badgeId);
    }

    /**
     * @return mixed|string|null
     */
    public function getSellerId()
    {
        return $this->_get(self::SELLER_ID);
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
     * @return mixed|string|null
     */
    public function getIsAssign()
    {
        return $this->_get(self::IS_ASSIGN);
    }

    /**
     * @param string $isAssign
     * @return SellerBadgeManagerInterface|SellerBadgeManager
     */
    public function setIsAssign($isAssign)
    {
        return $this->setData(self::IS_ASSIGN, $isAssign);
    }
}
