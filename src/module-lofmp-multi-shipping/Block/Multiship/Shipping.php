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

namespace Lofmp\MultiShipping\Block\Multiship;

class Shipping extends \Magento\Multishipping\Block\Checkout\Shipping
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Filter\DataObject\GridFactory $filterGridFactory
     * @param \Magento\Multishipping\Model\Checkout\Type\Multishipping $multishipping
     * @param \Magento\Tax\Helper\Data $taxHelper
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\ObjectManagerInterface $objectInterface
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Filter\DataObject\GridFactory $filterGridFactory,
        \Magento\Multishipping\Model\Checkout\Type\Multishipping $multishipping,
        \Magento\Tax\Helper\Data $taxHelper,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\ObjectManagerInterface $objectInterface,
        array $data = []
    ) {
        parent::__construct($context, $filterGridFactory, $multishipping, $taxHelper, $priceCurrency, $data);
        $this->_objectManager = $objectInterface;
    }

    /**
     * @return Shipping
     */
    protected function _prepareLayout()
    {
        if (!$this->_objectManager->get(\Lofmp\MultiShipping\Helper\Data::class)->isEnabled()) {
            $this->setTemplate('Magento_Multishipping::checkout/shipping.phtml');
        }
        return parent::_prepareLayout();
    }

    /**
     * @param $address
     * @return false|string[]
     */
    public function getSelectedMethod($address)
    {
        $selectedMethod = str_replace('seller_rates_', '', $address->getShippingMethod());
        $selectedMethods = explode(\Lofmp\MultiShipping\Model\Shipping::METHOD_SEPARATOR, $selectedMethod);
        return $selectedMethods;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address $address
     * @return array|mixed
     */
    public function getShippingRates($address)
    {
        if (!$this->_objectManager->get(\Lofmp\MultiShipping\Helper\Data::class)->isEnabled()) {
            return parent::getShippingRates($address);
        }
        $groups = $address->getGroupedAllShippingRates();

        $rates = [];
        foreach ($groups as $code => $_rates) {
            if ($code == 'seller_rates') {
                foreach ($_rates as $rate) {
                    if (!$rate->isDeleted()) {
                        if (!isset($rates[$rate->getCarrier()])) {
                            $rates[$rate->getCarrier()] = [];
                        }
                        $rates[$rate->getCarrier()][] = $rate;
                    }
                }
            }
        }
        return $rates;
    }

    /**
     * @param $address
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getRatesBySeller($address)
    {
        $addressMthd = $address->getGroupedAllShippingRates();
        $groups = [];
        foreach ($addressMthd as $rateCollection) {
            foreach ($rateCollection as $rate) {
                if ($rate->isDeleted()) {
                    continue;
                }

                if ($rate->getCarrier() == 'seller_rates') {
                    continue;
                }

                $tmp = explode(\Lofmp\MultiShipping\Model\Shipping::SEPARATOR, $rate->getCode());
                $sellerId = isset($tmp[1]) ? $tmp[1] : 'admin';
                // @phpstan-ignore-next-line
                $seller = $this->_objectManager->create(\Lof\MarketPlace\Model\Seller::class);
                if ($sellerId && $sellerId != 'admin') {
                    $seller = $seller->load($sellerId);
                }

                if (!isset($groups[$sellerId])) {
                    $groups[$sellerId] = [];
                }

                $groups[$sellerId]['title'] = $seller->getId()
                    ? $seller->getName()
                    // @phpstan-ignore-next-line
                    : $this->_objectManager->get(\Lof\MarketPlace\Helper\Data::class)
                        ->getStore()
                        ->getWebsite()
                        ->getName();

                if (!isset($groups[$sellerId]['rates'])) {
                    $groups[$sellerId]['rates'] = [];
                }
                $groups[$sellerId]['rates'][] = $rate;
            }
        }
        return $groups;
    }
}
