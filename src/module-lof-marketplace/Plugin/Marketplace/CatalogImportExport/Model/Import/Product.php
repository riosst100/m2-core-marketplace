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

namespace Lof\MarketPlace\Plugin\Marketplace\CatalogImportExport\Model\Import;

class Product
{
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Lof\MarketPlace\Model\SellerProduct
     */
    protected $sellerproduct;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $helper;

    /**
     * Product constructor.
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param \Lof\MarketPlace\Model\SellerProduct $sellerproduct
     */
    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Lof\MarketPlace\Helper\Data $helper,
        \Lof\MarketPlace\Model\SellerProduct $sellerproduct
    ) {
        $this->_productFactory = $productFactory;
        $this->helper = $helper;
        $this->sellerproduct = $sellerproduct;
    }

    /**
     * @param \Magento\CatalogImportExport\Model\Import\Product $subject
     * @param $result
     * @param $rowData
     * @param $rowNum
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterValidateRow(
        \Magento\CatalogImportExport\Model\Import\Product $subject,
        $result,
        $rowData,
        $rowNum
    ) {
        $sellerId = $this->helper->getSellerId();
        $product = $this->_productFactory->create()->loadByAttribute('sku', $rowData['sku']);
        if ($sellerId && $product && $product->getData() && $product->getId() && $product->getSellerId()) {
            if ($sellerId != $product->getSellerId()) {
                return false;
            }
        }
        return $result;
    }
}
