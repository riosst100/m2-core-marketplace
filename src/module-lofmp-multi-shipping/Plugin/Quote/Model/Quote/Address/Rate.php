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
 * @package    Lofmp_MultiShipping
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\MultiShipping\Plugin\Quote\Model\Quote\Address;

class Rate
{

    /**
     * @var \Lofmp\MultiShipping\Helper\Data
     */
    private $moduleConfig;

    /**
     * @param \Lofmp\MultiShipping\Helper\Data $configData
     */
    public function __construct(
        \Lofmp\MultiShipping\Helper\Data $configData
    ) {
        $this->moduleConfig = $configData;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address\Rate $subject
     * @param $result
     * @param \Magento\Quote\Model\Quote\Address\RateResult\AbstractResult $rate
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterImportShippingRate(
        \Magento\Quote\Model\Quote\Address\Rate $subject,
        $result,
        \Magento\Quote\Model\Quote\Address\RateResult\AbstractResult $rate
    ) {
        if (!$this->moduleConfig->isEnabled()) {
            return $result;
        }

        if ($rate->getMpInfo()) {
            $result->setMpInfo($rate->getMpInfo());
        }

        return $result;
    }
}
