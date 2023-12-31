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

namespace Lof\MarketPlace\Model;

use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UpdateStock implements \Lof\MarketPlace\Api\UpdateStockRepositoryInterface
{

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    protected $resourceModel;

    /**
     * @var CategoryLinkManagementInterface|null
     */
    protected $linkManagement;

    /**
     * @var Product
     */
    protected $product;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var mixed|array
     */
    protected $_sellers = [];

    /**
     * UpdateStock constructor.
     *
     * @param ProductFactory $productFactory
     * @param SellerFactory $sellerFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product $resourceModel
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param ProductRepositoryInterface $productRepository
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        ProductFactory $productFactory,
        SellerFactory $sellerFactory,
        \Magento\Catalog\Model\ResourceModel\Product $resourceModel,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        ProductRepositoryInterface $productRepository
    ) {
        $this->productFactory = $productFactory;
        $this->sellerFactory = $sellerFactory;
        $this->stockRegistry = $stockRegistry;
        $this->resourceModel = $resourceModel;
        $this->productRepository = $productRepository;
    }

    /**
     * @inheritdoc
     */
    public function saveSellerStock(int $customerId, $product)
    {
        if (!$product->getProductId()) {
            throw new NoSuchEntityException(__('product_id is required.'));
        }

        $seller = $this->getSellerByCustomer($customerId);

        if ($seller && $seller->getId()) {
            $productSellerId = $this->getSellerIdByProduct($product->getProductId());
            if ($productSellerId != $seller->getId()) {
                throw new NoSuchEntityException(__('Not found product with ID "%1" for current seller.'. $product->getProductId()));
            }
            $foundProduct = $this->productFactory->create();
            $foundProduct = $this->resourceModel->load($foundProduct, $product->getProductId());

            if ($foundProduct && !$foundProduct->getId()) {
                throw new NoSuchEntityException(__('Not found product with ID "%1".'. $product->getProductId()));
            }
            try {
                $stockItem = $this->stockRegistry->getStockItem($product->getProductId()); // load stock of that product
                $stockItem->setData('is_in_stock', $product->getIsInStock()); //set updated data as your requirement
                if ($product->getQty()) {
                    $stockItem->setQty($product->getQty()); //set updated quantity
                } else {
                    $stockItem->setQty(0); //set updated quantity
                }

                $stockItem->setData('use_config_notify_stock_qty', 1);
                $stockItem->save(); //save stock of item

                /** Also save product with qty */
                $foundProduct->setQty($product->getQty());
                $this->resourceModel->save($foundProduct);

            } catch (\Exception $exception) {
                throw new CouldNotSaveException(__(
                    'Could not save the product stock: %1',
                    $exception->getMessage()
                ));
            }
            return $product;
        } else {
            throw new NoSuchEntityException(__('Seller with customerId "%1" does not exist.', $customerId));
        }
    }

    /**
     * @inheritdoc
     */
    public function saveSellerProductPrice(int $customerId, $product)
    {
        if (!$product->getProductId() || !$product->getPrice()) {
            throw new NoSuchEntityException(__('product_id and price are required.'));
        }

        $seller = $this->getSellerByCustomer($customerId);

        if ($seller && $seller->getId()) {
            $productSellerId = $this->getSellerIdByProduct($product->getProductId());
            if ($productSellerId != $seller->getId()) {
                throw new NoSuchEntityException(__('Not found product with ID "%1".'. $product->getProductId()));
            }
            try {
                // phpcs:disable Generic.Files.LineLength.TooLong
                $attributeCode = 'price';
                $entityType = 'catalog_product';
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $attributeInfo = $objectManager->get(\Magento\Eav\Model\Entity\Attribute::class)
                    ->loadByCode($entityType, $attributeCode);
                $attribteId = $attributeInfo->getAttributeId();
                $resource = $objectManager->get(\Magento\Framework\App\ResourceConnection::class);
                $connection = $resource->getConnection();
                $tableName = $resource->getTableName('catalog_product_entity_decimal');

                $query = "UPDATE " . $tableName . " SET value = " . (float)$product->getPrice() . " WHERE attribute_id = " . $attribteId . " AND entity_id =" . $product->getProductId();

                $connection->query($query);

                /** update product price */
                $productCore = $this->productFactory->create();
                $this->resourceModel->load($productCore, $product->getProductId());

                $productCore->setPrice((float)$product->getPrice());
                $this->resourceModel->save($productCore);
            } catch (\Exception $exception) {
                throw new CouldNotSaveException(__(
                    'Could not save the product stock: %1',
                    $exception->getMessage()
                ));
            }
            return $product;
        } else {
            throw new NoSuchEntityException(__('Seller with customerId "%1" does not exist.', $customerId));
        }
    }

    /**
     * get seller by customer id
     *
     * @param int $customerId
     * @return Seller
     */
    protected function getSellerByCustomer(int $customerId)
    {
        if (!isset($this->_sellers[$customerId])) {
            $seller = $this->sellerFactory->create()->getCollection()
                    ->addFieldToFilter("customer_id", $customerId)
                    ->addFieldToFilter("status", Seller::STATUS_ENABLED)
                    ->getFirstItem();
            $this->_sellers[$customerId] = $seller;
        }
        return $this->_sellers[$customerId];
    }

    /**
     * Get seller product by product id
     *
     * @param int $productId
     * @return int
     */
    protected function getSellerIdByProduct($productId)
    {
        $product = $this->productRepository->getById($productId);
        $sellerId = $product ? $product->getSellerId() : 0;

        return $sellerId;
    }
}
