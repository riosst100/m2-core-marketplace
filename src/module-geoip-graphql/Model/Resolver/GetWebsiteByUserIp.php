<?php
declare(strict_types=1);

namespace CoreMarketplace\GeoIpGraphQl\Model\Resolver;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\CatalogGraphQl\Model\Resolver\Products\DataProvider\CustomAttributesFlattener;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ValueFactory;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Store\Model\StoreManagerInterface;

class GetWebsiteByUserIp implements ResolverInterface
{
    /**
     * @var CustomAttributesFlattener
     */
    private $customAttributesFlattener;

    /**
     * @var ValueFactory
     */
    private $valueFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param CustomAttributesFlattener $customAttributesFlattener
     * @param ValueFactory $valueFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        CustomAttributesFlattener $customAttributesFlattener,
        ValueFactory $valueFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->customAttributesFlattener = $customAttributesFlattener;
        $this->valueFactory = $valueFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritdoc
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        // if (!isset($value['model'])) {
        //     throw new LocalizedException(__('"model" value should be specified'));
        // }

        // /** @var \Magento\Catalog\Model\Product $product */
        // $product = $value['model'];
        // $storeId = $this->storeManager->getStore()->getId();
        // $categoryIds = $this->productCategories->getCategoryIdsByProduct((int)$product->getId(), (int)$storeId);
        // $this->categoryIds = array_merge($this->categoryIds, $categoryIds);
        // $that = $this;

        // return $this->valueFactory->create(
        //     function () use ($that, $categoryIds, $info) {
        //         $categories = [];
        //         if (empty($that->categoryIds)) {
        //             return [];
        //         }

        //         if (!$this->collection->isLoaded()) {
        //             $that->attributesJoiner->join($info->fieldNodes[0], $this->collection, $info);
        //             $this->collection->addIdFilter($this->categoryIds);
        //         }
        //         /** @var CategoryInterface | \Magento\Catalog\Model\Category $item */
        //         foreach ($this->collection as $item) {
        //             if (in_array($item->getId(), $categoryIds)) {
        //                 // Try to extract all requested fields from the loaded collection data
        //                 $categories[$item->getId()] = $this->categoryHydrator->hydrateCategory($item, true);
        //                 $categories[$item->getId()]['model'] = $item;
        //                 $requestedFields = $that->attributesJoiner->getQueryFields($info->fieldNodes[0], $info);
        //                 $extractedFields = array_keys($categories[$item->getId()]);
        //                 $foundFields = array_intersect($requestedFields, $extractedFields);
        //                 if (count($requestedFields) === count($foundFields)) {
        //                     continue;
        //                 }

        //                 // If not all requested fields were extracted from the collection, start more complex extraction
        //                 $categories[$item->getId()] = $this->categoryHydrator->hydrateCategory($item);
        //             }
        //         }

        //         return $categories;
        //     }
        // );
    }
}
