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
 * @package    Lofmp_PreOrder
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\PreOrder\Ui\Component\Listing\Column;

use Lof\MarketPlace\Model\SellerFactory;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class SellerName.
 * phpcs:disable Magento2.Commenting.ClassAndInterfacePHPDocFormatting.InvalidDescription:
 * @SuppressWarinings(PHPMD.CamelCasePropertyName)
 */
class SellerName extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var SellerFactory
     */
    protected $seller;

    /**
     * Constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param SellerFactory $seller
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        SellerFactory $seller,
        array $components = [],
        array $data = []
    ) {
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
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['seller_id'])) {
                    $seller = $this->seller->create()->load($item['seller_id']);
                    if ($seller->getData()) {
                        $item[$fieldName] = "<a href='" . $this->urlBuilder->getUrl(
                            'lofmarketplace/seller/edit',
                            ['seller_id' => $item['seller_id']]
                        ) . "' target='blank' title='" . __('View Seller') . "'>" . $seller->getName() . '</a>';
                    } else {
                        $item[$fieldName] = 'admin';
                    }
                }
            }
        }

        return $dataSource;
    }
}
