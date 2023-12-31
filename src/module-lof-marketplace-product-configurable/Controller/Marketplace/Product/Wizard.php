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
 * @package    Lof_MarketProductConfigurable
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketProductConfigurable\Controller\Marketplace\Product;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;
use Magento\Framework\App\Action\Context;
use Magento\Store\Model\StoreFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\Product;
/**
 * Class Wizard
 */
class Wizard extends \Magento\Framework\App\Action\Action
{
    /**
     * @var Builder
     */
    //protected $productBuilder;

    /**
     * @param Context $context
     * @param Builder $productBuilder
     */
    public function __construct(
        Context $context,
        ProductFactory $productFactory,
        \Magento\Framework\App\RequestInterface $request,
        Registry $registry,
        StoreFactory $storeFactory = null)
    {
        parent::__construct($context);
         $this->request = $request;
         $this->registry = $registry;
         $this->productFactory = $productFactory;
         $this->storeFactory = $storeFactory ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Store\Model\StoreFactory::class);
       // $this->productBuilder = $productBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
       // $this->productBuilder->build($this->getRequest());
         $request = $this->request;
        $productId = (int) $request->getParam('id');
        $storeId = $request->getParam('store', 0);
        $attributeSetId = (int) $request->getParam('set');
        $typeId = $request->getParam('type');
        $product = $this->createEmptyProduct($typeId, $attributeSetId, $storeId);
        $store = $this->storeFactory->create();
        $store->load($storeId);

        $this->registry->register('product', $product);
        $this->registry->register('current_product', $product);
        $this->registry->register('current_store', $store);

        /** @var \Magento\Framework\View\Result\Layout $resultLayout */
        $resultLayout = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultLayout->getLayout()->getUpdate()->removeHandle('default');

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
