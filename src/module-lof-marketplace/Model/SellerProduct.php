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

use Lof\MarketPlace\Api\Data\SellerProductInterface;
use Lof\MarketPlace\Api\Data\ProductInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SellerProduct extends AbstractModel implements SellerProductInterface, ProductInterface
{
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    const STATUS_PENDING = 2;

    /** Seller product approval status */
    const STATUS_NOT_SUBMITED = 0;
    const STATUS_WAITING = 1;
    const STATUS_APPROVED = 2;
    const STATUS_UNAPPROVED = 3;

    /**
     * @var string
     */
    protected $_eventPrefix = 'lof_marketplace_seller_product';

    /**
     * Name of the event object
     *
     * @var string
     */
    protected $_eventObject = 'seller_product';

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Lof\MarketPlace\Model\ResourceModel\SellerProduct::class);
    }

    /**
     * Prepare page's statuses.
     * Available event cms_page_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [
            self::STATUS_NOT_SUBMITED => __('Not Submited'),
            self::STATUS_WAITING => __('Pending Review'),
            self::STATUS_APPROVED => __('Approved'),
            self::STATUS_UNAPPROVED => __('Unapproved'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getSellerProduct()
    {
        return $this->getData(self::SELLER_PRODUCT);
    }

    /**
     * @inheritdoc
     */
    public function setSellerProduct($seller_product)
    {
        return $this->setData(self::SELLER_PRODUCT, $seller_product);
    }

    /**
     * @inheritdoc
     */
    public function getEntityId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setEntityId($entity_id)
    {
        return $this->setData(self::ENTITY_ID, $entity_id);
    }

    /**
     * @inheritdoc
     */
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * @inheritdoc
     */
    public function setProductId($product_id)
    {
        return $this->setData(self::PRODUCT_ID, $product_id);
    }

    /**
     * @inheritdoc
     */
    public function getSellerId()
    {
        return $this->getData(self::SELLER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setSellerId($seller_id)
    {
        return $this->setData(self::SELLER_ID, $seller_id);
    }

    /**
     * @inheritdoc
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setStoreId($store_id)
    {
        return $this->setData(self::STORE_ID, $store_id);
    }

    /**
     * @inheritdoc
     */
    public function getProductName()
    {
        return $this->getData(self::PRODUCT_NAME);
    }

    /**
     * @inheritdoc
     */
    public function setProductName($product_name)
    {
        return $this->setData(self::PRODUCT_NAME, $product_name);
    }

    /**
     * @inheritdoc
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritdoc
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setCreatedAt($created_at)
    {
        return $this->setData(self::CREATED_AT, $created_at);
    }

    /**
     * @inheritdoc
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setUpdatedAt($updated_at)
    {
        return $this->setData(self::UPDATED_AT, $updated_at);
    }

    /**
     * @inheritdoc
     */
    public function getCommission()
    {
        return $this->getData(self::COMMISSION);
    }

    /**
     * @inheritdoc
     */
    public function setCommission($commission)
    {
        return $this->setData(self::COMMISSION, $commission);
    }

    /**
     * @inheritdoc
     */
    public function getAdminassign()
    {
        return $this->getData(self::ADDMINASSIGN);
    }

    /**
     * @inheritdoc
     */
    public function setAdminassign($adminassign)
    {
        return $this->setData(self::ADDMINASSIGN, $adminassign);
    }

    /**
     * @inheritDoc
     */
    public function getQty()
    {
        return $this->getData(self::QTY);
    }

    /**
     * @inheritDoc
     */
    public function setQty($qty)
    {
        return $this->setData(self::QTY, $qty);
    }

    /**
     * @inheritDoc
     */
    public function getTypeId()
    {
        return $this->getData(self::TYPE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setTypeId($type_id)
    {
        return $this->setData(self::TYPE_ID, $type_id);
    }

    /**
     * @inheritDoc
     */
    public function getSku()
    {
        return $this->getData(self::SKU);
    }

    /**
     * @inheritDoc
     */
    public function setSku($sku)
    {
        return $this->setData(self::SKU, $sku);
    }

    /**
     * @inheritDoc
     */
    public function getHasOptions()
    {
        return $this->getData(self::HAS_OPTIONS);
    }

    /**
     * @inheritDoc
     */
    public function setHasOptions($has_options)
    {
        return $this->setData(self::HAS_OPTIONS, $has_options);
    }

    /**
     * @inheritDoc
     */
    public function getRequiredOptions()
    {
        return $this->getData(self::REQUIRED_OPTIONS);
    }

    /**
     * @inheritDoc
     */
    public function setRequiredOptions($required_options)
    {
        return $this->setData(self::REQUIRED_OPTIONS, $required_options);
    }

    /**
     * @inheritDoc
     */
    public function getApproval()
    {
        return $this->getData(self::APPROVAL);
    }

    /**
     * @inheritDoc
     */
    public function setApproval($approval)
    {
        return $this->setData(self::APPROVAL, $approval);
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @inheritDoc
     */
    public function setPrice($price = null)
    {
        return $this->setData(self::PRICE, $price);
    }

    /**
     * @inheritDoc
     */
    public function getPrice()
    {
        return $this->getData(self::PRICE);
    }

    /**
     * @inheritDoc
     */
    public function setAttributeSetId($attribute_set_id)
    {
        return $this->setData(self::ATTRIBUTE_SET_ID, $attribute_set_id);
    }

    /**
     * @inheritDoc
     */
    public function getAttributeSetId()
    {
        return $this->getData(self::ATTRIBUTE_SET_ID);
    }

    /**
     * @inheritDoc
     */
    public function getIsInStock()
    {
        return $this->getData(self::IS_IN_STOCK);
    }

    /**
     * @inheritDoc
     */
    public function setIsInStock($is_in_stock = 1)
    {
        return $this->setData(self::IS_IN_STOCK, $is_in_stock);
    }
}
