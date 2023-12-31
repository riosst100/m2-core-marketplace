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

namespace Lofmp\SellerBadge\Api\Data;

interface SellerBadgeManagerInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const BADGE_ID = 'badge_id';
    const MANAGER_ID = 'manager_id';
    const SELLER_ID = 'seller_id';
    const IS_ASSIGN = 'is_assign';

    /**
     * Get manager_id
     * @return string|null
     */
    public function getManagerId();

    /**
     * Set manager_id
     * @param string $managerId
     * @return \Lofmp\SellerBadge\Api\Data\SellerBadgeManagerInterface
     */
    public function setManagerId($managerId);

    /**
     * Get seller_id
     * @return string|null
     */
    public function getSellerId();

    /**
     * Set seller_id
     * @param string $sellerId
     * @return \Lofmp\SellerBadge\Api\Data\SellerBadgeManagerInterface
     */
    public function setSellerId($sellerId);

    /**
     * Get is_assign
     * @return string|null
     */
    public function getIsAssign();

    /**
     * Set is_assign
     * @param string $isAssign
     * @return \Lofmp\SellerBadge\Api\Data\SellerBadgeManagerInterface
     */
    public function setIsAssign($isAssign);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Lofmp\SellerBadge\Api\Data\SellerBadgeManagerExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Lofmp\SellerBadge\Api\Data\SellerBadgeManagerExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Lofmp\SellerBadge\Api\Data\SellerBadgeManagerExtensionInterface $extensionAttributes
    );

    /**
     * Get badge_id
     * @return string|null
     */
    public function getBadgeId();

    /**
     * Set badge_id
     * @param string $badgeId
     * @return \Lofmp\SellerBadge\Api\Data\SellerBadgeManagerInterface
     */
    public function setBadgeId($badgeId);
}
