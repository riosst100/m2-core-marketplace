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
 * @package    Lof_PreOrder
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\PreOrder\Ui\Component\Listing\Column;

use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class ProductName.
 * phpcs:disable Magento2.Commenting.ClassAndInterfacePHPDocFormatting.InvalidDescription:
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class ProductName extends Column
{
    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var ProductFactory
     */
    protected $_productloader;

    /**
     * Constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param ProductFactory $_productloader
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     * @SuppressWarnings(PHPMD.CamelCaseParameterName)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ProductFactory $_productloader,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->_productloader = $_productloader;
        $this->_urlBuilder = $urlBuilder;
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
                if (isset($item['product_id'])) {
                    $product = $this->_productloader->create()->load($item['product_id']);
                    $url = $this->_urlBuilder->getUrl('catalog/product/edit', ['id' => $item['product_id']]);
                    $title = __('View Product');
                    $item[$fieldName] = "<a href='" . $url . "'
                    target='blank'
                    title='" . $title . "'>" . $product->getName() . '</a>';
                }
            }
        }
        return $dataSource;
    }
}
