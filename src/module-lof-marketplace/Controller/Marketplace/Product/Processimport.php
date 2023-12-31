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

use Lof\MarketPlace\Controller\Marketplace\Product\ImportResult as ImportResultController;
use Lof\MarketPlace\Helper\Data;
use Lof\MarketPlace\Model\SellerProduct;
use Lof\MarketPlace\Model\SellerProductFactory;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\CatalogInventory\Model\Stock\ItemFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\File\Csv;
use Magento\Framework\View\Result\Layout;
use Magento\ImportExport\Block\Adminhtml\Import\Frame\Result;
use Magento\ImportExport\Helper\Report;
use Magento\ImportExport\Model\History;
use Magento\ImportExport\Model\Report\ReportProcessorInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Processimport extends ImportResultController implements HttpGetActionInterface, HttpPostActionInterface
{
    const FLAG_IS_URLS_CHECKED = 'check_url_settings';

    /**
     * @var \Magento\ImportExport\Model\Import
     */
    protected $importModel;

    /**
     * @var Csv
     */
    protected $csvProcessor;

    /**
     * @var ProductFactory
     */
    protected $_productFactory;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var SellerProduct
     */
    protected $_sellerProduct;

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;


    /**
     * @var CollectionFactory
     */
    private $_attrSetColFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    private $_entityCollectionFactory;

    /**
     * @var CategoryFactory
     */
    private $_categoryFactory;

    /**
     * @var ItemFactory
     */
    private $_stockItemFactory;

    /**
     * @var \Lof\MarketPlace\Model\SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var string[]
     */
    protected $_fieldsMap = [
        'attribute_set_code' => 'attribute_set_id',
        'name' => 'name',
        'sku' => 'sku',
        'price' => 'price',
        'tax_class_name' => 'tax_class_id',
        'qty' => 'qty',
        'is_in_stock' => 'is_in_stock',
        'weight' => 'weight',
        'visibility' => 'visibility',
        'categories' => 'categories',
        'description' => 'description',
        'short_description' => 'short_description',
        'base_image' => 'image',
        'url_key' => 'url_key'
    ];

    /**
     * @var string[]
     */
    protected $_pendingAttributeFieldsMap = [
        'attribute_set_id' => 'attribute_set_id',
        'name' => 'name',
        'sku' => 'sku',
        'price' => 'price',
        'tax_class_id' => 'tax_class_id',
        'qty' => 'qty',
        'is_in_stock' => 'is_in_stock',
        'weight' => 'weight',
        'visibility' => 'visibility',
        'category_ids' => 'categories',
        'description' => 'description',
        'short_description' => 'short_description',
        'images' => 'image',
        'url_key' => 'url_key'
    ];

    /**
     * Process Import constructor.
     * @param Context $context
     * @param ReportProcessorInterface $reportProcessor
     * @param History $historyModel
     * @param Report $reportHelper
     * @param Csv $csvProcessor
     * @param ProductFactory $productFactory
     * @param Data $helper
     * @param SellerProductFactory $sellerProduct
     * @param \Magento\ImportExport\Model\Import $importModel
     * @param ResourceConnection $resourceConnection
     * @param CollectionFactory $attrSetColFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory
     * @param CategoryFactory $categoryFactory
     * @param ItemFactory $stockItemFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerFactory
     * @param \Magento\Framework\Url $frontendUrl
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        ReportProcessorInterface $reportProcessor,
        History $historyModel,
        Report $reportHelper,
        Csv $csvProcessor,
        ProductFactory $productFactory,
        Data $helper,
        SellerProductFactory $sellerProduct,
        \Magento\ImportExport\Model\Import $importModel,
        ResourceConnection $resourceConnection,
        CollectionFactory $attrSetColFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        CategoryFactory $categoryFactory,
        ItemFactory $stockItemFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Magento\Framework\Url $frontendUrl
    ) {
        parent::__construct(
            $context,
            $reportProcessor,
            $historyModel,
            $reportHelper
        );

        $this->importModel = $importModel;
        $this->csvProcessor = $csvProcessor;
        $this->_productFactory = $productFactory;
        $this->helper = $helper;
        $this->_sellerProduct = $sellerProduct;
        $this->resourceConnection = $resourceConnection;
        $this->_attrSetColFactory = $attrSetColFactory;
        $this->_entityCollectionFactory = $collectionFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->_stockItemFactory = $stockItemFactory;
        $this->customerSession = $customerSession;
        $this->sellerFactory = $sellerFactory;
        $this->_frontendUrl = $frontendUrl;
        $this->_actionFlag = $context->getActionFlag();
    }

    /**
     * @param $route
     * @param $params
     * @return string|null
     */
    public function getFrontendUrl($route, $params)
    {
        return $this->_frontendUrl->getUrl($route, $params);
    }

    /**
     * Redirect to URL
     * @param string $url
     * @return \Magento\Framework\App\ResponseInterface
     */
    protected function _redirectUrl($route = '', $params = [])
    {
        $this->getResponse()->setRedirect($this->getFrontendUrl($route, $params));
        $this->customerSession->setIsUrlNotice($this->_actionFlag->get('', self::FLAG_IS_URLS_CHECKED));
        return $this->getResponse();
    }

    /**
     * Start import process action
     *
     * @return ResultInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     * @SuppressWarnings(PHPMD.LongVariable)
     * @phpcs:disable Generic.Metrics.NestingLevel.TooHigh
     * @phpcs:disable Magento2.CodeAnalysis.EmptyBlock.DetectedCatch
     */
    public function execute()
    {
        $customerSession = $this->customerSession;
        $customerId = $customerSession->getId();
        $sellerModel = $this->sellerFactory->create()->load($customerId, 'customer_id');
        $status = $sellerModel->getStatus();
        if ($customerSession->isLoggedIn() && $status == 1) {
            $data = $this->getRequest()->getPostValue();
            if ($data) {
                $this->_eventManager->dispatch('marketplace_start_import_product', [
                    'object' => $this,
                    'seller' => $sellerModel,
                    'data' => $data
                ]);
                /** @var Layout $resultLayout */
                $resultLayout = $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
                /** @var Result $resultBlock */
                $resultBlock = $resultLayout->getLayout()->getBlock('import.frame.result');
                $resultBlock
                    ->addAction('show', 'import_validation_container')
                    ->addAction('innerHTML', 'import_validation_container_header', __('Status'))
                    ->addAction('hide', ['edit_form', 'upload_button', 'messages']);

                $approval = $this->helper->getConfig('seller_settings/approval');
                $approvalEditing = $this->helper->getConfig('seller_settings/approval_editing');
                $pendingProductAttributes = $this->helper
                    ->getConfig('seller_settings/pending_product_attributes');
                $behavior = $this->getRequest()->getParam('behavior');
                $productUpdateIds = [];

                if ($approval && $approvalEditing && $behavior != 'delete') {
                    //validate pending product attribute
                    if ($pendingProductAttributes != 0) {
                        $pendingProductAttributes = explode(',', $pendingProductAttributes);
                        foreach ($pendingProductAttributes as $k => $v) {
                            $pendingProductAttributes[$k] = $this->_pendingAttributeFieldsMap[$v];
                        }
                    }

                    $fileUpload = $this->getRequest()->getFiles();
                    $tmp_name = $fileUpload['import_file']['tmp_name'];
                    $csvData = $this->csvProcessor->getData($tmp_name);
                    if ($csvData) {
                        $csvData = $this->mergeData($csvData);
                        foreach ($csvData as $_csvData) {
                            $orgProductData = $this->_productFactory->create()
                                ->loadByAttribute('sku', $_csvData['sku']);

                            //if Pending Product Attributes is Default
                            if ($orgProductData && $pendingProductAttributes == 0) {
                                foreach ($_csvData as $key => $value) {
                                    if (isset($this->_fieldsMap[$key])) {
                                        $attributeCode = $this->_fieldsMap[$key];
                                    }

                                    //convert attribute name
                                    $attributeCode = isset($this->_fieldsMap[$key]) ? $this->_fieldsMap[$key] : '';
                                    if ($attributeCode != null && $value != null) {
                                        if ($value != $this->getProductData($orgProductData, $attributeCode)) {
                                            array_push($productUpdateIds, $orgProductData->getId());
                                        }
                                    }
                                }
                            } elseif ($orgProductData) {
                                foreach ($_csvData as $key => $value) {
                                    //convert attribute name
                                    $attributeCode = isset($this->_fieldsMap[$key]) ? $this->_fieldsMap[$key] : '';

                                    if ($attributeCode != null
                                        && in_array($attributeCode, $pendingProductAttributes)
                                        && $value != null
                                    ) {
                                        if ($value != $this->getProductData($orgProductData, $attributeCode)) {
                                            array_push($productUpdateIds, $orgProductData->getId());
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                $this->importModel->setData($data);
                $errorAggregator = $this->importModel->getErrorAggregator();

                try {
                    $this->importModel->importSource();
                } catch (\Exception $e) {
                    //
                }

                if ($this->importModel->getErrorAggregator()->hasToBeTerminated()) {
                    $resultBlock->addError(__('Maximum error count has been reached or system error is occurred!'));
                    $this->addErrorMessages($resultBlock, $errorAggregator);
                } else {
                    $this->importModel->invalidateIndex();

                    $file = $this->importModel->invalidateIndex()->uploadSource();

                    // phpcs:disable Magento2.Functions.DiscouragedFunction.Discouraged
                    // TODO: Move to Observer
                    if (file_exists($file)) {
                        try {
                            $data = $this->csvProcessor->getData($file);
                            $file = [];
                            foreach ($data as $key => $_data) {
                                if (!$key) {
                                    continue;
                                }
                                $file[] = $_data[0];
                                $product = $this->_productFactory->create()->loadByAttribute('sku', $_data[0]);

                                $productId = $product ? (int)$product->getId() : 0;

                                if (!$productId) {
                                    continue;
                                }

                                $productSellerId = $product->getSellerId();
                                $sellerId = $sellerModel->getSellerId();
                                $isPendingProduct = false;

                                if ($approval) {
                                    if ($this->isUpdateProduct($productId)) {
                                        if ($sellerId != $productSellerId) {
                                            continue;
                                        }
                                    }

                                    if ($this->isNewProduct($productId)) {
                                        $isPendingProduct = true;
                                    } elseif ($this->isUpdateProduct($productId) && $approvalEditing) {
                                        if (count($productUpdateIds) > 0) {
                                            if (in_array($product->getId(), $productUpdateIds)) {
                                                $isPendingProduct = true;
                                            }
                                        }
                                    }
                                }

                                $status = $approval ? 1 : 2;
                                $connection = $this->resourceConnection->getConnection();
                                $productData = [
                                    'approval' => $status,
                                ];

                                if (!$productSellerId) {
                                    $productData['seller_id'] = $sellerId;
                                }
                                if (!$productSellerId || ($productSellerId && ($productSellerId == $sellerId))) {
                                    $connection->update(
                                        $this->resourceConnection->getTableName('catalog_product_entity'),
                                        $productData,
                                        [
                                            'entity_id = ?' => $productId,
                                        ]
                                    );
                                }

                                $sellerProduct = $this->_sellerProduct->create()->load($productId, 'product_id');
                                if ($sellerProduct && count($sellerProduct->getData()) > 0) {
                                    $sellerProductSellerId = $sellerProduct->getSellerId();
                                    if (!$sellerProductSellerId) {
                                        $sellerProduct
                                            ->setData('seller_id', $sellerId)
                                            ->setData('status', 3)
                                            ->save();
                                    } elseif ($sellerProductSellerId == $sellerId) {
                                        if (!$approvalEditing) {
                                            $sellerProduct
                                                ->setData('status', 3)
                                                ->save();
                                        }
                                    }
                                } else {
                                    if (!$productSellerId) {
                                        $sellerProduct
                                            ->setData('seller_id', $sellerId)
                                            ->setData('product_id', $productId)
                                            ->setData('status', 0)
                                            ->setData('product_name', $product->getName())
                                            ->save();
                                    } elseif ($productSellerId && ($productSellerId == $sellerId)) {
                                        $sellerProduct
                                            ->setData('seller_id', $sellerId)
                                            ->setData('product_id', $productId)
                                            ->setData('status', 0)
                                            ->setData('product_name', $product->getName())
                                            ->save();
                                    }
                                }

                                if ($approval && $isPendingProduct) {
                                    $sellerProduct
                                        ->setData('status', 1)
                                        ->setData('product_name', $product->getName())
                                        ->save();
                                }

                            }
                            $this->_eventManager->dispatch('marketplace_import_product_success', [
                                'object' => $this,
                                'seller' => $sellerModel,
                                'data' => $data
                            ]);
                        } catch (\Exception $e) {
                            //
                        }
                    }

                    $this->addErrorMessages($resultBlock, $errorAggregator);
                    $resultBlock->addSuccess(__('Import successfully done'));
                }
                return $resultLayout;
            }

            /** @var Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('*/*/import');

            return $resultRedirect;
        } elseif ($customerSession->isLoggedIn() && $status == 0) {
            $this->_redirectUrl('lofmarketplace/seller/becomeseller');
        } else {
            $this->messageManager->addNoticeMessage(__('You must have a seller account to access'));
            $this->_redirectUrl('lofmarketplace/seller/login');
        }
    }

    /**
     * @param $productId
     * @return bool
     */
    public function isNewProduct($productId)
    {
        $sellerProduct = $sellerProduct = $this->_sellerProduct->create()->load($productId, 'product_id');
        if (!$sellerProduct->getId()) {
            return true;
        }
        return false;
    }

    /**
     * @param $productId
     * @return bool
     */
    public function isUpdateProduct($productId)
    {
        $sellerProduct = $sellerProduct = $this->_sellerProduct->create()->load($productId, 'product_id');
        if ($sellerProduct->getId()) {
            return true;
        }
        return false;
    }

    /**
     * @param $data
     * @return array
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     */
    public function mergeData($data)
    {
        $dataLength = count($data);
        $dataKeyLength = count($data[0]);
        $result = [];
        for ($dataCount = 1; $dataCount < $dataLength; $dataCount++) {
            $_data = [];
            for ($i = 0; $i < $dataKeyLength; $i++) {
                $_data[$data[0][$i]] = $data[$dataCount][$i];
            }
            array_push($result, $_data);
        }
        return $result;
    }

    /**
     * @param $attributeId
     * @return |null
     */
    public function isUpdatedAttributeSet($attributeId)
    {
        $attribute = $this->_attrSetColFactory->create()->getItemById($attributeId);
        if ($attribute) {
            return $attribute->getAttributeSetName();
        }
        return null;
    }

    /**
     * @param $attributeSetId
     * @return |null
     */
    public function getProductAttributeName($attributeSetId)
    {
        $item = $this->_attrSetColFactory->create()->load();
        $item = $item->getItemById($attributeSetId);
        $attributeSetName = $item->getAttributeSetName();
        if ($attributeSetName) {
            return $attributeSetName;
        }
        return null;
    }

    /**
     * @param $productId
     * @return float|null
     */
    public function getProductQty($productId)
    {
        $qty = $this->_stockItemFactory->create()->load($productId, 'product_id')->getQty();
        if ($qty) {
            return $qty;
        }
        return null;
    }

    /**
     * @param $productId
     * @return bool|int|null
     */
    public function getProductStockStatus($productId)
    {
        $stockStatus = $this->_stockItemFactory->create()->load($productId, 'product_id')->getIsInStock();
        if ($stockStatus) {
            return $stockStatus;
        }
        return null;
    }

    /**
     * @param $categoryIds
     * @return string
     */
    public function getProductCategory($categoryIds)
    {
        $categoryNames = [];
        foreach ($categoryIds as $categoryId) {
            $data = $this->_categoryFactory->create();
            $_categoryId = $categoryId;
            $categoryName = '';
            do {
                if ($categoryId != $_categoryId) {
                    $categoryName = '/' . $categoryName;
                }
                $data->load($categoryId);
                $name = $data->getName();
                $categoryName = $name . $categoryName;
                $categoryId = $data->getParentId();
            } while ($categoryId > 1);
            if ($categoryName != '') {
                array_push($categoryNames, $categoryName);
            }
        }
        if (count($categoryNames) > 0) {
            return implode(",", $categoryNames);
        }
        return null;
    }

    /**
     * @param $product
     * @param $key
     * @return string|bool|int
     */
    public function getProductData($product, $key)
    {
        if ($key == 'attribute_set_id') {
            return $this->getProductAttributeName($product->getData('attribute_set_id'));
        }
        if ($key == 'qty') {
            return $this->getProductQty($product->getId());
        }
        if ($key == 'is_in_stock') {
            return $this->getProductStockStatus($product->getId());
        }
        if ($key == 'categories') {
            return $this->getProductCategory($product->getCategoryIds());
        }
        if ($key == 'tax_class_id') {
            return $product->getAttributeText('tax_class_id');
        }
        if ($key == 'visibility') {
            return $product->getAttributeText('visibility')->getText();
        }
        return $product->getData($key);
    }
}
