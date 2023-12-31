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

namespace Lof\MarketPlace\Controller\Marketplace\Product;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
use Magento\Store\Model\StoreFactory;
use Magento\Framework\Registry;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;

class Reload extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $helper;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var StoreFactory|mixed|null
     */
    protected $storeFactory;

    /**
     * Reload constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param StoreFactory|null $storeFactory
     * @param ProductFactory $productFactory
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        StoreFactory $storeFactory = null,
        ProductFactory $productFactory,
        \Lof\MarketPlace\Helper\Data $helper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->session = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->helper = $helper;
        $this->registry = $registry;
        $this->request = $request;
        $this->productFactory = $productFactory;
        $this->storeFactory = $storeFactory ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Store\Model\StoreFactory::class);
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if (!$this->getRequest()->getParam('set')) {
            return $this->resultFactory->create(ResultFactory::TYPE_FORWARD)->forward('noroute');
        }
        $productId = (int)$this->getRequest()->getParam('id');
        /** @var \Magento\Framework\View\Result\Layout $resultLayout */
        $resultLayout = $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
        if ($productId) {
            $product = $this->helper->getProductById($productId);
        } else {
            $request = $this->request;
            $storeId = $request->getParam('store', 0);
            $attributeSetId = (int)$request->getParam('set');
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
