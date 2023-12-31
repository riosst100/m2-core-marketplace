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

namespace Lof\MarketPlace\Framework\Stdlib\DateTime\Filter;

use Magento\Framework\Stdlib\DateTime\Filter\Date;

class DateTime extends Date
{
    /**
     * DateTime constructor.
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     */
    public function __construct(\Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate)
    {
        parent::__construct($localeDate);
        $this->_localToNormalFilter = new \Zend_Filter_LocalizedToNormalized(
            [
                'date_format' => $this->_localeDate->getDateTimeFormat(
                    \IntlDateFormatter::SHORT
                ),
            ]
        );
        $this->_normalToLocalFilter = new \Zend_Filter_NormalizedToLocalized(
            ['date_format' => \Magento\Framework\Stdlib\DateTime::DATETIME_INTERNAL_FORMAT]
        );
    }

    /**
     * Convert date from localized to internal format
     *
     * @param string $value
     * @return string
     * @throws \Exception
     * @phpcs:disable Magento2.Exceptions.DirectThrow.FoundDirectThrow
     */
    public function filter($value)
    {
        try {
            $dateTime = $this->_localeDate->date($value, null, false, false);
            return $dateTime->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            throw new \Exception("Invalid input datetime format of value '$value'", $e->getCode(), $e);
        }
    }
}