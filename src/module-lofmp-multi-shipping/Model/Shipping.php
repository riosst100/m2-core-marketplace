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

namespace Lofmp\MultiShipping\Model;

use Magento\Framework\App\ObjectManager;
use Magento\Quote\Model\Quote\Address\RateRequestFactory;
use Magento\Shipping\Model\Rate\CarrierResultFactory;
use Magento\Shipping\Model\Rate\PackageResult;
use Magento\Shipping\Model\Rate\PackageResultFactory;
use Magento\Quote\Model\Quote\Address\RateResult\Error;
use Magento\Shipping\Model\Rate\Result;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Shipping extends \Magento\Shipping\Model\Shipping
{
    const SEPARATOR = ' ';
    const METHOD_SEPARATOR = ':';

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var mixed
     */
    protected $_register;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Lof\MarketPlace\Helper\Seller
     */
    protected $sellerHelper;

    /**
     * @var \Lof\MarketPlace\Model\SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $product;

    /**
     * @var \Lofmp\MultiShipping\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $helperData;

    /**
     * @var PackageResultFactory|mixed
     */
    protected $packageResultFactory;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Shipping\Model\Config $shippingConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Shipping\Model\CarrierFactory $carrierFactory
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Shipping\Model\Shipment\RequestFactory $shipmentRequestFactory
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Framework\Math\Division $mathDivision
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\Framework\ObjectManagerInterface $objectInterface
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerFactory
     * @param \Magento\Catalog\Model\ProductFactory $product
     * @param \Lof\MarketPlace\Helper\Data $helperData
     * @param \Lof\MarketPlace\Helper\Seller $sellerHelper
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Lofmp\MultiShipping\Helper\Data $multishippingData
     * @param RateRequestFactory|null $rateRequestFactory
     * @param PackageResultFactory|null $packageResultFactory
     * @param CarrierResultFactory|null $carrierResultFactory
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Shipping\Model\Config $shippingConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Shipping\Model\CarrierFactory $carrierFactory,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Shipping\Model\Shipment\RequestFactory $shipmentRequestFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Framework\Math\Division $mathDivision,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Framework\ObjectManagerInterface $objectInterface,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Magento\Catalog\Model\ProductFactory $product,
        \Lof\MarketPlace\Helper\Data $helperData,
        \Lof\MarketPlace\Helper\Seller $sellerHelper,
        \Magento\Framework\App\RequestInterface $request,
        \Lofmp\MultiShipping\Helper\Data $multishippingData,
        RateRequestFactory $rateRequestFactory = null,
        ?PackageResultFactory $packageResultFactory = null,
        ?CarrierResultFactory $carrierResultFactory = null
    ) {
        parent::__construct(
            $scopeConfig,
            $shippingConfig,
            $storeManager,
            $carrierFactory,
            $rateResultFactory,
            $shipmentRequestFactory,
            $regionFactory,
            $mathDivision,
            $stockRegistry,
            $rateRequestFactory,
            $packageResultFactory,
            $carrierResultFactory
        );

        $this->packageResultFactory = $packageResultFactory
            ?? ObjectManager::getInstance()->get(PackageResultFactory::class);
        $this->_request = $request;
        $this->sellerHelper = $sellerHelper;
        $this->helperData = $helperData;
        $this->sellerFactory = $sellerFactory;
        $this->product = $product;
        $this->_objectManager = $objectInterface;
        $this->_helper = $multishippingData;
        $this->_register = $this->_objectManager->get(\Magento\Framework\Registry::class);
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     * @return $this|Shipping
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function collectRates(\Magento\Quote\Model\Quote\Address\RateRequest $request)
    {
        if (!$this->_helper->isEnabled()) {
            return parent::collectRates($request);
        }
        $quotes = [];
        $sellerAddressDetails = [];
        $allItems = $request->getAllItems();
        foreach ($allItems as $item) {
            if ($item->getProductType() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE
                && $item->getProduct()->getShipmentType()
            ) {
                continue;
            }

            if ($item->getParentItem()) {
                if (!$item->getParentItem()->getProduct()->getShipmentType()) {
                    continue;
                }
            }

            $sellerId = $item->getLofSellerId() ? $item->getLofSellerId() : $this->getSellerIdByProduct($item);
            if ($sellerId) {
                $seller = $this->sellerFactory->create()->load($sellerId);
                if ($seller && $seller->getId()) {
                    $this->_register->register('current_order_seller', $seller);
                }
                if (isset($sellerAddressDetails[$sellerId]) && count($sellerAddressDetails[$sellerId]) > 0
                ) {
                    $sellerAddress = $sellerAddressDetails[$sellerId];
                } else {
                    $sellerAddress = $this->_helper->getSellerAddress($sellerId);
                }
                if ($this->_helper->validateAddress($sellerAddress)) {
                    if (!isset($quotes[$sellerId])) {
                        $quotes[$sellerId] = [];
                    }
                    $quotes[$sellerId][] = $item;
                    if (!isset($sellerAddressDetails[$sellerId])) {
                        $sellerAddressDetails[$sellerId] = $sellerAddress;
                    }
                }
                if ($this->_register->registry('current_order_seller') != null) {
                    $this->_register->unregister('current_order_seller');
                }
            } else {
                $quotes['admin'][] = $item;
            }
        }

        if ($this->_register->registry('current_order_seller') != null) {
            $this->_register->unregister('current_order_seller');
        }
        $origRequest = clone $request;
        $last_count = 0;
        $prod_model = $this->product->create();
        if ($this->_objectManager->get(\Magento\Checkout\Model\Session::class)->getInvalidItem()) {
            $this->_objectManager->get(\Magento\Checkout\Model\Session::class)->unsInvalidItem();
        }

        $storeId = $request->getStoreId();
        foreach ($quotes as $sellerId => $items) {
            $request = clone $origRequest;
            $request->setSellerId($sellerId);
            $request->setSellerItems($items);
            $request->setAllItems($items);
            $request->setPackageWeight($this->getItemWeight($request, $items));
            $request->setPackageQty($this->getItemQty($request, $items));
            $request->setPackageValue($this->getItemSubtotal($request, $items));
            $request->setBaseSubtotalInclTax($this->getItemSubtotal($request, $items));

            //set address for seller
            if ($sellerId != 'admin') {
                $seller = $this->sellerFactory->create()->load($sellerId);
                if ($seller && $seller->getId()) {
                    $this->_register->register('current_order_seller', $seller);
                }
                $sellerAddress = $sellerAddressDetails[$sellerId];
                if (isset($sellerAddress['country_id'])) {
                    $request->setOrigCountry($sellerAddress['country_id']);
                }
                if (isset($sellerAddress['region'])) {
                    $request->setOrigRegionCode($sellerAddress['region']);
                }
                if (isset($sellerAddress['region_id'])) {
                    $origRegionCode = $sellerAddress['region_id'];
                    if (is_numeric($origRegionCode)) {
                        $origRegionCode = $this->_objectManager->get(\Magento\Directory\Model\Region::class)
                            ->load($origRegionCode)->getCode();
                    }
                    $request->setOrigRegionCode($origRegionCode);
                }
                if (isset($sellerAddress['postcode'])) {
                    $request->setOrigPostcode($sellerAddress['postcode']);
                }
                if (isset($sellerAddress['city'])) {
                    $request->setOrigCity($sellerAddress['city']);
                }
            }
            $storeId = $request->getStoreId();
            if (!$request->getOrig()) {
                $request
                    ->setCountryId(
                        $this->helperData->getStoreConfig(
                            \Magento\Shipping\Model\Config::XML_PATH_ORIGIN_COUNTRY_ID,
                            $storeId
                        )
                    )
                    ->setRegionId(
                        $this->helperData->getStoreConfig(
                            \Magento\Shipping\Model\Config::XML_PATH_ORIGIN_REGION_ID,
                            $storeId
                        )
                    )
                    ->setCity(
                        $this->helperData->getStoreConfig(
                            \Magento\Shipping\Model\Config::XML_PATH_ORIGIN_CITY,
                            $storeId
                        )
                    )
                    ->setPostcode($this->_objectManager->create(\Lof\MarketPlace\Helper\Data::class)
                        ->getStoreConfig(\Magento\Shipping\Model\Config::XML_PATH_ORIGIN_POSTCODE, $storeId));
            }
            $limitCarrier = $request->getLimitCarrier();
            if (!is_array($limitCarrier)) {
                if ($limitCarrier == 'seller' || $limitCarrier == 'seller_rates') {
                    $limitCarrier = '';
                }
            }

            if (!$limitCarrier) {
                $carriers = $this->helperData->getStoreConfig('carriers', $storeId);
                foreach ($carriers as $carrierCode => $carrierConfig) {
                    $this->collectCarrierRates($carrierCode, $request);
                }
            } else {
                if (!is_array($limitCarrier)) {
                    $limitCarrier = [$limitCarrier];
                }
                foreach ($limitCarrier as $carrierCode) {
                    $carrierConfig = $this->helperData->getStoreConfig('carriers/' . $carrierCode, $storeId);
                    if (!$carrierConfig) {
                        continue;
                    }
                    $this->collectCarrierRates($carrierCode, $request);
                }
            }
            if ($this->_register->registry('current_order_seller') != null) {
                $this->_register->unregister('current_order_seller');
            }
            $total_count = count($this->getResult()->getAllRates());
            $current_count = $total_count - $last_count;
            $last_count = $total_count;
            if ($current_count < 1) {
                $prod_name = $this->_objectManager->get(\Magento\Checkout\Model\Session::class)->getInvalidItem();
                foreach ($items as $item) {
                    $prod_name[] = $prod_model->load($item->getProductId())->getName();
                }
                $this->_objectManager->get(\Magento\Checkout\Model\Session::class)->setInvalidItem($prod_name);
            }
        }
        $shippingRates = $this->getResult()->getAllRates();
        $newRates = [];
        $newSellerRates = [];
        $sellerRates = [];
        $ratesBySeller = $this->ratesBySeller($shippingRates, $quotes);
        foreach ($ratesBySeller as $sellerId => $rates) {
            if (!count($newSellerRates)) {
                foreach ($rates as $rate) {
                    $newRateCode = $rate->getCarrier() . '_' . $rate->getMethod();
                    $newSellerRates[$newRateCode]['price'] = $rate->getPrice();
                    $newSellerRates[$newRateCode]['mp_info'] = [
                        $rate->getSellerId() => [
                            'code' => $newRateCode,
                            'carrier' => $rate->getCarrier(),
                            'carrier_title' => $rate->getCarrierTitle(),
                            'method' => $rate->getMethod(),
                            'method_title' => $rate->getMethodTitle(),
                            'price' => $rate->getPrice(),
                            'seller_id' => $rate->getSellerId(),
                        ]
                    ];
                }
            } else {
                $tmpRates = [];
                foreach ($rates as $rate) {
                    foreach ($newSellerRates as $code => $newShipping) {
                        $mpInfo = $newShipping['mp_info'];
                        $rateCode = $code . self::METHOD_SEPARATOR . $rate->getCarrier() . '_' . $rate->getMethod();
                        $tmpRates[$rateCode]['price'] = $newShipping['price'] + $rate->getPrice();
                        $mpInfo[$rate->getSellerId()] = [
                            'code' => $rate->getCarrier() . '_' . $rate->getMethod(),
                            'carrier' => $rate->getCarrier(),
                            'carrier_title' => $rate->getCarrierTitle(),
                            'method' => $rate->getMethod(),
                            'method_title' => $rate->getMethodTitle(),
                            'price' => $rate->getPrice(),
                            'seller_id' => $rate->getSellerId(),
                        ];
                        $tmpRates[$rateCode]['mp_info'] = $mpInfo;
                    }
                }
                $newSellerRates = $tmpRates;
            }
            foreach ($rates as $rate) {
                $rateKey = $sellerId . '|' . $rate->getCarrier() . '_' . $rate->getMethod();
                $newRates[$rateKey]['price'] = $rate->getPrice();
                $newRates[$rateKey]['method_title'] = $rate->getMethodTitle();
                $newRates[$rateKey]['mp_info'] = [
                    $rate->getSellerId() => [
                        'code' => $rateKey,
                        'carrier' => $rate->getCarrier(),
                        'carrier_title' => $rate->getCarrierTitle(),
                        'method' => $rate->getMethod(),
                        'method_title' => $rate->getMethodTitle(),
                        'price' => $rate->getPrice(),
                        'seller_id' => $rate->getSellerId(),
                    ]
                ];
            }
            $sellerRates = $newRates;
        }

        $carrier_title = $this->helperData->getStoreConfig('lofmp_multishipping/general/carrier_title', $storeId);
        $method_title = $this->helperData->getStoreConfig('lofmp_multishipping/general/method_title', $storeId);
        if ($newSellerRates) {
            foreach ($newSellerRates as $code => $shipping) {
                $method = $this->_objectManager->create(\Magento\Quote\Model\Quote\Address\RateResult\Method::class);
                $method->setCarrier('seller_rates');
                $method->setCarrierTitle($carrier_title);
                $method->setMethod($code);
                $method->setMethodTitle($method_title);
                $method->setPrice($shipping['price']);
                $method->setCost($shipping['price']);
                $method->setMpInfo(json_encode($shipping['mp_info']));
                $this->getResult()->append($method);
            }
        }
        if ($sellerRates) {
            foreach ($sellerRates as $code => $shipping) {
                if (!$shipping['method_title'] && !$shipping['price']) {
                    continue;
                }
                $method = $this->_objectManager->create(\Magento\Quote\Model\Quote\Address\RateResult\Method::class);
                $method->setCarrier('seller_rates');
                $method->setCarrierTitle($carrier_title);
                $method->setMethod($code);
                $method->setMethodTitle($shipping['method_title']);
                $method->setPrice($shipping['price']);
                $method->setCost($shipping['price']);
                $method->setMpInfo(json_encode($shipping['mp_info']));
                $this->getResult()->append($method);
            }
        }
        return $this;
    }

    /**
     * @param array $shippingRates
     * @param array $quotes
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function ratesBySeller($shippingRates, $quotes)
    {
        $rates = [];
        foreach ($shippingRates as $rate) {
            if (!$rate->getSellerId()) {
                $rate->setSellerId('admin');
            }
            if (!isset($rates[$rate->getSellerId()])) {
                $rates[$rate->getSellerId()] = [];
            }
            if (!$this->isAllowedRate($rate)) {
                continue;
            }
            $rates[$rate->getSellerId()][] = $rate;
        }
        ksort($rates);

        return $rates;
    }

    /**
     * @param string $carrierCode
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     * @return $this|Shipping
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function collectCarrierRates($carrierCode, $request)
    {
        if (!$this->_helper->isEnabled()) {
            return parent::collectCarrierRates($carrierCode, $request);
        }
        try {
            $carrier = $this->prepareCarrier($carrierCode, $request);
        } catch (\RuntimeException $exception) {
            return $this;
        }

        $result = null;
        if ($carrier->getConfigData('shipment_requesttype')) {
            $packages = $this->composePackagesForCarrier($carrier, $request);
            if (!empty($packages)) {
                //Multiple shipments
                /** @var PackageResult $result */
                $result = $this->packageResultFactory->create();
                foreach ($packages as $weight => $packageCount) {
                    $request->setPackageWeight($weight);
                    $packageResult = $carrier->collectRates($request);
                    if (!$packageResult) {
                        return $this;
                    } else {
                        $result->appendPackageResult($packageResult, $packageCount);
                    }
                }
            }
        }
        if (!$result) {
            //One shipment for all items.
            $result = $carrier->collectRates($request);
        }
        if ($result && $request->getSellerId() && $result->getAllRates()) {
            foreach ($result->getAllRates() as $rate) {
                $rate->setSellerId($request->getSellerId());
            }
        }
        if (!$result) {
            return $this;
        } elseif ($result instanceof Result) {
            $this->getResult()->appendResult($result, $carrier->getConfigData('showmethod') != 0);
        } else {
            $this->getResult()->append($result);
        }

        return $this;
    }

    /**
     * @param $request
     * @param $items
     * @return int
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getItemQty($request, $items)
    {
        $qty = 0;
        foreach ($items as $item) {
            $qty += $item->getQty();
        }
        return $qty;
    }

    /**
     * @param $request
     * @param $items
     * @return float|int
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getItemWeight($request, $items)
    {
        $qty = 0;
        foreach ($items as $item) {
            if ($item->getData('product_type') != 'configurable' && $item->getData('product_type') != 'bundle') {
                $qty += $item->getQty() * $item->getWeight();
            }
        }
        return $qty;
    }

    /**
     * @param $request
     * @param $items
     * @return int
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getItemSubtotal($request, $items)
    {
        $rowTotal = 0;
        foreach ($items as $item) {
            $rowTotal += $item->getBaseRowTotalInclTax();
        }
        return $rowTotal;
    }

    /**
     * @param string $carrierCode
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     * @return \Magento\Shipping\Model\Carrier\AbstractCarrier
     */
    private function prepareCarrier(
        string $carrierCode,
        \Magento\Quote\Model\Quote\Address\RateRequest $request
    ): \Magento\Shipping\Model\Carrier\AbstractCarrier {
        $carrier = $this->isShippingCarrierAvailable($carrierCode)
            ? $this->_carrierFactory->create($carrierCode, $request->getStoreId())
            : null;
        if (!$carrier) {
            throw new \RuntimeException('Failed to initialize carrier');
        }
        $carrier->setActiveFlag($this->_availabilityConfigField);
        $result = $carrier->checkAvailableShipCountries($request);
        if (false !== $result && !$result instanceof Error) {
            $result = $carrier->processAdditionalValidation($request);
        }
        if (!$result) {
            /*
             * Result will be false if the admin set not to show the shipping module
             * if the delivery country is not within specific countries
             */
            throw new \RuntimeException('Cannot collect rates for given request');
        } elseif ($result instanceof Error) {
            $this->getResult()->append($result);
            throw new \RuntimeException('Error occurred while preparing a carrier');
        }

        return $carrier;
    }

    /**
     * Checks availability of carrier.
     *
     * @param string $carrierCode
     * @return bool
     */
    private function isShippingCarrierAvailable(string $carrierCode): bool
    {
        return $this->_scopeConfig->isSetFlag(
            'carriers/' . $carrierCode . '/' . $this->_availabilityConfigField,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param $item
     * @return mixed
     */
    public function getSellerIdByProduct($item)
    {
        $productId = $item->getProduct()->getId();
        if ($item->getParentItem()) {
            $productId = $item->getParentItem()->getProduct()->getId();
        }

        return $this->sellerHelper->getSellerIdByProduct($productId);
    }

    /**
     * @param $rate
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isAllowedRate($rate)
    {
        $useAdminShipping = $this->_helper->isUseAdminShipping();
        if ($useAdminShipping) {
            return true;
        }

        if ($rate->getSellerId() != 'admin' && strpos($rate->getCarrier(), 'lofmp') === false) {
            return false;
        }

        return true;
    }
}
