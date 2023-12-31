<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://venustheme.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_Faq
 * @copyright  Copyright (c) 2018 Landofcoder (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */

namespace Lofmp\Faq\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    protected $_storeManager;
    protected $_objectManager;
    protected $settingFactory;
    protected $_localeDate;

    const PATH_MPFAQ = 'lofmpfaq/';

    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        \Lofmp\Faq\Model\SettingFactory $settingFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
    )
    {
        $this->_objectManager = $objectManager;
        $this->_storeManager = $storeManager;
        $this->settingFactory = $settingFactory;
        $this->_localeDate = $localeDate;
        parent::__construct($context);
    }

    public function getConfig($filed, $storeId = null)
    {
        return $this->scopeConfig->getValue(self::PATH_MPFAQ . $filed, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getSellerSettings($sellerId)
    {
        return $this->settingFactory->create()->load($sellerId, "seller_id");
    }

    public function getFormatDate($date, $type = 'full'){
        $result = '';
        switch ($type) {
            case 'full':
            $result = $this->formatDate($date, \IntlDateFormatter::FULL);
            break;
            case 'long':
            $result = $this->formatDate($date, \IntlDateFormatter::LONG);
            break;
            case 'medium':
            $result = $this->formatDate($date, \IntlDateFormatter::MEDIUM);
            break;
            case 'short':
            $result = $this->formatDate($date, \IntlDateFormatter::SHORT);
            break;
        }
        return $result;
    }

    public function formatDate(
        $date = null,
        $format = \IntlDateFormatter::SHORT,
        $showTime = false,
        $timezone = null
        ) {
        $date = $date instanceof \DateTimeInterface ? $date : new \DateTime($date);
        return $this->_localeDate->formatDateTime(
            $date,
            $format,
            $showTime ? $format : \IntlDateFormatter::NONE,
            null,
            $timezone
            );
    }

}

?>