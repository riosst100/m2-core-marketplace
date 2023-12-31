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

namespace Lofmp\SellerMembership\Model\Source;

class DurationUnit extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    const DURATION_DAY = 'day';
    const DURATION_WEEK = 'week';
    const DURATION_MONTH = 'month';
    const DURATION_YEAR = 'year';

    /**
     * Options array.
     *
     * @var array
     */
    protected $_options = null;

    /**
     * Retrieve all options array.
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = [
                ['label' => __('Day'), 'value' => self::DURATION_DAY],
                ['label' => __('Week'), 'value' => self::DURATION_WEEK],
                ['label' => __('Month'), 'value' => self::DURATION_MONTH],
                ['label' => __('Year'), 'value' => self::DURATION_YEAR],
            ];
        }

        return $this->_options;
    }

    /**
     * Retrieve option array.
     *
     * @return array
     */
    public function getOptionArray()
    {
        $_options = [];
        foreach ($this->getAllOptions() as $option) {
            $_options[$option['value']] = $option['label'];
        }

        return $_options;
    }

    /**
     * Get options as array.
     *
     * @return array
     * @codeCoverageIgnore
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
}
