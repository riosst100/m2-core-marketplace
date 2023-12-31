<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Lofmp\LayeredNavigation\Model\Layer;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Layer\ContextInterface;
use Magento\Catalog\Model\Layer\StateFactory;

class SellerHomePage extends \Magento\Catalog\Model\Layer
{
    protected $vendorCategoryRepository;

    /**
     * VendorHomePage constructor.
     * @param ContextInterface $context
     * @param StateFactory $layerStateFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product $catalogProduct
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Registry $registry
     * @param CategoryRepositoryInterface $categoryRepository
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        StateFactory $layerStateFactory,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product $catalogProduct,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        CategoryRepositoryInterface $categoryRepository,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $layerStateFactory,
            $attributeCollectionFactory,
            $catalogProduct,
            $storeManager,
            $registry,
            $categoryRepository,
            $data
        );
    }

    /**
     * Retrieve current layer product collection.
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getProductCollection()
    {
        $collection = $this->collectionProvider->getCollection($this->getCurrentCategory());
        $this->prepareProductCollection($collection);
        return $collection;
    }

    /**
     * Get layer state key.
     *
     * @return string
     */
    public function getStateKey()
    {
        $seller = $this->registry->registry('current_seller');

        if (!$this->_stateKey) {
            $this->_stateKey = $this->stateKeyGenerator->toString($seller);
        }

        return $this->_stateKey;
    }

    /**
     * Retrieve current category model
     * If no category found in registry, the root will be taken
     *
     * @return \Magento\Catalog\Model\Category
     */
    public function getCurrentVendorCategory()
    {
        $category = $this->getData('current_seller');
        if ($category === null) {
            $category = $this->registry->registry('current_seller');
            if ($category) {
                $this->setData('current_seller', $category);
            } else {

                $this->setData('current_seller', $category);
            }
        }

        return $category;
    }

    /**
     * Change current category object
     *
     * @param mixed $category
     * @return \Magento\Catalog\Model\Layer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setCurrentVendorCategory($category)
    {
        if ($category->getId() != $this->getCurrentVendorCategory()->getId()) {
            $this->setData('current_seller', $category);
        }
        return $this;
    }

    public function getRootCategoryId()
    {
        return \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Magento\Framework\Registry')->registry('current_root_cat')->getId();
    }
}
