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

use Lof\MarketPlace\Api\SellerProductInterface;
use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Webapi\Rest\Request;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SellerProductManager implements SellerProductInterface
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var SellerProductFactory
     */
    protected $sellerProduct;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    protected $resourceModel;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CategoryLinkManagementInterface|null
     */
    protected $linkManagement;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var SellerProduct
     */
    protected $product;

    /**
     * SellerProductManager constructor.
     *
     * @param Request $request
     * @param SellerProductFactory $sellerProduct
     * @param ProductFactory $productFactory
     * @param CustomerFactory $customerFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param SellerFactory $sellerFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product $resourceModel
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param SellerProduct $product
     * @param CategoryLinkManagementInterface|null $linkManagement
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        Request $request,
        SellerProductFactory $sellerProduct,
        ProductFactory $productFactory,
        CustomerFactory $customerFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        SellerFactory $sellerFactory,
        \Magento\Catalog\Model\ResourceModel\Product $resourceModel,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        SellerProduct $product,
        CategoryLinkManagementInterface $linkManagement = null
    ) {
        $this->request = $request;
        $this->sellerProduct = $sellerProduct;
        $this->productFactory = $productFactory;
        $this->customerFactory = $customerFactory;
        $this->sellerFactory = $sellerFactory;
        $this->storeManager = $storeManager;
        $this->linkManagement = $linkManagement;
        $this->scopeConfig = $scopeConfig;
        $this->product = $product;
    }

    /**
     * @inheritdoc
     */
    public function assignProduct($productId, $storeId, $sellerId)
    {
        $seller = $this->sellerFactory->create()->getCollection()
            ->addFieldToFilter('seller_id', $sellerId)
            ->getFirstItem();

        if ($seller && $seller->getId()) {
            try {
                $statusApproval = $this->scopeConfig->getValue(
                    'lofmarketplace/seller_settings/approval',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                );
                $productOrg = $this->productFactory->create();
                $productOrg->load($productId);
                if ($productOrg->getId()) {
                    $sellerProduct = $this->sellerProduct->create();
                    $sellerProductData = $sellerProduct->getCollection()
                                ->addFieldToFilter('product_id', $productId)
                                ->getFirstItem()
                                ->getData();
                    $status = SellerProduct::STATUS_WAITING;
                    if ($statusApproval == 0) {
                        $status = SellerProduct::STATUS_APPROVED;
                    }
                    if (!empty($sellerProductData)) {
                        $productSeller = $sellerProduct->load($sellerProductData["entity_id"]);
                        $productSeller->setProductId($productId)
                            ->setSellerId($sellerId)
                            ->setStoreId($storeId)
                            ->setStatus( $status )
                            ->save();

                    } else {
                        $sellerProduct->setProductId($productId)
                            ->setSellerId($sellerId)
                            ->setStoreId($storeId)
                            ->setStatus( $status )
                            ->save();
                    }
                    $productOrg->load($productId)
                                ->setSellerId($sellerId)
                                ->save();
                    return true;
                } else {
                    return false;
                }
            } catch (\Exception $e) {
                // phpcs:disable Magento2.Security.LanguageConstruct.DirectOutput
                throw new CouldNotSaveException(__('Can assign product Id to Seller. Error: %1', $e->getMessage()));
            }
        } else {
            throw new NoSuchEntityException(__('Customer has not registered the seller yet'));
        }
    }

    /**
     *
     * @inheritdoc
     */
    public function setCommissionForSpecialProduct($productId, $commission, $sellerId)
    {
        $collection = $this->sellerProduct->create()->getCollection()->addFieldToFilter('product_id', $productId);
        $modelData = $this->sellerProduct->create()->getCollection()->addFieldToFilter('product_id', $productId);
        $sellerProductId = $modelData->getData()[0]['entity_id'];
        $model = $this->product;
        if (count($collection->getData()) > 0) {
            $model->load($sellerProductId);
            $model->setCommission($commission)->setSellerId($sellerId)
                ->save();
        } else {
            throw new NoSuchEntityException(__('Products with id %1 don\'t exist in seller\'s product', $productId));
        }
        $data['data'] = [];
        $data['data']['product_id'] = $productId;
        $data['data']['commission'] = $model->getCommission();
        $data['data']['seller_id'] = $model->getSellerId();
        $data['data']['entity_id'] = $model->getId();
        return $data;
    }

    /**
     * @inheritdoc
     * @param int $customerId
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function createProduct(\Magento\Catalog\Api\Data\ProductInterface $product, $customerId)
    {
        $seller = $this->sellerFactory->create()->getCollection()
            ->addFieldToFilter('customer_id', $customerId)
            ->addFieldToFilter('status', Seller::STATUS_ENABLED)
            ->getFirstItem();

        if ($seller && $seller->getId()) {
            $products = $this->productFactory->create();
            $status = $this->scopeConfig->getValue(
                'lofmarketplace/seller_settings/approval',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            /** @var Seller $seller */
            $sellerId = $seller->getSellerId();
            $storeId = $seller->getStoreId();
            $storeId = is_array($storeId) && !empty($storeId) ? $storeId[0] : (int)$storeId;

            if ($status == 0) {
                $products->setSku($product->getSku())
                    ->setName($product->getName())
                    ->setAttributeSetId($product->getAttributeSetId())
                    ->setPrice($product->getPrice())
                    ->setStatus($product->getStatus())
                    ->setVisibility($product->getVisibility())
                    ->setTypeId($product->getTypeId())
                    ->setWeight($product->getWeight())
                    ->setSellerId($sellerId)
                    ->setMediaGalleryEntries($product->getMediaGalleryEntries())
                    ->setExtensionAttributes($product->getExtensionAttributes())
                    ->save();

                $sellerProduct = $this->sellerProduct->create();
                $sellerProduct->setProductId($products->getId())
                    ->setSellerId($sellerId)
                    ->setStoreId($storeId)
                    ->setStatus(SellerProduct::STATUS_APPROVED)
                    ->save();

                $data['product'] = [];
                $data['product']['product_id'] = $products->getId();
                $data['product']['sku'] = $products->getSku();
                $data['product']['name'] = $products->getName();
                $data['product']['price'] = $products->getPrice();
                $data['product']['visibility'] = $products->getVisibility();
                $data['product']['type_id'] = $products->getTypeId();
                $data['product']['seller_id'] = $sellerId;
                $data['product']['customer_id'] = $customerId;
                return $data;
            } else {
                $products->setSku($product->getSku())
                    ->setName($product->getName())
                    ->setAttributeSetId($product->getAttributeSetId())
                    ->setPrice($product->getPrice())
                    ->setStatus($product->getStatus())
                    ->setVisibility($product->getVisibility())
                    ->setTypeId($product->getTypeId())
                    ->setWeight($product->getWeight())
                    ->setSellerId($sellerId)
                    ->setMediaGalleryEntries($product->getMediaGalleryEntries())
                    ->setExtensionAttributes($product->getExtensionAttributes())->save();
                $sellerProduct = $this->sellerProduct->create();
                $sellerProduct->setProductId($products->getId())
                    ->setSellerId($sellerId)
                    ->setStoreId($storeId)
                    ->setStatus(SellerProduct::STATUS_WAITING)
                    ->save();

                $data['product'] = [];
                $data['product']['product_id'] = $products->getId();
                $data['product']['sku'] = $products->getSku();
                $data['product']['name'] = $products->getName();
                $data['product']['price'] = $products->getPrice();
                $data['product']['visibility'] = $products->getVisibility();
                $data['product']['type_id'] = $products->getTypeId();
                $data['product']['seller_id'] = $sellerId;
                $data['product']['customer_id'] = $customerId;

                return $data;
            }
        } else {
            throw new NoSuchEntityException(__('Customer has not registered the seller yet'));
        }
    }
}
