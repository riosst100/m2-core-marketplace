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
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Model\Data;

use Lof\MarketPlace\Api\Data\SummaryRatingInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @codeCoverageIgnore
 */
class SummaryRating extends AbstractExtensibleObject implements SummaryRatingInterface
{

    /**
     * @inheritdoc
     */
    public function getTotalCount()
    {
        return $this->_get(self::TOTAL_COUNT);
    }

    /**
     * @inheritdoc
     */
    public function setTotalCount($total_count)
    {
        return $this->setData(self::TOTAL_COUNT, $total_count);
    }

    /**
     * @inheritdoc
     */
    public function getCount()
    {
        return $this->_get(self::COUNT);
    }

    /**
     * @inheritdoc
     */
    public function setCount($count)
    {
        return $this->setData(self::COUNT, $count);
    }

    /**
     * @inheritdoc
     */
    public function getTotalRate()
    {
        return $this->_get(self::TOTAL_RATE);
    }

    /**
     * @inheritdoc
     */
    public function setTotalRate($total_rate)
    {
        return $this->setData(self::TOTAL_RATE, $total_rate);
    }

    /**
     * @inheritdoc
     */
    public function getAverage()
    {
        return $this->_get(self::AVERAGE);
    }

    /**
     * @inheritdoc
     */
    public function setAverage($average)
    {
        return $this->setData(self::AVERAGE, $average);
    }

    /**
     * @inheritdoc
     */
    public function getRateOne()
    {
        return $this->_get(self::RATE_ONE);
    }

    /**
     * @inheritdoc
     */
    public function setRateOne($rateOne)
    {
        return $this->setData(self::RATE_ONE, $rateOne);
    }

    /**
     * @inheritdoc
     */
    public function getRateTwo()
    {
        return $this->_get(self::RATE_TWO);
    }

    /**
     * @inheritdoc
     */
    public function setRateTwo($rateTwo)
    {
        return $this->setData(self::RATE_TWO, $rateTwo);
    }

    /**
     * @inheritdoc
     */
    public function getRateThree()
    {
        return $this->_get(self::RATE_THREE);
    }

    /**
     * @inheritdoc
     */
    public function setRateThree($rateThree)
    {
        return $this->setData(self::RATE_THREE, $rateThree);
    }

    /**
     * @inheritdoc
     */
    public function getRateFour()
    {
        return $this->_get(self::RATE_FOUR);
    }

    /**
     * @inheritdoc
     */
    public function setRateFour($rateFour)
    {
        return $this->setData(self::RATE_FOUR, $rateFour);
    }

    /**
     * @inheritdoc
     */
    public function getRateFive()
    {
        return $this->_get(self::RATE_FIVE);
    }

    /**
     * @inheritdoc
     */
    public function setRateFive($rateFive)
    {
        return $this->setData(self::RATE_FIVE, $rateFive);
    }

    /**
     * @inheritdoc
     */
    public function getPerRate()
    {
        return $this->_get(self::PER_RATE);
    }

    /**
     * @inheritdoc
     */
    public function setPerRate($peRate)
    {
        return $this->setData(self::PER_RATE, $peRate);
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(
        \Lof\Marketplace\Api\Data\SummaryRatingExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
