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
 * @package    Lof_Auction
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\Auction\Model\ResourceModel\Product\Source;

use Magento\Framework\Data\OptionSourceInterface;

class AuctionStatus implements OptionSourceInterface
{
    const STATUS_TIME_END = 0;
    const STATUS_PROCESS = 1;
    const STATUS_WINNER_NOT_DEFINED = 2;
    const STATUS_CANCELED = 3;
    const STATUS_COMPLETE = 4;
    const STATUS_PROCESS_HODING = 5;
    /**
     * Get Auction status type labels array.
     *
     * @return array
     */
    public function getOptionArray()
    {
        $options = [
            self::STATUS_TIME_END => __('Auction Time End'),
            self::STATUS_PROCESS => __('Processing'),
            self::STATUS_WINNER_NOT_DEFINED => __('Winner not defined'),
            self::STATUS_CANCELED => __('Canceled by admin'),
            self::STATUS_COMPLETE => __('Completed')
        ];

        return $options;
    }

    /**
     * Get Auction status labels array with empty value for option element.
     *
     * @return array
     */
    public function getAllOptions()
    {
        $res = $this->getOptions();
        array_unshift($res, ['value' => '', 'label' => '']);

        return $res;
    }

    /**
     * Get Auction type labels array for option element.
     *
     * @return array
     */
    public function getOptions()
    {
        $res = [];
        foreach ($this->getOptionArray() as $index => $value) {
            $res[] = ['value' => $index, 'label' => $value];
        }

        return $res;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->getOptions();
    }
}
