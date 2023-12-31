<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_TimeDiscount
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */


namespace Lofmp\TimeDiscount\Model;

use Magento\Framework\ObjectManagerInterface;
use Lofmp\TimeDiscount\Api\Data\ProductInterface;
use Magento\Framework\Model\AbstractModel;


class Product extends AbstractModel implements ProductInterface
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    public function __construct(
    	\Magento\Framework\Model\Context $context,
    	\Magento\Framework\Registry $registry
    ) {
    	parent::__construct($context,$registry);
    }
    protected function _construct()
    {

        $this->_init('Lofmp\TimeDiscount\Model\ResourceModel\Product');
    }

    /**
     * @inheritDoc
     */
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * @inheritDoc
     */
    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * @inheritDoc
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * @inheritDoc
     */
    public function getSellerId()
    {
        return $this->getData(self::SELLER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setSellerId($sellerId)
    {
        return $this->setData(self::SELLER_ID, $sellerId);
    }

    /**
     * @inheritDoc
     */
    public function getTimeProductData()
    {
        return $this->getData(self::TIME_PRODUCT_DATA);
    }

    /**
     * @inheritDoc
     */
    public function setTimeProductData($data)
    {
        return $this->setData(self::TIME_PRODUCT_DATA, $data);
    }

    /**
     * @inheritDoc
     */
    public function getTimeDiscountParsed()
    {
        return $this->getData(self::TIME_DISCOUNT_PARSED);
    }

    /**
     * @inheritDoc
     */
    public function setTimeDiscountParsed($timeDiscountParsed)
    {
        return $this->setData(self::TIME_DISCOUNT_PARSED, $timeDiscountParsed);
    }

    /**
     * @inheritDoc
     */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }

    /**
     * @inheritDoc
     */
    public function setSortOrder($sortOrder)
    {
        return $this->setData(self::SORT_ORDER, $sortOrder);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @inheritDoc
     */
    public function getTimeDiscount()
    {
        return $this->getData(self::TIME_DISCOUNT);
    }

    /**
     * @inheritDoc
     */
    public function setTimeDiscount($time_discount)
    {
        return $this->setData(self::TIME_DISCOUNT, $time_discount);
    }
}
