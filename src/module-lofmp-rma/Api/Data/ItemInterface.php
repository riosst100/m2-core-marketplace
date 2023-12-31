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

namespace Lofmp\Rma\Api\Data;

interface ItemInterface extends DataInterface
{
    const KEY_ID = 'item_id';
    const KEY_RMA_ID = 'rma_id';
    const KEY_ORDER_ITEM_ID = 'order_item_id';
    const KEY_PRODUCT_ID = 'product_id';
    const KEY_ORDER_ID = 'order_id';
    const KEY_REASON_ID = 'reason_id';
    const KEY_RESOLUTION_ID = 'resolution_id';
    const KEY_CONDITION_ID = 'condition_id';
    const KEY_QTY_REQUESTED = 'qty_requested';
    const KEY_QTY_RETURNED = 'qty_returned';
    const KEY_CREATED_AT = 'created_at';
    const KEY_UPDATED_AT = 'updated_at';
    const KEY_NAME = 'name';
    const KEY_SELLER_COMMISSION = 'seller_commission';
    const KEY_ADMIN_COMMISSION = 'admin_commission';

    /**
     * @return int
     */
    public function getItemId();

    /**
     * @param int $itemId
     * @return $this
     */
    public function setItemId($itemId);

    /**
     * @return int
     */
    public function getRmaId();

    /**
     * @param int $rmaId
     * @return $this
     */
    public function setRmaId($rmaId);

    /**
     * @return int
     */
    public function getOrderItemId();

    /**
     * @param int $orderItemId
     * @return $this
     */
    public function setOrderItemId($orderItemId);

    /**
     * @return int
     */
    public function getProductId();

    /**
     * @param int $id
     * @return $this
     */
    public function setProductId($id);

    /**
     * @return int
     */
    public function getOrderId();

    /**
     * @param int $orderId
     * @return $this
     */
    public function setOrderId($orderId);

    /**
     * @return int
     */
    public function getReasonId();

    /**
     * @param int $reasonId
     * @return $this
     */
    public function setReasonId($reasonId);

    /**
     * @return int
     */
    public function getResolutionId();

    /**
     * @param int $resolutionId
     * @return $this
     */
    public function setResolutionId($resolutionId);

    /**
     * @return int
     */
    public function getConditionId();

    /**
     * @param int $conditionId
     * @return $this
     */
    public function setConditionId($conditionId);

    /**
     * @return int
     */
    public function getQtyRequested();

    /**
     * @param int $qtyRequested
     * @return $this
     */
    public function setQtyRequested($qtyRequested);

    /**
     * @return int
     */
    public function getQtyReturned();

    /**
     * @param int $qtyReturned
     * @return $this
     */
    public function setQtyReturned($qtyReturned);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * @return string
     */
    public function getUpdatedAt();

    /**
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getSellerCommission();

    /**
     * @param string
     * @return $this
     */
    public function setSellerCommission($sellerCommission);

    /**
     * @return string
     */
    public function getAdminCommission();

    /**
     * @param string
     * @return $this
     */
    public function setAdminCommission($adminCommission);
}
