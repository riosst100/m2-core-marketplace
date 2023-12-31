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

namespace Lof\MarketPlace\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SaveProductSeller implements ObserverInterface
{
    /**
     * Catalog data
     *
     * @var \Magento\Catalog\Helper\Data
     */
    protected $catalogData;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * @var \Lof\MarketPlace\Model\SellerProduct
     */
    protected $sellerproduct;

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * @var \Lof\MarketPlace\Model\SellerFactory
     */
    protected $_sellerFactory;

    /**
     * @var \Lof\MarketPlace\Helper\Report
     */
    protected $_helperReport;

    /**
     * SaveProductSeller constructor.
     *
     * @param \Lof\MarketPlace\Model\SellerProduct $sellerproduct
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Lof\MarketPlace\Model\SellerFactory $seller
     * @param \Lof\MarketPlace\Helper\Report $helperReport
     */
    public function __construct(
        \Lof\MarketPlace\Model\SellerProduct $sellerproduct,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\ResourceConnection $resource,
        \Lof\MarketPlace\Model\SellerFactory $seller,
        \Lof\MarketPlace\Helper\Report $helperReport
    ) {
        $this->sellerproduct = $sellerproduct;
        $this->_resource = $resource;
        $this->_cacheTypeList = $cacheTypeList;
        $this->_sellerFactory = $seller;
        $this->_helperReport = $helperReport;
    }

    /**
     * @param Observer $observer
     * @return $this|void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute(Observer $observer)
    {
        $connection = $this->_resource->getConnection();
        $table_name = $this->_resource->getTableName('lof_marketplace_product');
        $productController = $observer->getController();
        $productId = $productController->getRequest()->getParam('id');
        $data = $productController->getRequest()->getPostValue();
        $product = $observer->getEvent()->getProduct();
        if (isset($data['product']['seller_id']) && $data['product']['seller_id'] > 0 && $productId) {
            $status = $product->getApproval();
            $product_name = $data['product']['name'];
            $productSellers = $data['product']['seller_id'];
            if (!is_array($productSellers)) {
                $productSellers = [];
                $productSellers[] = (int)$data['product']['seller_id'];
            }
            $sellerproduct = $this->sellerproduct
                ->getCollection()
                ->addFieldToFilter(
                    'product_id',
                    $productId
                )->getFirstItem();

            // TODO: (assigned AndyHoangHuu) convert RawQuery to ORM query.
            if (count($sellerproduct->getData()) > 0) {
                foreach ($productSellers as $k => $v) {
                    // phpcs:disable Magento2.SQL.RawQuery.FoundRawSql
                    // phpcs:disable Generic.Files.LineLength.TooLong
                    $connection->query('UPDATE ' . $table_name . ' SET seller_id = ' . $v . ',status = ' . $status . ' WHERE  product_id = ' . (int)$productId);
                }
            } else {
                // phpcs:disable Magento2.SQL.RawQuery.FoundRawSql
                // phpcs:disable Generic.Files.LineLength.TooLong
                $connection->query('DELETE FROM ' . $table_name . ' WHERE product_id =  ' . (int)$productId . ' ');
                foreach ($productSellers as $k => $v) {
                    // phpcs:disable Magento2.SQL.RawQuery.FoundRawSql
                    // phpcs:disable Generic.Files.LineLength.TooLong
                    $connection->query('INSERT INTO ' . $table_name . ' (seller_id,product_id,status,product_name) VALUES ( ' . $v . ', ' . (int)$productId . ', ' . $status . ', "' . $product_name . '")');
                }
            }
        }

        $sellerId = $product->getSellerId();
        if ($sellerId && $sellerId > 0) {
            $sellerProductCount = $this->_helperReport->getTotalProduct($sellerId) ?: 0;
            $this->_sellerFactory->create()->load($sellerId)
                ->setProductCount($sellerProductCount)
                ->save();
        }

        $this->_cacheTypeList->cleanType('full_page');
        $this->_cacheTypeList->cleanType('block_html');
        $this->_cacheTypeList->cleanType('config');

        return $this;
    }
}
