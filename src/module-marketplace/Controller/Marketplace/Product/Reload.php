<?php

namespace CoreMarketplace\MarketPlace\Controller\Marketplace\Product;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
use Magento\Store\Model\StoreFactory;
use Magento\Framework\Registry;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;

class Reload extends \Lof\MarketPlace\Controller\Marketplace\Product\Reload
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $productId = (int)$this->getRequest()->getParam('id');
        /** @var \Magento\Framework\View\Result\Layout $resultLayout */
        $resultLayout = $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
        if ($productId) {
            $product = $this->helper->getProductById($productId);
        } else {
            $request = $this->request;
            $storeId = $request->getParam('store', 0);
            $attributeSetId = (int)$request->getParam('set') ?: 4;
            $typeId = $request->getParam('type');
            $product = $this->createEmptyProduct($typeId, $attributeSetId, $storeId);
        }
        $store = $this->storeFactory->create();
        $store->load($product->getStoreId());
        $this->registry->register('product', $product);
        $this->registry->register('current_product', $product);
        $this->registry->register('current_store', $store);

        $resultLayout->getLayout()->getUpdate()->addHandle(['catalog_product_' . $product->getTypeId()]);
        $resultLayout->getLayout()->getUpdate()->removeHandle('default');
        $resultLayout->setHeader('Content-Type', 'application/json', true);

        return $resultLayout;
    }

    /**
     * @param int $typeId
     * @param int $attributeSetId
     * @param int $storeId
     * @return \Magento\Catalog\Model\Product
     */
    public function createEmptyProduct($typeId, $attributeSetId, $storeId): Product
    {
        /** @var $product \Magento\Catalog\Model\Product */
        $product = $this->productFactory->create();
        $product->setData('_edit_mode', true);

        if ($typeId !== null) {
            $product->setTypeId($typeId);
        }

        if ($storeId !== null) {
            $product->setStoreId($storeId);
        }

        if ($attributeSetId) {
            $product->setAttributeSetId($attributeSetId);
        }

        return $product;
    }
}
