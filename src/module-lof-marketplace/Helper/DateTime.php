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

namespace Lof\MarketPlace\Helper;

class DateTime extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_dateTime;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param Data $helperData
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        Data $helperData
    ) {
        $this->_dateTime = $dateTime;
        $this->_localeDate = $localeDate;
        $this->_helperData = $helperData;

        parent::__construct($context);
    }

    /**
     * @return Data
     */
    public function getHelperData()
    {
        return $this->_helperData;
    }

    /**
     * @return \Magento\Framework\Stdlib\DateTime\DateTime
     */
    public function getDateTime()
    {
        return $this->_dateTime;
    }

    /**
     * Get timezone date time, convert date time to timezone
     * @param string $dateTime
     * @return string
     */
    public function getTimezoneDateTime($dateTime = 'today')
    {
        if ($dateTime === 'today' || !$dateTime) {
            $dateTime = $this->_dateTime->gmtDate();
        }

        $today = $this->_localeDate
            ->date(
                new \DateTime($dateTime)
            )->format('Y-m-d H:i:s');
        return $today;
    }

    /**
     * Get Time zone name
     * @return string
     */
    public function getTimezoneName()
    {
        return $this->_localeDate->getConfigTimezone(\Magento\Store\Model\ScopeInterface::SCOPE_STORES);
    }
}
