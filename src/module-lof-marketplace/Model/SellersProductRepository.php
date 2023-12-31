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


use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Lof\MarketPlace\Helper\Data;

class SellersProductRepository extends \Magento\Catalog\Model\ProductRepository
{
    /**
     * @var FilterGroup|null
     */
    private $filterGroup = null;

     /**
     * @var Data
     */
    protected $helperData = null;

    /**
     * @var SellerProductFactory
     */
    protected $sellerProduct = null;

    /**
     * @var mixed|Seller[]
     */
    protected $_seller = [];

    /**
     * @var mixed
     */
    protected $_cachedSellerProduct = [];

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function sellerSave(int $customerId, \Magento\Catalog\Api\Data\ProductInterface $product, $saveOptions = false)
    {
        $seller = $this->getSellerByCustomerId($customerId);
        if ($seller && $seller->getId()) {
            if ($this->isSellerManageProduct($seller->getId(), $product->getId())){
                $product->setSellerId($seller->getId());
                $product = $this->save($product, $saveOptions);

                $this->updateSellerProduct($product, $seller);

                return $product;
            } else {
                throw new NoSuchEntityException(__('The Product is not available for the seller ID: %1.', $seller->getId()));
            }
        } else {
            throw new NoSuchEntityException(__('Seller account is not exists.'));
        }
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function sellerGet(int $customerId, $sku, $editMode = false, $storeId = null, $forceReload = false)
    {
        $seller = $this->getSellerByCustomerId($customerId);
        if ($seller && $seller->getId()) {
            $product = $this->get($sku, $editMode, $storeId, $forceReload);
            if ($seller->getId() != $product->getSellerId()){
                throw new NoSuchEntityException(__('The Product is not available for the seller ID: %1.', $seller->getId()));
            }
            return $product;
        } else {
            throw new NoSuchEntityException(__('Seller account is not exists.'));
        }
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function sellerGetById(int $customerId, $productId, $editMode = false, $storeId = null, $forceReload = false)
    {
        $seller = $this->getSellerByCustomerId($customerId);
        if ($seller && $seller->getId()) {
            $product = $this->getById($productId, $editMode, $storeId, $forceReload);
            if ($seller->getId() != $product->getSellerId()){
                throw new NoSuchEntityException(__('The Product is not available for the seller ID: %1.', $seller->getId()));
            }
            return $product;
        } else {
            throw new NoSuchEntityException(__('Seller account is not exists.'));
        }
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function sellerGetList(int $customerId, \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $seller = $this->getSellerByCustomerId($customerId);
        if ($seller && $seller->getId()) {
            $filterGroups = $searchCriteria->getFilterGroups();
            $this->getFilterGroup()->setFilters([
                $this->getFilterBuilder()
                    ->setField('seller_id')
                    ->setConditionType('eq')
                    ->setValue((int)$seller->getId())
                    ->create()
            ]);
            if ($this->filterGroup) {
                $filterGroups = array_merge($filterGroups, [$this->filterGroup]);
                $searchCriteria->setFilterGroups($filterGroups);
                return $this->getList($searchCriteria);
            } else {
                throw new NoSuchEntityException(__('Can not get products of the seller ID: %1.', $seller->getId()));
            }
        } else {
            throw new NoSuchEntityException(__('Seller account is not exists.'));
        }
    }

    /**
     * get seller by customer id
     *
     * @param int $customerId
     * @return \Lof\MarketPlace\Model\Seller
     */
    protected function getSellerByCustomerId(int $customerId)
    {
        if (!isset($this->_seller[$customerId])) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $sellerCollection = $objectManager->create(\Lof\MarketPlace\Model\ResourceModel\Seller\Collection::class);
            $this->_seller[$customerId] = $sellerCollection
                                    ->addFieldToFilter("customer_id", $customerId)
                                    ->addFieldToFilter("status", Seller::STATUS_ENABLED)
                                    ->getFirstItem();
        }
        return $this->_seller[$customerId];
    }

    /**
     * check is seller manage product
     *
     * @param int $sellerId
     * @param int $productId
     * @return bool
     */
    protected function isSellerManageProduct(int $sellerId, $productId)
    {
        if (!$productId) {
            return false;
        }
        if (!isset($this->_cachedSellerProduct[$productId])) {
            $this->_cachedSellerProduct[$productId] = $this->getById($productId);
        }
        $productSellerId = $this->_cachedSellerProduct[$productId] ? $this->_cachedSellerProduct[$productId]->getSellerId() : 0;

        return ($sellerId == $productSellerId) ? true : false;
    }

    /**
     * get filter group
     *
     * @return FilterGroup|null
     */
    protected function getFilterGroup()
    {
        if (!$this->filterGroup) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $this->filterGroup = $objectManager->create(FilterGroup::class);
        }
        return $this->filterGroup;
    }

    /**
     * get filter builder
     *
     * @return FilterBuilder|null
     */
    protected function getFilterBuilder()
    {
        if (!$this->filterBuilder) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $this->filterBuilder = $objectManager->create(FilterBuilder::class);
        }
        return $this->filterBuilder;
    }

    /**
     * get helper Data
     *
     * @return Data|null
     */
    protected function getHelperData()
    {
        if (!$this->helperData) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $this->helperData = $objectManager->create(Data::class);
        }
        return $this->helperData;
    }

    /**
     * get helper Data
     *
     * @return Data|null
     */
    protected function getSellerProductFactory()
    {
        if (!$this->sellerProduct) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $this->sellerProduct = $objectManager->get(SellerProductFactory::class);
        }
        return $this->sellerProduct;
    }

    /**
     * update seller product
     *
     * @param mixed $product
     * @param Seller $seller
     * @return void
     */
    protected function updateSellerProduct($product, $seller)
    {
        try {
            /** @var Seller $seller */
            $sellerId = $seller->getId();
            $storeId = $seller->getStoreId();
            $storeId = is_array($storeId) && !empty($storeId) ? $storeId[0] : (int)$storeId;
            $status = $this->getHelperData()->getConfig('seller_settings/approval', $storeId);

            if ($status == 1) {
                $sellerProduct = $this->getSellerProductFactory()->create();
                $sellerProduct->setProductId($product->getId())
                    ->setSellerId($sellerId)
                    ->setStoreId($storeId)
                    ->setStatus(SellerProduct::STATUS_APPROVED)
                    ->save();
            } else {
                $sellerProduct = $this->getSellerProductFactory()->create();
                $sellerProduct->setProductId($product->getId())
                    ->setSellerId($sellerId)
                    ->setStoreId($storeId)
                    ->setStatus(SellerProduct::STATUS_WAITING)
                    ->save();
            }
        } catch (\Exception $e) {
            // phpcs:disable Magento2.Security.LanguageConstruct.DirectOutput
            throw new CouldNotSaveException(__('Can update seller Product. Error: %1', $e->getMessage()));
        }
    }
}
