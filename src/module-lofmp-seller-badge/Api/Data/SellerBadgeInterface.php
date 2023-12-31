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

interface SellerBadgeInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const IS_ACTIVE = 'is_active';
    const BADGE_ID = 'badge_id';
    const RANK = 'rank';
    const IMAGE = 'image';
    const DESCRIPTION = 'description';
    const NAME = 'name';
    const UPDATED_AT = 'updated_at';
    const CREATED_AT = 'created_at';

    /**
     * Get badge_id
     * @return string|null
     */
    public function getBadgeId();

    /**
     * Set badge_id
     * @param string $badgeId
     * @return \Lofmp\SellerBadge\Api\Data\SellerBadgeInterface
     */
    public function setBadgeId($badgeId);

    /**
     * Get name
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     * @param string $name
     * @return \Lofmp\SellerBadge\Api\Data\SellerBadgeInterface
     */
    public function setName($name);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Lofmp\SellerBadge\Api\Data\SellerBadgeExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Lofmp\SellerBadge\Api\Data\SellerBadgeExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Lofmp\SellerBadge\Api\Data\SellerBadgeExtensionInterface $extensionAttributes
    );

    /**
     * Get image
     * @return string|null
     */
    public function getImage();

    /**
     * Set image
     * @param string $image
     * @return \Lofmp\SellerBadge\Api\Data\SellerBadgeInterface
     */
    public function setImage($image);

    /**
     * Get description
     * @return string|null
     */
    public function getDescription();

    /**
     * Set description
     * @param string $description
     * @return \Lofmp\SellerBadge\Api\Data\SellerBadgeInterface
     */
    public function setDescription($description);

    /**
     * Get rank
     * @return string|null
     */
    public function getRank();

    /**
     * Set rank
     * @param string $rank
     * @return \Lofmp\SellerBadge\Api\Data\SellerBadgeInterface
     */
    public function setRank($rank);

    /**
     * Get is_active
     * @return string|null
     */
    public function getIsActive();

    /**
     * Set is_active
     * @param string $isActive
     * @return \Lofmp\SellerBadge\Api\Data\SellerBadgeInterface
     */
    public function setIsActive($isActive);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $createdAt
     * @return \Lofmp\SellerBadge\Api\Data\SellerBadgeInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Get updated_at
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set updated_at
     * @param string $updatedAt
     * @return \Lofmp\SellerBadge\Api\Data\SellerBadgeInterface
     */
    public function setUpdatedAt($updatedAt);
}
