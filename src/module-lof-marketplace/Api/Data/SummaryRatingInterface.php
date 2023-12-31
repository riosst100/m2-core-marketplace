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

namespace Lof\MarketPlace\Api\Data;

interface SummaryRatingInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const TOTAL_COUNT = 'total_count';
    const COUNT = 'count';
    const TOTAL_RATE = 'total_rate';
    const AVERAGE = 'average';
    const PER_RATE = 'per_rate';
    const RATE_ONE = 'rate_one';
    const RATE_TWO = 'rate_two';
    const RATE_THREE = 'rate_three';
    const RATE_FOUR = 'rate_four';
    const RATE_FIVE = 'rate_five';

    /**
     * Get total_count
     * @return int|null
     */
    public function getTotalCount();

    /**
     * Set total_count
     * @param int $total_count
     * @return \Lof\MarketPlace\Api\Data\SummaryRatingInterface
     */
    public function setTotalCount($total_count);

    /**
     * Get count
     * @return int|null
     */
    public function getCount();

    /**
     * Set count
     * @param int $count
     * @return \Lof\MarketPlace\Api\Data\SummaryRatingInterface
     */
    public function setCount($count);

    /**
     * Get total_rate
     * @return float|int|null
     */
    public function getTotalRate();

    /**
     * Set total_rate
     * @param float|int $total_rate
     * @return \Lof\MarketPlace\Api\Data\SummaryRatingInterface
     */
    public function setTotalRate($total_rate);

    /**
     * Get average
     * @return float|int|null
     */
    public function getAverage();

    /**
     * Set average
     * @param float|int $average
     * @return \Lof\MarketPlace\Api\Data\SummaryRatingInterface
     */
    public function setAverage($average);

    /**
     * Get perRate
     * @return float|int|null
     */
    public function getPerRate();

    /**
     * Set perRate
     * @param float|int $perRate
     * @return \Lof\MarketPlace\Api\Data\SummaryRatingInterface
     */
    public function setPerRate($perRate);

    /**
     * Get rateOne
     * @return float|int|null
     */
    public function getRateOne();

    /**
     * Set rateOne
     * @param float|int $rateOne
     * @return \Lof\MarketPlace\Api\Data\SummaryRatingInterface
     */
    public function setRateOne($rateOne);

    /**
     * Get rateTwo
     * @return float|int|null
     */
    public function getRateTwo();

    /**
     * Set rateTwo
     * @param float|int $rateTwo
     * @return \Lof\MarketPlace\Api\Data\SummaryRatingInterface
     */
    public function setRateTwo($rateTwo);

    /**
     * Get rateThree
     * @return float|int|null
     */
    public function getRateThree();

    /**
     * Set rateThree
     * @param float|int $rateThree
     * @return \Lof\MarketPlace\Api\Data\SummaryRatingInterface
     */
    public function setRateThree($rateThree);

    /**
     * Get rateFour
     * @return float|int|null
     */
    public function getRateFour();

    /**
     * Set rateFour
     * @param float|int $rateFour
     * @return \Lof\MarketPlace\Api\Data\SummaryRatingInterface
     */
    public function setRateFour($rateFour);

    /**
     * Get rateFive
     * @return float|int|null
     */
    public function getRateFive();

    /**
     * Set rateFive
     * @param float|int $rateFive
     * @return \Lof\MarketPlace\Api\Data\SummaryRatingInterface
     */
    public function setRateFive($rateFive);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Lof\MarketPlace\Api\Data\SummaryRatingExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Lof\MarketPlace\Api\Data\SummaryRatingExtensionInterface $extensionAttributes
     * @return \Lof\MarketPlace\Api\Data\SummaryRatingInterface
     */
    public function setExtensionAttributes(
        \Lof\MarketPlace\Api\Data\SummaryRatingExtensionInterface $extensionAttributes
    );
}
