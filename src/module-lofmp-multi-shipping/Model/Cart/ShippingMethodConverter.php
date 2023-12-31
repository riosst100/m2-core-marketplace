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

namespace Lofmp\MultiShipping\Model\Cart;

class ShippingMethodConverter extends \Magento\Quote\Model\Cart\ShippingMethodConverter
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Lofmp\MultiShipping\Helper\Data
     */
    protected $_helperData;

    /**
     * @var \Lof\MarketPlace\Model\SellerFactory
     */
    protected $_sellerFactory;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $_marketplaceHelperData;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Quote\Api\Data\ShippingMethodInterfaceFactory $shippingMethodDataFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Store\Model\StoreManagerInterface $_storeManager
     * @param \Magento\Framework\ObjectManagerInterface $objectInterface
     * @param \Magento\Tax\Helper\Data $taxHelper
     * @param \Lofmp\MultiShipping\Helper\Data $helperData
     * @param \Lof\MarketPlace\Helper\Data $marketplaceHelperData
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerFactory
     */
    public function __construct(
        \Magento\Quote\Api\Data\ShippingMethodInterfaceFactory $shippingMethodDataFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Store\Model\StoreManagerInterface $_storeManager,
        \Magento\Framework\ObjectManagerInterface $objectInterface,
        \Magento\Tax\Helper\Data $taxHelper,
        \Lofmp\MultiShipping\Helper\Data $helperData,
        \Lof\MarketPlace\Helper\Data $marketplaceHelperData,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory
    ) {
        parent::__construct($shippingMethodDataFactory, $storeManager, $taxHelper);
        $this->_objectManager = $objectInterface;
        $this->_helperData = $helperData;
        $this->_sellerFactory = $sellerFactory;
        $this->_marketplaceHelperData = $marketplaceHelperData;
        $this->_storeManager = $_storeManager;
    }

    /**
     * Converts a specified rate model to a shipping method data object.
     *
     * @param string $quoteCurrencyCode The quote currency code.
     * @param \Magento\Quote\Model\Quote\Address\Rate $rateModel The rate model.
     * @return \Magento\Quote\Api\Data\ShippingMethodInterface Shipping method data object.
     */
    public function modelToDataObject($rateModel, $quoteCurrencyCode)
    {
        if (!$this->_helperData->isEnabled()) {
            return parent::modelToDataObject($rateModel, $quoteCurrencyCode);
        }

        /**
         * @var \Magento\Directory\Model\Currency $currency
         */
        $currency = $this->_storeManager->getStore()->getBaseCurrency();
        $errorMessage = $rateModel->getErrorMessage();
        $sellerId = 'admin';
        $carrier = $rateModel->getCarrier();
        $method = $rateModel->getMethod();
        if ($carrier == 'seller_rates') {
            if (strpos($method, '|') !== false) {
                $tmp = explode('|', $method);
                $sellerId = isset($tmp[0]) ? (int)$tmp[0] : 'admin';
            }
        }

        $seller = $this->_sellerFactory->create();
        if ($sellerId && $sellerId != 'admin') {
            $seller = $seller->load($sellerId);
        }

        $title = $seller->getId()
            ? $seller->getName()
            : $this->_marketplaceHelperData->getStore()->getWebsite()->getName();

        return $this->shippingMethodDataFactory->create()
            ->setCarrierCode($carrier)
            ->setMethodCode($method)
            ->setCarrierTitle($title)
            ->setMethodTitle($rateModel->getMethodTitle())
            ->setAmount(
                $currency->convert($rateModel->getPrice(), $quoteCurrencyCode)
            )
            ->setBaseAmount($rateModel->getPrice())
            ->setAvailable(empty($errorMessage))
            ->setErrorMessage(empty($errorMessage) ? false : $errorMessage)
            ->setPriceExclTax(
                $currency->convert(
                    $this->getShippingPriceWithFlag($rateModel, false),
                    $quoteCurrencyCode
                )
            )
            ->setPriceInclTax(
                $currency->convert(
                    $this->getShippingPriceWithFlag($rateModel, true),
                    $quoteCurrencyCode
                )
            );
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address\Rate $rateModel
     * @param bool $flag
     * @return float
     */
    private function getShippingPriceWithFlag($rateModel, $flag)
    {
        return $this->taxHelper->getShippingPrice(
            $rateModel->getPrice(),
            $flag,
            $rateModel->getAddress(),
            $rateModel->getAddress()->getQuote()->getCustomerTaxClassId()
        );
    }
}
