<?php
/**
 * Data Model implementing the Address interface
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lofmp\CouponCode\Model\Data;

// use Lofmp\CouponCode\Api\Data\ConditionInterface;
use Lofmp\CouponCode\Api\Data\RuleInterface;

/**
 * Class Rule
 *
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @codeCoverageIgnore
 */
class Rule extends \Magento\Framework\Api\AbstractExtensibleObject implements
    \Lofmp\CouponCode\Api\Data\RuleInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRuleId()
    {
        return $this->_get(self::KEY_RULE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setRuleId($ruleId)
    {
        return $this->setData(self::KEY_RULE_ID, $ruleId);
    }

    /**
     * {@inheritdoc}
     */
    public function getCouponRuleId()
    {
        return $this->_get(self::KEY_COUPON_RULE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCouponRuleId($coupon_rule_id)
    {
        return $this->setData(self::KEY_COUPON_RULE_ID, $coupon_rule_id);
    }

    /**
     * {@inheritdoc}
     */
    public function getSimpleAction()
    {
        return $this->_get(self::KEY_SIMPLE_ACTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setSimpleAction($simple_action)
    {
        return $this->setData(self::KEY_SIMPLE_ACTION, $simple_action);
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscountAmount()
    {
        return $this->_get(self::KEY_DISCOUNT_AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setDiscountAmount($discount_amount)
    {
        return $this->setData(self::KEY_DISCOUNT_AMOUNT, $discount_amount);
    }

    /**
     * {@inheritdoc}
     */
    public function getConditions()
    {
        return $this->_get(self::KEY_CONDITIONS);
    }

    /**
     * {@inheritdoc}
     */
    public function setConditions($conditions)
    {
        return $this->setData(self::KEY_CONDITIONS, $conditions);
    }

    /**
     * {@inheritdoc}
     */
    public function getActions()
    {
        return $this->_get(self::KEY_ACTIONS);
    }

    /**
     * {@inheritdoc}
     */
    public function setActions($actions)
    {
        return $this->setData(self::KEY_ACTIONS, $actions);
    }

    /**
     * {@inheritdoc}
     */
    public function getCouponsGenerated()
    {
        return $this->_get(self::KEY_COUPONS_GENERATED);
    }

    /**
     * {@inheritdoc}
     */
    public function setCouponsGenerated($coupons_generated)
    {
        return $this->setData(self::KEY_COUPONS_GENERATED, $coupons_generated);
    }

    /**
     * {@inheritdoc}
     */
    public function getRuleName()
    {
        return $this->_get(self::KEY_RULE_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setRuleName($rule_name)
    {
        return $this->setData(self::KEY_RULE_NAME, $rule_name);
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeLength()
    {
        return $this->_get(self::KEY_CODE_LENGTH);
    }

    /**
     * {@inheritdoc}
     */
    public function setCodeLength($code_length)
    {
        return $this->setData(self::KEY_CODE_LENGTH, $code_length);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        \Lofmp\CouponCode\Api\Data\RuleExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

}
