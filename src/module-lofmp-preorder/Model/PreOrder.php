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
 * @package    Lof_PreOrder
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */


namespace Lofmp\PreOrder\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Lof\PreOrder\Api\Data\PreOrderInterface;
use Magento\Framework\Registry;

class PreOrder extends AbstractModel implements PreOrderInterface
{
    /**
     * Initialize resource model
     *
     * @param Context $context
     * @param Registry $registry
     * phpcs:disable Generic.CodeAnalysis.UselessOverridingMethod.Found
     */
    public function __construct(
        Context $context,
        Registry $registry
    ) {
        parent::__construct($context, $registry);
    }

    /**
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    public function _construct()
    {
        $this->_init(ResourceModel\PreOrder::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     *
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @param $id
     * @return PreOrder
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
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
    public function setItemId($itemId)
    {
        return $this->setData(self::ITEM_ID, $itemId);
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
     * @SuppressWarnings(PHPMD.CamelCaseParameterName)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     */
    public function setProductId($product_id)
    {
        return $this->setData(self::PRODUCT_ID, $product_id);
    }

    /**
     * {@inheritdoc}
     */
    public function getParentId()
    {
        return $this->getData(self::PARENT_ID);
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CamelCaseParameterName)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     */
    public function setParentId($parent_id)
    {
        return $this->setData(self::PARENT_ID, $parent_id);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CamelCaseParameterName)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     */
    public function setCustomerId($customer_id)
    {
        return $this->setData(self::CUSTOMER_ID, $customer_id);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerEmail()
    {
        return $this->getData(self::CUSTOMER_EMAIL);
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CamelCaseParameterName)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     */
    public function setCustomerEmail($customer_email)
    {
        return $this->setData(self::CUSTOMER_EMAIL, $customer_email);
    }

    /**
     * {@inheritdoc}
     */
    public function getPreorderPercent()
    {
        return $this->getData(self::PREORDER_PERCENT);
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CamelCaseParameterName)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     */
    public function setPreorderPercent($preorder_percent)
    {
        return $this->setData(self::PREORDER_PERCENT, $preorder_percent);
    }

    /**
     * {@inheritdoc}
     */
    public function getPaidAmount()
    {
        return $this->getData(self::PAID_AMOUNT);
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CamelCaseParameterName)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     */
    public function setPaidAmount($paid_amount)
    {
        return $this->setData(self::PAID_AMOUNT, $paid_amount);
    }

    /**
     * {@inheritdoc}
     */
    public function getRemainingAmount()
    {
        return $this->getData(self::REMAINING_AMOUNT);
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CamelCaseParameterName)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     */
    public function setRemainingAmount($remaining_amount)
    {
        return $this->setData(self::REMAINING_AMOUNT, $remaining_amount);
    }

    /**
     * {@inheritdoc}
     */
    public function getQty()
    {
        return $this->getData(self::QTY);
    }

    /**
     * {@inheritdoc}
     */
    public function setQty($qty)
    {
        return $this->setData(self::QTY, $qty);
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        return $this->getData(self::TYPE, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
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
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setTime($time)
    {
        return $this->setData(self::TIME, $time);
    }

    /**
     * {@inheritdoc}
     */
    public function getTime()
    {
        return $this->getData(self::TIME);
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CamelCaseParameterName)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     */
    public function setCreatedAt($created_at)
    {
        return $this->setData(self::CREATED_AT, $created_at);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }
}
