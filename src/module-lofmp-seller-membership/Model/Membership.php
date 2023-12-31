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
 * @package    Lofmp_SellerMembership
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerMembership\Model;

use Lofmp\SellerMembership\Api\Data\MembershipInterface;

class Membership extends \Magento\Framework\Model\AbstractModel implements MembershipInterface
{
    const ENABLE = 1;
    const DISABLE = 0;
    const PENDING = 2;

    /**
     * Membership constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context, $registry);
    }

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(\Lofmp\SellerMembership\Model\ResourceModel\Membership::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getMembershipId()
    {
        return $this->getData(self::MEMBERSHIP_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setMembershipId($membership_id)
    {
        return $this->setData(self::MEMBERSHIP_ID, $membership_id);
    }

    /**
     * {@inheritdoc}
     */
    public function getGroupId()
    {
        return $this->getData(self::GROUP_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setGroupId($group_id)
    {
        return $this->setData(self::GROUP_ID, $group_id);
    }

    /**
     * {@inheritdoc}
     */
    public function getSellerId()
    {
        return $this->getData(self::SELLER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setSellerId($seller_id)
    {
        return $this->setData(self::SELLER_ID, $seller_id);
    }

    /**
     * Get name
     * @return string|null
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function getDuration()
    {
        return $this->getData(self::DURATION);
    }

    /**
     * {@inheritdoc}
     */
    public function setDuration($duration)
    {
        return $this->setData(self::DURATION, $duration);
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice()
    {
        return $this->getData(self::PRICE);
    }

    /**
     * {@inheritdoc}
     */
    public function setPrice($price)
    {
        return $this->setData(self::PRICE, $price);
    }

    /**
     * {@inheritdoc}
     */
    public function getExpirationDate()
    {
        return $this->getData(self::EXPIRATION_DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setExpirationDate($expiration_date)
    {
        return $this->setData(self::EXPIRATION_DATE, $expiration_date);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($created_at)
    {
        return $this->setData(self::CREATED_AT, $created_at);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setProductId($product_id)
    {
        return $this->setData(self::PRODUCT_ID, $product_id);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductOptions()
    {
        return $this->getData(self::PRODUCT_OPTIONS);
    }

    /**
     * {@inheritdoc}
     */
    public function setProductOptions($product_options)
    {
        return $this->setData(self::PRODUCT_OPTIONS, $product_options);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemId()
    {
        return $this->getData(self::ITEM_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setItemId($item_id)
    {
        return $this->setData(self::ITEM_ID, $item_id);
    }

    /**
     * {@inheritdoc}
     */
    public function getBeforeSellerGroupId()
    {
        return $this->getData(self::BEFORE_SELLER_GROUP_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setBeforeSellerGroupId($before_seller_group_id)
    {
        return $this->setData(self::BEFORE_SELLER_GROUP_ID, $before_seller_group_id);
    }
}
