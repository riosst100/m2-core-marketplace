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
 * @package    Lofmp_SellerRule
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerRule\Helper;

use Lof\MarketPlace\Model\ResourceModel\Order\CollectionFactory as MarketPlaceOrderCollectionFactory;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ResourceConnection;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var MarketPlaceOrderCollectionFactory
     */
    protected $_marketOrderCollectionFactory;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * Data constructor.
     * @param Context $context
     * @param ResourceConnection $resource
     * @param MarketPlaceOrderCollectionFactory $marketOrderCollectionFactory
     */
    public function __construct(
        Context $context,
        ResourceConnection $resource,
        MarketPlaceOrderCollectionFactory $marketOrderCollectionFactory
    ) {
        parent::__construct($context);
        $this->resource = $resource;
        $this->_marketOrderCollectionFactory = $marketOrderCollectionFactory;
    }

    /**
     * @param $sellerId
     * @return int|string
     */
    public function getToldAmountSold($sellerId)
    {
        $collection = $this->getMarketPlaceOrderCollection();

        $connection = $this->resource->getConnection();

        $select = $connection->select()
            ->from(
                ['main_table' => $collection->getMainTable()],
                ['seller_amount' => new \Zend_Db_Expr('SUM(seller_amount)')]
            )
            ->where('main_table.seller_id = ?', $sellerId)
            ->where('main_table.status = ?', \Magento\Sales\Model\Order::STATE_COMPLETE);

        $sum = $this->resource->getConnection()->fetchOne($select);
        return $sum ?: 0;
    }

    /**
     * @param $sellerId
     * @return int
     */
    public function getTotalCompletedOrder($sellerId)
    {
        $total = $this->getMarketPlaceOrderCollection()
            ->addFieldToFilter('seller_id', $sellerId)
            ->addFieldToFilter('status', \Magento\Sales\Model\Order::STATE_COMPLETE)
            ->getSize();

        return $total ?: 0;
    }

    /**
     * @return \Lof\MarketPlace\Model\ResourceModel\Order\Collection
     */
    public function getMarketPlaceOrderCollection()
    {
        return $this->_marketOrderCollectionFactory->create();
    }
}
