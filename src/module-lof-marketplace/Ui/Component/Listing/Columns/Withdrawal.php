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

namespace Lof\MarketPlace\Ui\Component\Listing\Columns;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class Withdrawal extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Lof\MarketPlace\Model\Payment
     */
    protected $payment;

    /**
     * @var PriceCurrencyInterface
     */
    protected $_priceCurrency;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceFormatter;

    /**
     * @var \Lof\MarketPlace\Model\Seller
     */
    protected $seller;

    /**
     * @var \Lof\MarketPlace\Model\Amount
     */
    protected $amount;

    /**
     * Withdrawal constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param \Lof\MarketPlace\Model\Payment $payment
     * @param \Lof\MarketPlace\Model\Amount $amount
     * @param \Lof\MarketPlace\Model\Seller $seller
     * @param PriceCurrencyInterface $priceCurrency
     * @param PriceCurrencyInterface $priceFormatter
     * @param array $components
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        \Lof\MarketPlace\Model\Payment $payment,
        \Lof\MarketPlace\Model\Amount $amount,
        \Lof\MarketPlace\Model\Seller $seller,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        PriceCurrencyInterface $priceFormatter,
        array $components = [],
        array $data = []
    ) {
        $this->amount = $amount;
        $this->urlBuilder = $urlBuilder;
        $this->payment = $payment;
        $this->seller = $seller;
        $this->_priceCurrency = $priceCurrency;
        $this->priceFormatter = $priceFormatter;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {
                $payment = $this->payment->getCollection()->addFieldToFilter('payment_id', $item['payment_id']);

                $payment_name = '';
                foreach ($payment as $_payment) {
                    $payment_name = $_payment->getData('name');
                }

                $item[$fieldName . '_html'] = "<button class='button'><span>" . __('View Transaction') . "</span></button>";
                $item[$fieldName . '_title'] = __('Withdrawal Information');
                $item[$fieldName . '_submitlabel'] = __('Complete Withdrawal');
                $item[$fieldName . '_cancellabel'] = __('Cancel');
                $item[$fieldName . '_withdrawalid'] = $item['withdrawal_id'];
                $item[$fieldName . '_status'] = $item['status'];
                $item[$fieldName . '_sellername'] = $item['seller_id'];
                $item[$fieldName . '_sellerid'] = $item['sellerid'];
                $item[$fieldName . '_balance'] = $item['seller_amount'];
                $item[$fieldName . '_paymentname'] = $payment_name;
                $item[$fieldName . '_email'] = $item['email'];
                $item[$fieldName . '_amount'] = $item['amount'];
                $item[$fieldName . '_fee'] = $item['fee'];
                $item[$fieldName . '_netamount'] = $item['net_amount'];
                $item[$fieldName . '_comment'] = $item['comment'];
                $item[$fieldName . '_adminmessage'] = $item['admin_message'];
                $item[$fieldName . '_createdat'] = $item['created_at'];
                $item[$fieldName . '_formaction'] = $this->urlBuilder->getUrl('lofmarketplace/withdrawal/submit');
            }
        }

        return $dataSource;
    }

    /**
     * @return mixed
     */
    public function getCurrentCurrencyCode()
    {
        return $this->_priceCurrency->getCurrency()->getCurrencyCode();
    }

    /**
     * @param $price
     * @param $base_currency_code
     * @return string
     */
    public function getPriceFomat($price, $base_currency_code)
    {
        $currencyCode = isset($base_currency_code) ? $base_currency_code : null;
        return $this->priceFormatter->format(
            $price,
            false,
            null,
            null,
            $currencyCode
        );
    }
}
