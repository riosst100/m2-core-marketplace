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
 * @package    Lofmp_FeaturedProducts
 * @copyright  Copyright (c) 2018 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\FeaturedProducts\Model;

use Lofmp\FeaturedProducts\Api\Data\FeaturedProductInterface;
use Magento\Framework\Model\AbstractModel;

class FeaturedProduct extends AbstractModel implements FeaturedProductInterface
{
    const CACHE_TAG = 'lofmp_featuredproducts_product';

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
        $this->_init('Lofmp\FeaturedProducts\Model\ResourceModel\FeaturedProduct');
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
    public function getFeaturedFrom()
    {
        return $this->getData(self::FEATURED_FROM);
    }

    /**
     * @inheritDoc
     */
    public function setFeaturedFrom($featuredFrom)
    {
        return $this->setData(self::FEATURED_FROM, $featuredFrom);
    }

    /**
     * @inheritDoc
     */
    public function getFeaturedTo()
    {
        return $this->getData(self::FEATURED_TO);
    }

    /**
     * @inheritDoc
     */
    public function setFeaturedTo($featuredTo)
    {
        return $this->setData(self::FEATURED_TO, $featuredTo);
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
}
