<?php
/**
 * LandOfCoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   LandOfCoder
 * @package    Lofmp_Rma
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\Rma\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Lofmp\Rma\Api\Data\ItemInterface;
use Symfony\Component\Config\Definition\Exception\Exception;

class Item extends \Magento\Framework\Model\AbstractModel implements
    IdentityInterface,
    \Lofmp\Rma\Api\Data\ItemInterface
{
    const CACHE_TAG = 'rma_item';

    /**
     * {@inheritdoc}
     */
    protected $_cacheTag = 'rma_item';

    /**
     * {@inheritdoc}
     */
    protected $_eventPrefix = 'rma_item';

    /**
     * {@inheritdoc}
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG.'_'.$this->getId()];
    }

    /**
     * {@inheritdoc}
     */
    public function getItemId()
    {
        return $this->getData(self::KEY_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setItemId($itemId)
    {
        return $this->setData(self::KEY_ID, $itemId);
    }

    /**
     * {@inheritdoc}
     */
    public function getRmaId()
    {
        return $this->getData(self::KEY_RMA_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setRmaId($rmaId)
    {
        return $this->setData(self::KEY_RMA_ID, $rmaId);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductId()
    {
        return $this->getData(self::KEY_PRODUCT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setProductId($id)
    {
        return $this->setData(self::KEY_PRODUCT_ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderId()
    {
        return $this->getData(self::KEY_ORDER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::KEY_ORDER_ID, $orderId);
    }

    /**
     * {@inheritdoc}
     */
    public function getReasonId()
    {
        return $this->getData(self::KEY_REASON_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setReasonId($reasonId)
    {
        return $this->setData(self::KEY_REASON_ID, $reasonId);
    }

    /**
     * {@inheritdoc}
     */
    public function getResolutionId()
    {
        return $this->getData(self::KEY_RESOLUTION_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setResolutionId($resolutionId)
    {
        return $this->setData(self::KEY_RESOLUTION_ID, $resolutionId);
    }

    /**
     * {@inheritdoc}
     */
    public function getConditionId()
    {
        return $this->getData(self::KEY_CONDITION_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setConditionId($conditionId)
    {
        return $this->setData(self::KEY_CONDITION_ID, $conditionId);
    }

    /**
     * {@inheritdoc}
     */
    public function getQtyRequested()
    {
        return $this->getData(self::KEY_QTY_REQUESTED);
    }

    /**
     * {@inheritdoc}
     */
    public function setQtyRequested($qtyRequested)
    {
        return $this->setData(self::KEY_QTY_REQUESTED, $qtyRequested);
    }


    /**
     * {@inheritdoc}
     */
    public function getQtyReturned()
    {
        return $this->getData(self::KEY_QTY_RETURNED);
    }

    /**
     * {@inheritdoc}
     */
    public function setQtyReturned($qtyReturned)
    {
        return $this->setData(self::KEY_QTY_RETURNED, $qtyReturned);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->getData(self::KEY_CREATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::KEY_CREATED_AT, $createdAt);
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::KEY_UPDATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::KEY_UPDATED_AT, $updatedAt);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getData(self::KEY_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        return $this->setData(self::KEY_NAME, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function setSellerCommission($sellerCommission)
    {
        return $this->setData(self::KEY_SELLER_COMMISSION, $sellerCommission);
    }

     /**
      * {@inheritdoc}
      */
    public function getSellerCommission()
    {
        return $this->getData(self::KEY_SELLER_COMMISSION);
    }

    /**
     * {@inheritdoc}
     */
    public function setAdminCommission($adminCommission)
    {
        return $this->setData(self::KEY_ADMIN_COMMISSION, $adminCommission);
    }

     /**
      * {@inheritdoc}
      */
    public function getAdminCommission()
    {
        return $this->getData(self::KEY_ADMIN_COMMISSION);
    }


    /**
     * {@inheritdoc}
     */
    public function getOrderItemId()
    {
        return $this->getData(self::KEY_ORDER_ITEM_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderItemId($orderItemId)
    {
        return $this->setData(self::KEY_ORDER_ITEM_ID, $orderItemId);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('Lofmp\Rma\Model\ResourceModel\Item');
    }
}
