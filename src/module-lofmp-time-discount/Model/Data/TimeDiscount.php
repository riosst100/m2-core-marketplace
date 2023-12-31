<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_TimeDiscount
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\TimeDiscount\Model\Data;

use Lofmp\TimeDiscount\Api\Data\TimeDiscountInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @codeCoverageIgnore
 */
class TimeDiscount extends AbstractExtensibleObject implements TimeDiscountInterface
{
    /**
     * @inheritDoc
     */
    public function getStart()
    {
        return $this->_get(self::START);
    }

    /**
     * @inheritDoc
     */
    public function setStart($start)
    {
        return $this->setData(self::START, $start);
    }

    /**
     * @inheritDoc
     */
    public function getEnd()
    {
        return $this->_get(self::END);
    }

    /**
     * @inheritDoc
     */
    public function setEnd($end)
    {
        return $this->setData(self::END, $end);
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return $this->_get(self::TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * @inheritDoc
     */
    public function getDiscount()
    {
        return $this->_get(self::DISCOUNT);
    }

    /**
     * @inheritDoc
     */
    public function setDiscount($discount)
    {
        return $this->setData(self::DISCOUNT, $discount);
    }

    /**
     * @inheritDoc
     */
    public function getOrder()
    {
        return $this->_get(self::ORDER);
    }

    /**
     * @inheritDoc
     */
    public function setOrder($order)
    {
        return $this->setData(self::ORDER, $order);
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
        \Lofmp\TimeDiscount\Api\Data\TimeDiscountExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
