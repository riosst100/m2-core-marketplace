<?php
/**
 * Created by PhpStorm.
 * User: sairam
 * Date: 27/9/18
 * Time: 3:43 PM
 */

namespace Lofmp\DeliverySlot\Plugin\Sales\Order;

/**
 * Class Grid
 * @package Lofmp\DeliverySlot\Plugin\Sales\Order
 */
class Grid
{
    public static $table = 'sales_order_grid';
    public static $leftJoinTable = 'sales_order';

    public function afterSearch($intercepter, $collection)
    {
        if ($collection->getMainTable() === $collection->getConnection()->getTableName(self::$table)) {
            $leftJoinTableName = $collection->getConnection()->getTableName(self::$leftJoinTable);

            $collection
                ->getSelect()
                ->joinLeft(
                    ['sorder' => $leftJoinTableName],
                    "sorder.entity_id = main_table.entity_id",
                    [
                        'delivery_slot' => 'sorder.delivery_time_slot',
                        'delivery_date' => 'sorder.delivery_date'
                    ]
                );

            $where = $collection->getSelect()->getPart(\Magento\Framework\DB\Select::WHERE);

            $collection->getSelect()->setPart(\Magento\Framework\DB\Select::WHERE, $where);

            //echo $collection->getSelect()->__toString();die;
        }
        return $collection;
    }
}
