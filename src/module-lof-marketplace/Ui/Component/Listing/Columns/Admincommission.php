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

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class Admincommission extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var \Lof\MarketPlace\Model\Commission
     */
    protected $commission;

    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $localeCurrency;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Commission1 constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $components
     * @param array $data
     * @param \Lof\MarketPlace\Model\Commission $commission
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Lof\MarketPlace\Model\Commission $commission,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->localeCurrency = $localeCurrency;
        $this->storeManager = $storeManager;
        $this->commission = $commission;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = 'commission';
            $productPrice = 'seller_product_total';
            $sellerAmount = 'seller_amount';
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item[$fieldName])) {
                    //$item[$fieldName] =(100 - $item[$fieldName]) . ' %';
                    $commissionValue = (float)$item[$productPrice] > 0 ? (($item[$productPrice] - $item[$sellerAmount]) / $item[$productPrice] * 100): 0;
                    $commissionValue = (round($commissionValue, 1)). '%';
                    $item[$fieldName] = ($item[$productPrice] - $item[$sellerAmount]);
                    $item[$fieldName] .= " ( ".$commissionValue." )";
                }
            }
        }

        return $dataSource;
    }
}
