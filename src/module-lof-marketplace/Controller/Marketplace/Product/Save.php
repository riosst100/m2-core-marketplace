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

use Lof\MarketPlace\Model\Source\Approval;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type as ProductTypes;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\RequestInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper
     */
    protected $initializationHelper;

    /**
     * @var \Magento\Catalog\Model\Product\Copier
     */
    protected $productCopier;

    /**
     * @var \Magento\Catalog\Model\Product\TypeTransitionManager
     */
    protected $productTypeManager;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Catalog\Api\CategoryLinkManagementInterface
     */
    protected $categoryLinkManagement;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_session;

    /**
     * @var \Lof\MarketPlace\Model\Seller
     */
    protected $seller;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\ConfigurableProduct\Api\LinkManagementInterface
     */
    protected $linkManagement;

    /**
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * @var \Lof\MarketPlace\Model\Sender
     */
    protected $sender;

    /**
     * Save constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper $initializationHelper
     * @param Product\Copier $productCopier
     * @param Product\TypeTransitionManager $productTypeManager
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lof\MarketPlace\Model\Seller $seller
     * @param ProductFactory $productFactory
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\ConfigurableProduct\Api\LinkManagementInterface $linkManagement
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param \Lof\MarketPlace\Model\Sender $sender
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper $initializationHelper,
        \Magento\Catalog\Model\Product\Copier $productCopier,
        \Magento\Catalog\Model\Product\TypeTransitionManager $productTypeManager,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Model\Seller $seller,
        ProductFactory $productFactory,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\ConfigurableProduct\Api\LinkManagementInterface $linkManagement,
        \Lof\MarketPlace\Helper\Data $helper,
        \Lof\MarketPlace\Model\Sender $sender
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->initializationHelper = $initializationHelper;
        $this->productCopier = $productCopier;
        $this->productTypeManager = $productTypeManager;
        $this->productRepository = $productRepository;
        $this->helper = $helper;
        $this->_session = $customerSession;
        $this->seller = $seller;
        $this->productFactory = $productFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->linkManagement = $linkManagement;
        $this->sender = $sender;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function execute()
    {
        $storeId = $this->getRequest()->getParam('store');
        $redirectBack = $this->getRequest()->getParam('back', false);

        $productId = $this->getRequest()->getParam('id');
        $resultRedirect = $this->resultRedirectFactory->create();

        $data = $this->getRequest()->getPostValue();

        $productAttributeSetId = $this->getRequest()->getParam('set');
        $productTypeId = $this->getRequest()->getParam('type');
        $seller_id = $this->helper->getSellerId();

        if ($data) {
            try {
                $this->_eventManager->dispatch(
                    'marketplace_seller_prepare_save_product',
                    ['account_controller' => $this, 'seller_id' => $seller_id, 'request' => $this->getRequest(), 'store_id' => $storeId]
                );
                $product = $this->build($this->getRequest());
                $approval = $this->helper->getConfig('seller_settings/approval');
                $approvalEditing = $this->helper->getConfig('seller_settings/approval_editing');

                $product = $this->initializationHelper->initialize($product);
                //Need Admin to approve new product
                if ($product->isObjectNew()) {
                    if ($approval) {
                        $product->setStatus('2');

                        $product->setApproval(Approval::STATUS_PENDING);
                    } else {
                        $product->setApproval(Approval::STATUS_APPROVED);
                    }
                } else {
                    if ($approvalEditing) {
                        $orgData = $product->getOrigData();
                        $newData = $product->getData();

                        if ($this->hasDataChanges($orgData, $newData)) {
                            $product->setStatus('2');
                            $product->setApproval(Approval::STATUS_PENDING);
                            $this->messageManager->addNoticeMessage(
                                __('This product has been changed to the Pending status.')
                            );
                        }
                    }
                }

                $this->productTypeManager->processProduct($product);
                /*Set vendor ID and save*/
                $product->setWebsiteIds(
                    [$this->storeManager->getWebsite()->getId() => $this->storeManager->getWebsite()->getId()]
                );

                if (isset($data['product'][$product->getIdFieldName()])) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Unable to save product'));
                }

                $originalSku = $product->getSku();
                $product->setSellerId($seller_id);
                $product->save();

                $this->getCategoryLinkManagement()->assignProductToCategories(
                    $product->getSku(),
                    $product->getCategoryIds()
                );

                /* $this->handleImageRemoveError($data, $product->getId());*/
                $productId = $product->getId();
                $productAttributeSetId = $product->getAttributeSetId();
                $productTypeId = $product->getTypeId();

                /**
                 * Do copying data to marketplace
                 */
                if ($productId) {
                    $sellerProduct = $this->_objectManager->create(\Lof\MarketPlace\Model\SellerProduct::class);
                    $model = $this->_objectManager->create(\Lof\MarketPlace\Model\SellerProduct::class);
                    foreach ($sellerProduct->getCollection()->getData() as $_seller) {
                        if ($productId == $_seller['product_id']) {
                            $model->load($_seller['entity_id']);
                        }
                    }

                    $model->setProductId($productId)
                        ->setStoreId($this->helper->getCurrentStoreId())
                        ->setSellerId($product->getSellerId())
                        ->setStatus($product->getApproval())
                        ->save();
                }
                /**
                 * Do copying data to stores
                 */
                if (isset($data['copy_to_stores'])) {
                    foreach ($data['copy_to_stores'] as $storeTo => $storeFrom) {
                        $this->_objectManager->create(\Magento\Catalog\Model\Product::class)
                            ->setStoreId($storeFrom)
                            ->load($productId)
                            ->setStoreId($storeTo)
                            ->save();
                    }
                }

                $this->messageManager->addSuccessMessage(__('You saved the product.'));
                if ($product->getSku() != $originalSku) {
                    $escaper = $this->_objectManager->get(\Magento\Framework\Escaper::class);
                    $this->messageManager->addNoticeMessage(
                        __(
                            'SKU for product %1 has been changed to %2.',
                            $escaper->escapeHtml($product->getName()),
                            $escaper->escapeHtml($product->getSku())
                        )
                    );
                }

                $this->_eventManager->dispatch(
                    'controller_action_catalog_product_save_entity_after',
                    ['controller' => $this, 'product' => $product, 'productId' => $productId]
                );

                if ($this->helper->getConfig('email_settings/enable_send_email')) {
                    if (!$this->getRequest()->getParam('id')) {
                        $seller = $this->seller->load((int)$seller_id);
                        $data = $product->getData();
                        $data['seller_name'] = $seller->getShopTitle() ? $seller->getShopTitle() : $seller->getName();
                        $data['email'] = $seller->getEmail();
                        $data['url'] = $seller->getUrl();
                        $this->sender->newSellerProduct($data);
                    } else {
                        if ($approvalEditing) {
                            $seller = $this->seller->load((int)$seller_id);
                            $data = $product->getData();
                            $data['seller_name'] = $seller->getShopTitle() ? $seller->getShopTitle() : $seller->getName();
                            $data['email'] = $seller->getEmail();
                            $data['url'] = $seller->getUrl();
                            $this->sender->editSellerProduct($data);
                        }
                    }
                }

                if ($redirectBack === 'duplicate') {
                    if ($productId) {
                        $oldProduct = $this->_objectManager->create(\Magento\Catalog\Model\Product::class);
                        $newProduct = $this->productCopier->copy($oldProduct->load($productId));
                    } else {
                        $newProduct = $this->productCopier->copy($product);
                    }
                    $model = $this->_objectManager->create(\Lof\MarketPlace\Model\SellerProduct::class);
                    $model->setProductId($newProduct->getEntityId())
                        ->setStoreId($this->helper->getCurrentStoreId())
                        ->setSellerId($product->getSellerId())
                        ->save();

                    $this->_eventManager->dispatch(
                        'marketplace_seller_duplicate_product',
                        ['account_controller' => $this, 'seller_id' => $seller_id, "seller_product" => $model, 'request' => $this->getRequest(), 'store_id' => $storeId]
                    );
                    $this->messageManager->addSuccessMessage(__('You duplicated the product.'));
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->_session->setProductData($data);
                $redirectBack = $productId ? true : 'new';
            } catch (\Exception $e) {
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->_session->setProductData($data);
                $redirectBack = $productId ? true : 'new';
            }
        } else {
            $this->_redirect('catalog/product/index', ['store' => $storeId]);
            $this->messageManager->addErrorMessage(__('No data to save'));
            return $resultRedirect;
        }

        if ($redirectBack === 'new') {
            $result = $this->_redirect(
                'catalog/product/new',
                ['set' => $productAttributeSetId, 'type' => $productTypeId]
            );
        } elseif ($redirectBack === 'duplicate' && isset($newProduct)) {
            $result = $this->_redirect(
                'catalog/product/edit',
                ['id' => $newProduct->getId(), 'back' => null, '_current' => true]
            );
        } elseif ($redirectBack) {
            $result = $this->_redirect(
                'catalog/product/edit',
                ['id' => $productId, '_current' => true, 'set' => $productAttributeSetId]
            );
        } else {
            $result = $this->_redirect('catalog/product', ['store' => $storeId]);
        }

        return $result;
    }

    /**
     * @param RequestInterface $request
     * @return \Magento\Catalog\Api\Data\ProductInterface|Product
     */
    public function build(RequestInterface $request)
    {
        $productId = (int)$request->getParam('id');
        $storeId = $request->getParam('store', 0);
        $attributeSetId = (int)$request->getParam('set');
        $typeId = $request->getParam('type');

        if ($productId) {
            try {
                $product = $this->productRepository->getById($productId, true, $storeId);
            } catch (\Exception $e) {
                $product = $this->createEmptyProduct(ProductTypes::DEFAULT_TYPE, $attributeSetId, $storeId);
            }
        } else {
            $product = $this->createEmptyProduct($typeId, $attributeSetId, $storeId);
        }
        return $product;
    }

    /**
     * @param int $typeId
     * @param int $attributeSetId
     * @param int $storeId
     * @return \Magento\Catalog\Model\Product
     */
    private function createEmptyProduct($typeId, $attributeSetId, $storeId): Product
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

    /**
     * Notify customer when image was not deleted in specific case.
     *
     * @param array $postData
     * @param int $productId
     * @return void
     */
//    private function handleImageRemoveError($postData, $productId)
//    {
//        if (isset($postData['product']['media_gallery']['images'])) {
//            $removedImagesAmount = 0;
//            foreach ($postData['product']['media_gallery']['images'] as $image) {
//                if (!empty($image['removed'])) {
//                    $removedImagesAmount++;
//                }
//            }
//            if ($removedImagesAmount) {
//                $expectedImagesAmount = count($postData['product']['media_gallery']['images']) - $removedImagesAmount;
//                $product = $this->productRepository->getById($productId);
//                if ($expectedImagesAmount != count($product->getMediaGallery('images'))) {
//                    $this->messageManager->addNoticeMessage(
//                        __('The image cannot be removed as it has been assigned to the other image role')
//                    );
//                }
//            }
//        }
//    }

    /**
     * @return \Magento\Catalog\Api\CategoryLinkManagementInterface|mixed
     */
    private function getCategoryLinkManagement()
    {
        if (null === $this->categoryLinkManagement) {
            $this->categoryLinkManagement = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magento\Catalog\Api\CategoryLinkManagementInterface::class);
        }
        return $this->categoryLinkManagement;
    }

    /**
     * @param $data
     * @return array
     */
    public function getDataChanges($data)
    {
        $pendingProductAttributes = $this->helper->getConfig('seller_settings/pending_product_attributes');
        $available = $this->getAvailablePendingAttributes($data);

        if ($pendingProductAttributes === '0') {
            return $available;
        }
        $pendingProductAttributes = explode(',', $pendingProductAttributes);
        foreach ($available as $key => $attrData) {
            if (!in_array($key, $pendingProductAttributes)) {
                unset($available[$key]);
            }
        }

        return $available;
    }

    /**
     * @param $data
     * @return array
     */
    public function getAvailablePendingAttributes($data)
    {
        $product = $this->build($this->getRequest());
        if ($product->getTypeId() == 'configurable') {
            return [
                'attribute_set_id' => $data['attribute_set_id'],
                'name' => $data['name'],
                'sku' => $data['sku'],
                'tax_class_id' => isset($data['tax_class_id']) ?? null,
                'qty' => (float)$data['quantity_and_stock_status']['qty'],
                'is_in_stock' => (string)$data['quantity_and_stock_status']['is_in_stock'],
                'weight' => $data['weight'] ?? null,
                'visibility' => $data['visibility'],
                'category_ids' => $data['category_ids'],
                'short_description' => $data['short_description'] ?? null,
                'description' => $data['description'] ?? null,
                'images' => $data['media_gallery']['images'],
                'url_key' => $data['url_key'],
            ];
        } else {
            return [
                'attribute_set_id' => $data['attribute_set_id'],
                'name' => $data['name'],
                'sku' => $data['sku'],
                'price' => (float)$data['price'],
                'tax_class_id' => isset($data['tax_class_id']) ?? null,
                'qty' => (float)$data['quantity_and_stock_status']['qty'],
                'is_in_stock' => (string)$data['quantity_and_stock_status']['is_in_stock'],
                'weight' => $data['weight'] ?? null,
                'visibility' => $data['visibility'],
                'category_ids' => $data['category_ids'],
                'short_description' => $data['short_description'] ?? null,
                'description' => $data['description'] ?? null,
                'images' => $data['media_gallery']['images'],
                'url_key' => $data['url_key'],
            ];
        }
    }

    /**
     * @param $orgData
     * @param $newData
     * @return bool
     */
    public function hasDataChanges($orgData, $newData)
    {
        $dataOrgCompare = $this->getDataChanges($orgData);
        $dataNewCompare = $this->getDataChanges($newData);
        foreach ($dataOrgCompare as $key => $data) {
            if (isset($data)) {
                if (is_array($data)) {
                    if ($key === 'images') {
                        foreach ($dataNewCompare['images'] as $imageKey => $value) {
                            if ($imageKey && !$data) {
                                return true;
                            } else {
                                if (!isset($data[$imageKey])) {
                                    return true;
                                }
                            }

                            if (isset($value['removed'])) {
                                if ($value['removed'] === '1') {
                                    return true;
                                }
                            }
                        }
                    } else {
                        if (array_diff_assoc($data, $dataNewCompare[$key])) {
                            return true;
                        }
                    }
                } else {
                    if ($data !== $dataNewCompare[$key]) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
