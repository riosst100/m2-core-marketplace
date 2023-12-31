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

class ProductCommission extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * ProductCommission constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
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
            $fName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item[$fName])) {
                    $html = '';
                    $html .= '<button class="button">' . __('Set Commission') . '</button>';
                    if ($item[$fName]) {
                        $html .= '<span class="seller-product-grid grid-severity-notice"><span>'. $item[$fName].'%</span></span>';
                    }
                    $url = $this->urlBuilder->getUrl('lofmarketplace/sellerproduct/commission');
                    $item[$fName . '_html'] = $html;
                    $item[$fName . '_title'] = __('Do you want set commission value (%) for product?');
                    $item[$fName . '_placehoder'] = __('input the percent number..');
                    $item[$fName . '_submitlabel'] = __('Commission');
                    $item[$fName . '_cancellabel'] = __('Reset');
                    $item[$fName . '_productid'] = $item['product_id'];
                    $item[$fName . '_sellerid'] = $item['seller_id'];
                    $item[$fName . '_entityid'] = $item['entity_id'];
                    $item[$fName . '_formaction'] = $url;
                }
            }
        }

        return $dataSource;
    }
}
