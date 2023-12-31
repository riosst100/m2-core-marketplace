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

class WithdrawalSeller extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Lof\MarketPlace\Model\Seller
     */
    protected $seller;

    /**
     * @var \Lof\MarketPlace\Model\Amount
     */
    protected $amount;

    /**
     * @var PriceCurrencyInterface
     */
    protected $_priceCurrency;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceFormatter;

    /**
     * WithdrawalSeller constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param \Lof\MarketPlace\Model\Seller $seller
     * @param \Lof\MarketPlace\Model\Amount $amount
     * @param PriceCurrencyInterface $priceCurrency
     * @param PriceCurrencyInterface $priceFormatter
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        \Lof\MarketPlace\Model\Seller $seller,
        \Lof\MarketPlace\Model\Amount $amount,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        PriceCurrencyInterface $priceFormatter,
        array $components = [],
        array $data = []
    ) {
        $this->amount = $amount;
        $this->seller = $seller;
        $this->urlBuilder = $urlBuilder;
        $this->_priceCurrency = $priceCurrency;
        $this->priceFormatter = $priceFormatter;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source.
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {
                $seller = $this->seller->getCollection()->addFieldToFilter('seller_id', $item['seller_id']);
                $amount = $this->amount->getCollection()->addFieldToFilter('seller_id', $item['seller_id']);
                foreach ($amount as $key => $_amount) {
                    $item['seller_amount'] = $this->getPriceFomat(
                        $_amount->getData('amount'),
                        $this->getCurrentCurrencyCode()
                    );
                }
                foreach ($seller as $key => $_seller) {
                    $item['sellerid'] = $item['seller_id'];
                    $item[$fieldName] = $_seller->getData('name');
                }
            }
        }

        return $dataSource;
    }

    /**
     * Get current currency code
     *
     * @return string
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
            PriceCurrencyInterface::DEFAULT_PRECISION,
            null,
            $currencyCode
        );
    }
}
