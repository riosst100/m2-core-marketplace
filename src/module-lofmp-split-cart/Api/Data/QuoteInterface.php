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
 * @package    Lofmp_SplitCart
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
declare(strict_types=1);

namespace Lofmp\SplitCart\Api\Data;

interface QuoteInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const ENTITY_ID = 'entity_id';
    const QUOTE_ID = 'quote_id';
    const PARENT_ID = 'parent_id';
    const SELLER_ID = 'seller_id';
    const IS_ACTIVE = 'is_active';
    const IS_ORDERED = 'is_ordered';

    /**
     * Get entity_id
     * @return string|null
     */
    public function getEntityId();

    /**
     * Set entity_id
     * @param string $entityId
     * @return \Lofmp\SplitCart\Api\Data\QuoteInterface
     */
    public function setEntityId($entityId);

    /**
     * Get quote_id
     * @return string|null
     */
    public function getQuoteId();

    /**
     * Set quote_id
     * @param string $quoteId
     * @return \Lofmp\SplitCart\Api\Data\QuoteInterface
     */
    public function setQuoteId($quoteId);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Lofmp\SplitCart\Api\Data\QuoteExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Lofmp\SplitCart\Api\Data\QuoteExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Lofmp\SplitCart\Api\Data\QuoteExtensionInterface $extensionAttributes
    );

    /**
     * Get parent_id
     * @return string|null
     */
    public function getParentId();

    /**
     * Set parent_id
     * @param string $parentId
     * @return \Lofmp\SplitCart\Api\Data\QuoteInterface
     */
    public function setParentId($parentId);

    /**
     * Get seller_id
     * @return string|null
     */
    public function getSellerId();

    /**
     * Set seller_id
     * @param string $sellerId
     * @return \Lofmp\SplitCart\Api\Data\QuoteInterface
     */
    public function setSellerId($sellerId);

    /**
     * Get is_active
     * @return string|null
     */
    public function getIsActive();

    /**
     * Set is_active
     * @param string $isActive
     * @return \Lofmp\SplitCart\Api\Data\QuoteInterface
     */
    public function setIsActive($isActive);

    /**
     * Get is_ordered
     * @return string|null
     */
    public function getIsOrdered();

    /**
     * Set is_ordered
     * @param string $isOrdered
     * @return \Lofmp\SplitCart\Api\Data\QuoteInterface
     */
    public function setIsOrdered($isOrdered);
}
