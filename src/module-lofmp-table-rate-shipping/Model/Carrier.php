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
 * @package    Lofmp_TableRateShipping
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\TableRateShipping\Model;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Session\SessionManager;
use Magento\Quote\Model\Quote\Item\OptionFactory;
use \Magento\Framework\Unserialize\Unserialize;
use Magento\Shipping\Model\Rate\Result;
use Magento\Checkout\Model\Session;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class Carrier extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{
    const CODE = 'lofmptablerateshipping';
    const SEPARATOR = '~';

    /**
     * Code of the carrier.
     *
     * @var string
     */
    protected $_code = self::CODE;

    /**
     * @var
     */
    protected $_request;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_productFactory;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var array
     */
    protected $_errors = [];

    /**
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $_rateMethodFactory;

    /**
     * @var null
     */
    protected $_rawRequest = null;

    /**
     * @var \Lofmp\TableRateShipping\Model\ShippingmethodFactory
     */
    protected $_mpshippingMethod;

    /**
     * @var SessionManager
     */
    protected $_coreSession;

    /**
     * @var OptionFactory
     */
    protected $_itemOptionModel;

    /**
     * @var \Lof\MarketPlace\Model\SellerProduct
     */
    protected $_mpProductFactory;

    /**
     * @var ShippingFactory
     */
    protected $_mpShippingModel;

    /**
     * @var Unserialize
     */
    protected $_unserialize;

    /**
     * @var \Magento\Framework\Data\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Lof\MarketPlace\Model\SellerProductFactory
     */
    protected $sellerProduct;

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var Shipping
     */
    protected $shippingModel;

    /**
     * @var \Lofmp\TableRateShipping\Helper\Data
     */
    protected $helperData;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param ProductFactory $productFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param ShippingmethodFactory $shippingmethodFactory
     * @param SessionManager $coreSession
     * @param OptionFactory $itemOptionModel
     * @param \Lof\MarketPlace\Model\SellerProduct $mpproductModel
     * @param \Lof\MarketPlace\Model\SellerProductFactory $sellerProduct
     * @param ShippingFactory $mpshippingModel
     * @param Unserialize $unserialize
     * @param Session $checkoutSession
     * @param \Magento\Framework\Data\CollectionFactory $collectionFactory
     * @param \Lofmp\TableRateShipping\Helper\Data $helperData
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        ProductFactory $productFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        ShippingmethodFactory $shippingmethodFactory,
        SessionManager $coreSession,
        OptionFactory $itemOptionModel,
        \Lof\MarketPlace\Model\SellerProduct $mpproductModel,
        \Lof\MarketPlace\Model\SellerProductFactory $sellerProduct,
        ShippingFactory $mpshippingModel,
        Unserialize $unserialize,
        Session $checkoutSession,
        \Magento\Framework\Data\CollectionFactory $collectionFactory,
        \Lofmp\TableRateShipping\Helper\Data $helperData,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->sellerProduct = $sellerProduct;
        $this->_productFactory = $productFactory;
        $this->_objectManager = $objectManager;
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->_mpshippingMethod = $shippingmethodFactory;
        $this->_coreSession = $coreSession;
        $this->_itemOptionModel = $itemOptionModel;
        $this->_mpProductFactory = $mpproductModel;
        $this->_mpShippingModel = $mpshippingModel;
        $this->_unserialize = $unserialize;
        $this->checkoutSession = $checkoutSession;
        $this->helperData = $helperData;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * @param $min_ship_price
     * @return bool
     */
    public function allowFreeShipZeroPrice($min_ship_price)
    {
        $allow_zero_price = $this->getConfigData('allow_free_ship_zero_price');
        return (($min_ship_price > 0) || ($min_ship_price == 0 && $allow_zero_price)) ? true : false;
    }

    /**
     * Collect and get rates.
     *
     * @param RateRequest $request
     *
     * @return \Magento\Quote\Model\Quote\Address\RateResult\Error|bool|Result
     */
    public function collectRates(RateRequest $request)
    {
        $this->setRequest($request);
        return $this->getShippingPricedetail($this->_rawRequest);
    }

    /**
     * @param \Magento\Framework\DataObject|null $request
     * @return $this
     * @api
     */
    public function setRawRequest($request)
    {
        $this->_rawRequest = $request;
        return $this;
    }

    /**
     * Prepare and set request to this instance.
     *
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     *
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function setRequest(\Magento\Quote\Model\Quote\Address\RateRequest $request)
    {
        $this->_request = $request;
        $requestData = new \Magento\Framework\DataObject();
        $shippingdetail = [];
        foreach ($request->getAllItems() as $item) {
            $proid = $item->getProductId();
            if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                continue;
            }

            $sellerProduct = $this->sellerProduct->create()->getCollection()->addFieldToFilter('product_id', $proid);
            $partner = $item->getLofSellerId() ?? 0;
            
            if (!$partner) {
                if ($sellerProduct->count()) {
                    $partner = $sellerProduct->getFirstItem()->getSellerId();
                } else {
                    $partner = 0;
                }
            }
            
            $weight = $this->calculateWeightForProduct($item);
            if (empty($shippingdetail)) {
                array_push(
                    $shippingdetail,
                    [
                        'seller_id' => $partner,
                        'items_weight' => $weight,
                        'product_name' => $item->getName(),
                        'item_id' => $item->getId(),
                        'subtotal' => $item->getRowTotal(),
                    ]
                );
            } else {
                $shipinfoflag = true;
                $index = 0;
                foreach ($shippingdetail as $itemship) {
                    if ($itemship['seller_id'] == $partner) {
                        $itemship['items_weight'] = $itemship['items_weight'] + $weight;
                        $itemship['product_name'] = $itemship['product_name'] . ',' . $item->getName();
                        $itemship['item_id'] = $itemship['item_id'] . ',' . $item->getId();
                        $itemship['subtotal'] = $itemship['subtotal'] + $item->getRowTotal();
                        $shippingdetail[$index] = $itemship;
                        $shipinfoflag = false;
                    }
                    ++$index;
                }
                if ($shipinfoflag == true) {
                    array_push(
                        $shippingdetail,
                        [
                            'seller_id' => $partner,
                            'items_weight' => $weight,
                            'product_name' => $item->getName(),
                            'item_id' => $item->getId(),
                            'subtotal' => $item->getRowTotal(),
                        ]
                    );
                }
            }
        }

        if ($request->getShippingDetails()) {
            $shippingdetail = $request->getShippingDetails();
        }
        $requestData->setShippingDetails($shippingdetail);
        $requestData->setDestRegionCode($request->getDestRegionCode());
        $requestData->setDestRegionId($request->getDestRegionId());
        $requestData->setDestCountryId($request->getDestCountryId());

        if ($request->getDestPostcode()) {
            $requestData->setDestPostal($request->getDestPostcode());
            //$requestData->setDestPostal(str_replace('-', '', $request->getDestPostcode()));
        }

        $this->setRawRequest($requestData);

        return $this;
    }

    /**
     * @param \Magento\Framework\DataObject $request
     * @return \Magento\Shipping\Model\Rate\ResultFactory|void|null
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @phpcs:disable Generic.Metrics.NestingLevel.TooHigh
     */
    public function getShippingPricedetail(\Magento\Framework\DataObject $request)
    {
        $requestData = $request;
        $shippinginfo = [];
        $msg = '';
        $handling = 0;
        $totalPriceArr = [];
        $totalCostArr = [];
        $flag = false;
        $check = false;
        $returnError = false;
        $quote = $this->checkoutSession->getQuote();
        $quoteData = $quote->getData();
        // phpcs:disable Generic.Metrics.NestingLevel.TooHigh
        if (isset($quoteData['subtotal'])) {
            foreach ($requestData->getShippingDetails() as $shipdetail) {
                $thisMsg = false;
                $sellerId = isset($shipdetail['seller_id']) ? $shipdetail['seller_id'] : 0;
                $subtotal = $shipdetail['subtotal'];
                $foundShippingRates = $this->getShippingPriceRates($shipdetail, $requestData, $subtotal);
                if (!$foundShippingRates) {
                    continue;
                }

                foreach ($foundShippingRates as $shipping) {
                    //Get Shipping Price
                    $price = floatval($shipping->getPrice());
                    $free_ship_price = $shipping->getFreeShipping();
                    $is_allow_free_ship = $this->allowFreeShipZeroPrice($free_ship_price);
                    if ($is_allow_free_ship && $free_ship_price && $subtotal >= (float)$free_ship_price) {
                        $price = 0;
                    }

                    if (!$shipping->getData()) {
                        continue;
                    }

                    $returnRateArr = $this->getPriceArrForRate($shipping, $price);
                    $priceArr = $returnRateArr['price'];
                    $costArr = $returnRateArr['cost'];

                    if (!empty($totalPriceArr)) {
                        foreach ($priceArr as $methodId => $_price) {
                            // Calculate price
                            if (!array_key_exists($methodId, $totalPriceArr)) {
                                $check = true;
                                // phpcs:disable Magento2.Performance.ForeachArrayMerge.ForeachArrayMerge
                                $totalPriceArr = array_merge($totalPriceArr, $priceArr);
                                $priceArr = $totalPriceArr;
                            } else {
                                $thisMsg = true;
                                unset($priceArr[$methodId]);
                            }

                            // Calculate cost
                            if (!array_key_exists($methodId, $totalCostArr)) {
                                $totalCostArr = array_merge($totalCostArr, $costArr);
                                $costArr = $totalCostArr;
                            } else {
                                unset($costArr[$methodId]);
                            }
                            $flag = $check == true ? false : true;
                        }
                    } else {
                        $totalPriceArr = $priceArr;
                        $totalCostArr = $costArr;
                    }
                    if (empty($priceArr)) {
                        $totalPriceArr = [];
                        $totalCostArr = [];
                        $flag = true;
                    }
                    if ($flag) {
                        if ($thisMsg) {
                            $msg = $this->getErrorMsg($msg, $shipdetail);
                        }
                        $returnError = true;
                        $debugData['result'] = ['error' => 1, 'errormsg' => $msg];
                    }
                    $submethod = $this->getSubMethodsForRate($priceArr, $costArr);
                    $handling += $price;
                    array_push(
                        $shippinginfo,
                        [
                            'seller_id' => $shipdetail['seller_id'],
                            'methodcode' => $this->_code,
                            'shipping_ammount' => $price,
                            'product_name' => $shipdetail['product_name'],
                            'submethod' => $submethod,
                            'item_ids' => $shipdetail['item_id'],
                        ]
                    );
                }
                if ($returnError) {
                    return $this->_parseXmlResponse($debugData, $sellerId);
                }
                $totalpric = ['totalprice' => $totalPriceArr, 'costarr' => $totalCostArr];
                $result = ['handlingfee' => $totalpric, 'shippinginfo' => $shippinginfo, 'error' => 0];
                $shippingAll = $this->_coreSession->getShippingInfo();
                $shippingAll[$this->_code] = $result['shippinginfo'];
                $this->_coreSession->setShippingInfo($shippingAll);
                return $this->_parseXmlResponse($totalpric, $sellerId);
            }
        }
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        return ['lofmptablerateshipping' => $this->getConfigData('name')];
    }

    /**
     * @param $shipMethodId
     * @return string
     */
    public function getShipMethodNameById($shipMethodId)
    {
        $shippingMethodModel = $this->_mpshippingMethod->create()->load($shipMethodId);
        if ($shippingMethodModel && $methodName = $shippingMethodModel->getMethodName()) {
            return $methodName;
        }

        return '';
    }

    /**
     * _parseXmlResponse
     * @param mixed $response
     * @param int|null $sellerId
     * @return \Magento\Shipping\Model\Rate\ResultFactory|null
     */
    protected function _parseXmlResponse($response, $sellerId = 0)
    {
        $result = $this->_rateResultFactory->create();
        if (array_key_exists('result', $response) && $response['result']['error'] !== '') {
            $this->_errors[$this->_code] = $response['result']['errormsg'];
            $errors = explode('<br>', $response['result']['errormsg']);
            $error = $this->_rateErrorFactory->create();
            $error->setCarrier($this->_code);
            $error->setCarrierTitle($this->getConfigData('title'));
            foreach ($errors as $value) {
                $errorMsg[] = $value;
            }
            $error->setErrorMessage($errorMsg);
            return $error;
        } else {
            $totalPriceArr = $response['totalprice'];
            $costArr = $response['costarr'];
            foreach ($totalPriceArr as $key => $price) {
                if (isset($price['method_id']) && $price['method_id']) {
                    $rate = $this->_rateMethodFactory->create();
                    $rate->setCarrier($this->_code);
                    if ($sellerId > 0) {
                        $rate->setSellerId($sellerId);
                    }
                    $rate->setCarrierTitle($this->getConfigData('title'));
                    $rate->setMethod($this->_code . $price['method_id']);
                    $rate->setMethodTitle($price['method_title']);
                    $rate->setCost($costArr[$key]['cost']);
                    $rate->setPrice($price['price']);
                    $result->append($rate);
                }
            }
        }

        return $result;
    }

    /**
     * @param $itemOption
     * @param $item
     * @param $productId
     * @return int
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getSellerIdOfItemId($itemOption, $item, $productId)
    {
        $optionValue = '';
        if (!empty($itemOption)) {
            foreach ($itemOption as $value) {
                $optionValue = $value->getValue();
            }
        }

        $mpassignproductId = 0;
        if ($optionValue != '') {
            $temp = json_decode($optionValue, true);
            $mpassignproductId = isset($temp['product']) ? $temp['product'] : 0;
        }

        if (!$mpassignproductId) {
            foreach ($item->getOptions() as $option) {
                if (isset($option['value']) && is_array($option['value'])) {
                    $temp = json_decode($optionValue, true);
                }
                if (isset($temp['product'])) {
                    $mpassignproductId = $temp['product'];
                }
            }
        }

        if ($mpassignproductId) {
            $mpassignModel = $this->sellerProduct->create()->load($mpassignproductId);
            $partner = $mpassignModel->getSellerId();
        } else {
            $productSeller = $this->sellerProduct->create()->getCollection()
                ->addFieldToFilter('product_id', ['eq' => $productId])
                ->getFirstItem();
            $partner = $productSeller ? $productSeller->getSellerId() : 0;
        }
        return $partner;
    }

    /**
     * calculate Weight For Product
     */
    public function calculateWeightForProduct($item)
    {
        $childWeight = 0;
        if ($item->getHasChildren()) {
            $_product = $this->_productFactory
                ->create()
                ->load($item->getProductId());
            if ($_product->getTypeId() == 'bundle') {
                foreach ($item->getChildren() as $child) {
                    $productWeight = $this->_productFactory
                        ->create()
                        ->load($child->getProductId())
                        ->getWeight();
                    $childWeight += $productWeight * $child->getQty();
                }
                $weight = $childWeight * $item->getQty();
            } elseif ($_product->getTypeId() == 'configurable') {
                foreach ($item->getChildren() as $child) {
                    $productWeight = $this->_productFactory->create()->load($child->getProductId())->getWeight();
                    $weight = $productWeight * $item->getQty();
                }
            }
        } else {
            $productWeight = $this->_productFactory->create()->load($item->getProductId())->getWeight();
            $weight = $productWeight * $item->getQty();
        }
        return $weight;
    }

    /**
     * @param $countryId
     * @param $regionId
     * @param int $sellerId
     * @param $postalCode
     * @param $weight
     * @param int $subtotal
     * @return array|\Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    public function getShippingcollectionAccordingToDetails(
        $countryId,
        $regionId,
        $sellerId,
        $postalCode,
        $weight,
        $subtotal = 0
    ) {
        if (!$this->helperData->isSellerEnabled($sellerId)) {
            return [];
        }
        $filterPartner = ['eq' => (int)$sellerId];
        if ($this->getConfigData('allowadmin')) {
            $filterPartner = ['in' => [(int)$sellerId, 0]];
        }

        $shipping = $this->_mpShippingModel->create()
            ->getCollection()
            ->addFieldToFilter('dest_country_id', ['eq' => $countryId])
            ->addFieldToFilter('dest_region_id', [
                ['eq' => '*'],
                ['eq' => ''],
                ['eq' => @strtoupper($regionId)]
            ])
            ->addFieldToFilter('dest_zip', [
                ['lteq' => $postalCode],
                ['eq' => '*'],
                ['eq' => @strtoupper($postalCode)]
            ])
            ->addFieldToFilter('dest_zip_to', [
                ['gteq' => $postalCode],
                ['eq' => @strtoupper($postalCode)],
                ['eq' => '*'],
                ['eq' => '']
            ])
            ->addFieldToFilter('weight_from', ['lteq' => $weight])
            ->addFieldToFilter('weight_to', ['gteq' => $weight])
            ->addFieldToFilter('partner_id', $filterPartner)
            ->addFieldToFilter(
                'cart_total',
                [
                    ['lteq' => (float)$subtotal],
                    ['null' => true],
                    ['eq' => '']
                ]
            );

        return $shipping;
    }

    /**
     * @param $shipdetail
     * @param $requestData
     * @param float|int $subtotal
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    public function getShippingPriceRates($shipdetail, $requestData, $subtotal)
    {
        $shipping = $this->getShippingcollectionAccordingToDetails(
            $requestData->getDestCountryId(),
            $requestData->getDestRegionCode(),
            isset($shipdetail['seller_id']) && $shipdetail['seller_id'] ? (int)$shipdetail['seller_id'] : 0,
            $requestData->getDestPostal(),
            $shipdetail['items_weight'],
            $subtotal
        );
        return $shipping;
    }

    /**
     * @param $shipping
     * @param float|int $shipping_price
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPriceArrForRate($shipping, $shipping_price = 0)
    {
        $priceArr = [];
        $costArr = [];
        $shippingId = $shipping->getId();
        $shipMethodId = $shipping->getShippingMethodId();
        if ($shipMethodId) {
            $shipMethodName = $this->getShipMethodNameById($shipMethodId);
        } else {
            $shipMethodName = $this->getConfigData('title');
        }
        $priceArr[$shippingId] = [
            'method_id' => $shippingId,
            'price' => floatval($shipping_price),
            'method_title' => $shipMethodName
        ];
        $costArr[$shippingId] = [
            'method_id' => $shippingId,
            'cost' => $shipping->getCost() ? floatval($shipping->getCost()) : $shipping_price,
            'method_title' => $shipMethodName
        ];
        return [
            'price' => $priceArr,
            'cost' => $costArr
        ];
    }

    /**
     * @param $priceArr
     * @param $costArr
     * @return array
     */
    public function getSubMethodsForRate($priceArr, $costArr)
    {
        $submethod = [];
        if (!empty($priceArr)) {
            foreach ($priceArr as $index => $price) {
                $submethod[$index] = [
                    'method' => $index . ' (' . $this->getConfigData('title') . ')',
                    'cost' => isset($costArr[$index]) ? $costArr[$index] : $price,
                    'base_amount' => $price,
                    'error' => 0,
                ];
            }
        }
        return $submethod;
    }

    /**
     * @param $msg
     * @param $shipdetail
     * @return \Magento\Framework\Phrase|string
     */
    public function getErrorMsg($msg, $shipdetail)
    {
        $thisMsg = __(
            'Seller Of Product %1 do not provide shipping service to your location.',
            $shipdetail['product_name']
        );
        if ($msg == '') {
            $msg = $thisMsg;
        } else {
            $msg = $msg . "<br>" . $thisMsg;
        }
        return $msg;
    }
}
