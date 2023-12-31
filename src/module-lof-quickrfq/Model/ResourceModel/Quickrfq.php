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
 * @package    Lof_Quickrfq
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
namespace Lof\Quickrfq\Model\ResourceModel;

/**
 * Class Quickrfq
 * @package Lof\Quickrfq\Model\ResourceModel
 */
class Quickrfq extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{


    /**
     *
     */
    protected function _construct()
    {
        $this->_init('lof_quickrfq', 'quickrfq_id');
    }

    /**
     * Save Approve Cart
     * @param string|int $quickrfqId
     * @param string|int $cartId
     * @param string|null $expiry
     * @return bool|string|int|mixed|null
     */
    public function saveApproveCart($quickrfqId, $cartId, $expiry = null)
    {
        if ($cartId) {
            if (!$this->checkCartIsExists($quickrfqId, $cartId)) {
                $table = $this->getTable('lof_quickrfq_cart');
                $data = [
                    'quickrfq_id' => $quickrfqId,
                    'cart_id' => $cartId,
                    'status' => 0,
                    'expiry' => $expiry
                ];
                $this->getConnection()->insert($table, $data);
                return true;
            }
        }
        return false;
    }

    /**
     * Save Approve Cart
     * @param string|int $quickrfqId
     * @param string|int $cartId
     * @param string|null $status
     * @return bool|string|int|mixed|null
     */
    public function updateApproveCartStatus($quickrfqId, $cartId, $status = 0)
    {
        if ($cartId) {
            $table = $this->getTable('lof_quickrfq_cart');
            $this->getConnection()->update(
                $table,
                [
                    "status" => (int)$status
                ],
                [
                    'quickrfq_id = ?' => (int)$quickrfqId,
                    'cart_id = ?' => (int)$cartId
                ]
            );
            return true;
        }
        return false;
    }

    /**
     * @param string|int $quickrfqId
     * @param string|int $cartId
     * @return bool|null
     */
    public function checkCartIsExists($quickrfqId, $cartId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('lof_quickrfq_cart'),
            'entity_id'
        )->where(
            'quickrfq_id = :quickrfq_id'
        )->where(
            'cart_id = :cart_id'
        );
        $binds = [
            ':quickrfq_id' => (int)$quickrfqId,
            ':cart_id' => (int)$cartId,
        ];
        return $connection->fetchCol($select, $binds)?true:false;
    }
}
