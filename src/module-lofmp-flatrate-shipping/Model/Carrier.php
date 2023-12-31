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
 * @package    Lofmp_FlatRateShipping
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\FlatRateShipping\Model;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Lofmp\FlatRateShipping\Model\FlatRateShippingFactory;

class Carrier extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{
    /**
     * Code of the carrier.
     *
     * @var string
     */
    const CODE = 'lofmpflatrateshipping';

    /**
     * Code of the carrier.
     *
     * @var string
     */
    protected $_code = self::CODE;

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
     * @var Carrier\Flatrate\ItemPriceCalculator
     */
    private $itemPriceCalculator;

    /**
     * @var Shipping
     */
    protected $shipping;

    /**
     * @var \Lofmp\FlatRateShipping\Helper\Data
     */
    protected $helperData;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param Carrier\Flatrate\ItemPriceCalculator $itemPriceCalculator
     * @param Shipping $shipping
     * @param \Lofmp\FlatRateShipping\Helper\Data $helperData
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Lofmp\FlatRateShipping\Model\Carrier\Flatrate\ItemPriceCalculator $itemPriceCalculator,
        \Lofmp\FlatRateShipping\Model\Shipping $shipping,
        \Lofmp\FlatRateShipping\Helper\Data $helperData,
        array $data = []
    ) {
        $this->shipping = $shipping;
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->itemPriceCalculator = $itemPriceCalculator;
        $this->helperData = $helperData;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * @param RateRequest $request
     * @return \Magento\Shipping\Model\Rate\ResultFactory
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @phpcs:disable Generic.Metrics.NestingLevel.TooHigh
     */
    public function collectRates(RateRequest $request)
    {
        $quotes = [];
        $sellerRates = [];
        /** @var \Magento\Shipping\Model\Rate\ResultFactory $result */
        $result = $this->_rateResultFactory->create();

        if ($request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
                $product = $item->getProduct()->load($item->getProductId());
                if ($item->getParentItem() && $product->isVirtual()) {
                    continue;
                }

                if ($product->getSellerId()) {
                    if ($item->getSellerId()) {
                        $sellerId = $item->getSellerId();
                    } else {
                        $sellerId = $product->getSellerId();
                    }

                    if (!$this->helperData->isSellerEnabled($sellerId)) {
                        continue;
                    }

                    /*Get all flatrate shipping info*/
                    if (!isset($sellerRates[$sellerId])) {
                        $sellerRates[$sellerId] = [];
                        $filterPartner = ['eq' => (int)$sellerId];
                        if ($this->getConfigData('allowadmin')) {
                            $filterPartner = ['in' => [(int)$sellerId, 0]];
                        }
                        $rates = $this->shipping->getCollection()
                            ->addFieldToFilter('partner_id', $filterPartner)
                            ->addFieldToFilter('status', 1)
                            ->setOrder('sort_order', 'ASC');

                        if ($rates->getData()) {
                            foreach ($rates->getData() as $rate) {
                                $identifier = $rate['lofmpshipping_id'];
                                $sellerRates[$sellerId][$identifier] = [
                                    'title' => $rate['title'],
                                    'price' => $rate['price'],
                                    'type' => $rate['type'],
                                    'free_shipping' => $rate['free_shipping'],
                                    'sort_order' => $rate['sort_order'],
                                ];
                            }
                        }
                    }

                    /*Get item by seller id*/
                    if (!isset($quotes[$sellerId])) {
                        $quotes[$sellerId] = [];
                    }
                    $quotes[$sellerId][] = $item;
                } else {
                    $quotes['no_seller'][] = $item;
                }
            }

            foreach ($sellerRates as $sellerId => $rates) {
                $total = 0;
                foreach ($quotes[$sellerId] as $item) {
                    $product = $item->getProduct()->load($item->getProductId());
                    if ($item->getParentItem() || $product->isVirtual()) {
                        continue;
                    }
                    $total += $item->getRowTotal();
                }

                foreach ($rates as $rateId => $rate) {
                    /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
                    $method = $this->_rateMethodFactory->create();
                    $method->setCarrier($this->_code);
                    $method->setSellerId($sellerId);
                    $method->setCarrierTitle($this->getConfigData('title'));
                    $method->setMethod($this->_code . $rateId);
                    $method->setMethodTitle($rate['title'] ?: $this->getConfigData('name'));

                    if ($rate['type'] == 'O') {
                        $shippingPrice = $rate['price'];
                    } else {
                        $qty = 0;
                        foreach ($quotes[$sellerId] as $item) {
                            $product = $item->getProduct();
                            if ($product->isVirtual() || $item->getParentItem()) {
                                continue;
                            }
                            if ($item->getFreeShipping()) {
                                continue;
                            }
                            $qty += $item->getQty();
                        }
                        $shippingPrice = $qty * $rate['price'];
                    }

                    if ($rate['free_shipping'] && $total >= $rate['free_shipping']) {
                        $shippingPrice = 0;
                    }

                    $sellerRates[$sellerId][$rateId]['shipping_price'] = $shippingPrice;
                    $method->setPrice($shippingPrice);
                    $method->setCost($shippingPrice);
                    $result->append($method);
                }
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        return ['lofmpflatrateshipping' => $this->getConfigData('name')];
    }
}
