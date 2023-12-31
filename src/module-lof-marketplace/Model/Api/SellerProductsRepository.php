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

namespace Lof\MarketPlace\Model\Api;

use Magento\Catalog\Api\Data\ProductSearchResultsInterfaceFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductExtension;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\EntityManager\Operation\Read\ReadExtensions;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Lof\MarketPlace\Helper\Data;
use Lof\MarketPlace\Model\SellerProduct;
use Lof\MarketPlace\Model\SellerFactory;
use Lof\MarketPlace\Api\SellerProductsRepositoryInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SellerProductsRepository implements SellerProductsRepositoryInterface
{

    /**
     * @var SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var JoinProcessorInterface
     */
    private $extensionAttributesJoinProcessor;

    /**
     * @var ReadExtensions
     */
    private $readExtensions;

    /**
     * @var \Magento\Catalog\Api\Data\ProductSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $serializer;

    /**
     * @var int
     */
    private $cacheLimit = 0;

    /**
     * @var Product[]
     */
    protected $instances = [];

    /**
     * @var Product[]
     */
    protected $instancesById = [];

     /**
     * @var mixed|array
     */
    protected $_sellers = [];

    /**
     * SellerProductsRepository constructor.
     *
     * @param CollectionFactory $productCollectionFactory
     * @param Data $helper
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ProductSearchResultsInterfaceFactory $searchResultsFactory
     * @param SellerFactory $sellerFactory
     * @param \Magento\Framework\Serialize\Serializer\Json|null $serializer
     * @param int $cacheLimit [optional]
     * @param ReadExtensions $readExtensions
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        CollectionFactory $productCollectionFactory,
        Data $helper,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ProductSearchResultsInterfaceFactory $searchResultsFactory,
        SellerFactory $sellerFactory,
        \Magento\Framework\Serialize\Serializer\Json $serializer = null,
        $cacheLimit = 1000,
        ReadExtensions $readExtensions = null
    ) {
        $this->helper = $helper;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->sellerFactory = $sellerFactory;
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Serialize\Serializer\Json::class);
        $this->cacheLimit = (int)$cacheLimit;
        $this->readExtensions = $readExtensions ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(ReadExtensions::class);
    }

    /**
     * @inheritdoc
     */
    public function getSellerProducts(int $sellerId, \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        return $this->getListSellerProducts($searchCriteria, $sellerId);
    }

    /**
     * @inheritdoc
     */
    public function getListSellersProduct(string $sellerUrl, \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $seller = $this->getSellerByUrl($sellerUrl);

        if ($seller && $seller->getId()) {
            return $this->getSellerProducts($seller->getId(), $searchCriteria);
        } else {
            throw new NoSuchEntityException(__('Seller with seller Url "%1" does not exist.', $sellerUrl));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    )
    {
        return $this->getListSellerProducts($searchCriteria);
    }

    /**
     * {@inheritdoc}
     */
    public function getListSellerProducts(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria,
        $sellerId = 0
    )
    {
        $collection = $this->_productCollectionFactory->create();
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Magento\Catalog\Api\Data\ProductInterface::class
        );

        $collection->addAttributeToSelect('*');
        $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
        $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');

        if ($sellerId) {
            $collection->addFieldToFilter('seller_id', ['eq' => $sellerId]);
        } else {
            $collection->addFieldToFilter('seller_id', ['neq' => 0]);
        }
        $collection->addAttributeToFilter('approval',
                        ['in' => [
                            SellerProduct::STATUS_NOT_SUBMITED,
                            SellerProduct::STATUS_APPROVED
                        ]
                    ]);

        $this->collectionProcessor->process($searchCriteria, $collection);

        $collection->load();

        $collection->addCategoryIds();
        $this->addExtensionAttributes($collection);
        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());

        foreach ($collection->getItems() as $product) {
            $this->cacheProduct(
                $this->getCacheKey(
                    [
                        false,
                        $product->getStoreId()
                    ]
                ),
                $product
            );
        }

        return $searchResult;
    }

    /**
     * Add product to internal cache and truncate cache if it has more than cacheLimit elements.
     *
     * @param string $cacheKey
     * @param ProductInterface $product
     * @return void
     */
    private function cacheProduct($cacheKey, ProductInterface $product)
    {
        $this->instancesById[$product->getId()][$cacheKey] = $product;
        $this->saveProductInLocalCache($product, $cacheKey);

        if ($this->cacheLimit && count($this->instances) > $this->cacheLimit) {
            $offset = round($this->cacheLimit / -2);
            $this->instancesById = array_slice($this->instancesById, $offset, null, true);
            $this->instances = array_slice($this->instances, $offset, null, true);
        }
    }

    /**
     * Removes product in the local cache by sku.
     *
     * @param string $sku
     * @return void
     */
    private function removeProductFromLocalCacheBySku(string $sku): void
    {
        $preparedSku = $this->prepareSku($sku);
        unset($this->instances[$preparedSku]);
    }

    /**
     * Removes product in the local cache by id.
     *
     * @param string|null $id
     * @return void
     */
    private function removeProductFromLocalCacheById(?string $id): void
    {
        unset($this->instancesById[$id]);
    }

    /**
     * Saves product in the local cache by sku.
     *
     * @param Product $product
     * @param string $cacheKey
     * @return void
     */
    private function saveProductInLocalCache(Product $product, string $cacheKey): void
    {
        $preparedSku = $this->prepareSku($product->getSku());
        $this->instances[$preparedSku][$cacheKey] = $product;
    }

    /**
     * Add extension attributes to loaded items.
     *
     * @param Collection $collection
     * @return Collection
     */
    private function addExtensionAttributes(Collection $collection) : Collection
    {
        foreach ($collection->getItems() as $item) {
            $this->readExtensions->execute($item);
        }
        return $collection;
    }

    /**
     * Get key for cache
     *
     * @param array $data
     * @return string
     */
    protected function getCacheKey($data)
    {
        $serializeData = [];
        foreach ($data as $key => $value) {
            if (is_object($value)) {
                $serializeData[$key] = $value->getId();
            } else {
                $serializeData[$key] = $value;
            }
        }
        $serializeData = $this->serializer->serialize($serializeData);
        return sha1($serializeData);
    }

    /**
     * Converts SKU to lower case and trims.
     *
     * @param string $sku
     * @return string
     */
    private function prepareSku(string $sku): string
    {
        return mb_strtolower(trim($sku));
    }

    /**
     * get seller by sellerUrl
     *
     * @param string $sellerUrl
     * @return \Lof\MarketPlace\Model\Seller
     */
    protected function getSellerByUrl(string $sellerUrl)
    {
        if (!isset($this->_sellers[$sellerUrl])) {
            $seller = $this->sellerFactory->create()->getCollection()
                    ->addFieldToFilter('url_key', ['eq' => $sellerUrl])
                    ->addFieldToFilter("status", \Lof\MarketPlace\Model\Seller::STATUS_ENABLED)
                    ->getFirstItem();
            $this->_sellers[$sellerUrl] = $seller;
        }
        return $this->_sellers[$sellerUrl];
    }
}
