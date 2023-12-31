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

use Lof\MarketPlace\Api\Data\ProductInterfaceFactory;
use Lof\MarketPlace\Api\Data\SellerProductSearchResultsInterfaceFactory;
use Lof\MarketPlace\Api\SellersFrontendProductRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Lof\MarketPlace\Model\ResourceModel\SellerProduct as ResourceModelSellerProduct;
use Lof\MarketPlace\Model\ResourceModel\SellerProduct\CollectionFactory as SellerProductCollectionFactory;
use Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory as SellerCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\EntityManager\Operation\Read\ReadExtensions;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SellersFrontendProductRepository implements SellersFrontendProductRepositoryInterface
{
    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;

    /**
     * @var ProductInterfaceFactory
     */
    protected $dataFactory;

    /**
     * @var SellerProductSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var ExtensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;

    /**
     * @var ResourceModelSellerProduct
     */
    protected $resource;

    /**
     * @var SellerProductCollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var SellerProductFactory
     */
    protected $modelFactory;

    /**
     * @var CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var ReadExtensions
     */
    private $readExtensions;

    /**
     * @var SellerCollectionFactory
     */
    protected $sellerCollectionFactory;

    /**
     * @var mixed|array
     */
    protected $_sellers = [];

    /**
     * SellersFrontendProductRepository constructor.
     *
     * @param ResourceModelSellerProduct $resource
     * @param SellerProductFactory $modelFactory
     * @param ProductInterfaceFactory $dataFactory
     * @param SellerProductCollectionFactory $collectionFactory
     * @param SellerProductSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param CollectionFactory $productCollectionFactory
     * @param SellerCollectionFactory $sellerCollectionFactory
     * @param ReadExtensions|null $readExtensions
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ResourceModelSellerProduct $resource,
        SellerProductFactory $modelFactory,
        ProductInterfaceFactory $dataFactory,
        SellerProductCollectionFactory $collectionFactory,
        SellerProductSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        CollectionFactory $productCollectionFactory,
        SellerCollectionFactory $sellerCollectionFactory,
        ReadExtensions $readExtensions = null
    ) {
        $this->sellerCollectionFactory = $sellerCollectionFactory;
        $this->resource = $resource;
        $this->modelFactory = $modelFactory;
        $this->dataFactory = $dataFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->readExtensions = $readExtensions ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(ReadExtensions::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        $sellerId,
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->_productCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection);

        $collection->addAttributeToSelect('*');
        $collection->joinAttribute(
            'status',
            'catalog_product/status',
            'entity_id',
            null,
            'inner'
        );
        $collection->joinAttribute(
            'visibility',
            'catalog_product/visibility',
            'entity_id',
            null,
            'inner'
        );
        $collection->addFieldToFilter('seller_id', $sellerId);

        $this->collectionProcessor->process($criteria, $collection);

        $collection->addCategoryIds();
        $this->addExtensionAttributes($collection);
        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setSearchCriteria($criteria);
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());

        return $searchResult;
    }

    /**
     * {@inheritdoc}
     */
    public function getSellerProductsList(
        string $sellerUrl,
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $seller = $this->getSellerByUrl($sellerUrl);
        if ($seller && $seller->getId()) {
            $collection = $this->collectionFactory->create();

            $this->extensionAttributesJoinProcessor->process(
                $collection,
                \Lof\MarketPlace\Api\Data\ProductInterface::class
            );

            $this->collectionProcessor->process($criteria, $collection);

            $collection->addFieldToFilter('seller_id', $seller->getId());

            $searchResults = $this->searchResultsFactory->create();
            $searchResults->setSearchCriteria($criteria);

            $items = [];
            foreach ($collection as $model) {
                $items[] = $model->getDataModel();
            }

            $searchResults->setItems($items);
            $searchResults->setTotalCount($collection->getSize());
            return $searchResults;
        } else {
            throw new NoSuchEntityException(__('Seller with url key "%1" does not exist.', $sellerUrl));
        }
    }

    /**
     * Add extension attributes to loaded items.
     *
     * @param Collection $collection
     * @return Collection
     */
    private function addExtensionAttributes(Collection $collection): Collection
    {
        foreach ($collection->getItems() as $item) {
            $this->readExtensions->execute($item);
        }
        return $collection;
    }

    /**
     * get seller by sellerUrl
     *
     * @param string $sellerUrl
     * @return Seller
     */
    protected function getSellerByUrl(string $sellerUrl)
    {
        if (!isset($this->_sellers[$sellerUrl])) {
            $seller = $this->sellerFactory->create()->getCollection()
                    ->addFieldToFilter('url_key', ['eq' => $sellerUrl])
                    ->addFieldToFilter("status", Seller::STATUS_ENABLED)
                    ->getFirstItem();
            $this->_sellers[$sellerUrl] = $seller;
        }
        return $this->_sellers[$sellerUrl];
    }
}

