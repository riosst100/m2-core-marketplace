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

class Seller extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Lof\MarketPlace\Model\SellerFactory
     */
    protected $seller;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $helper;

    /**
     * Constructor.
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
        \Lof\MarketPlace\Model\SellerFactory $seller,
        \Lof\MarketPlace\Helper\Data $helper,
        array $components = [],
        array $data = []
    ) {
        $this->helper = $helper;
        $this->seller = $seller;
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
            $fieldName = $this->getData('name');
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $escaper = $objectManager->create(\Magento\Framework\Escaper::class);

            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['seller_id']) && !empty($item['seller_id'])) {
                    $url = $this->urlBuilder->getUrl(
                        'lofmarketplace/seller/edit',
                        ['seller_id' => $item['seller_id']]
                    );
                    $seller = $this->getSellerById((int)$item['seller_id']);
                    $sellerName = $seller && $seller->getName() ? $seller->getName() : '';
                    $html = '<a href="' . $url . '" target="_blank" title="' . __('Edit Seller') . '">';
                    $html .= $escaper->escapeHtml($sellerName);
                    $html .= '</a>';
                    $item[$fieldName] = $html;
                }
            }
        }

        return $dataSource;
    }

    /**
     * @param $sellerId
     * @return \Lof\MarketPlace\Model\Seller
     */
    public function getSellerById($sellerId)
    {
        return $this->seller->create()->load($sellerId);
    }
}
