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

namespace Lof\MarketPlace\Model\ResourceModel\SellerInvoice\Grid;

use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Psr\Log\LoggerInterface as Logger;

class Collection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    /**
     * Collection constructor.
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager
    ) {
        $mainTable = 'lof_marketplace_sellerinvoice';
        $resourceModel = \Magento\Sales\Model\ResourceModel\Order\Invoice::class;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        parent::_construct();
        $fields = [
            'status',
            'grand_total',
            'base_grand_total',
            'seller_id'
        ];
        foreach ($fields as $field) {
            $this->addFilterToMap(
                $field,
                'main_table.' . $field
            );
        }
    }

    /**
     * Init collection select
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->join(
            [
                'invoice_grid' => $this->getTable('sales_invoice_grid')
            ],
            'main_table.invoice_id=invoice_grid.entity_id',
            [
                'entity_id',
                'increment_id',
                'order_increment_id',
                'order_id',
                'store_id',
                'customer_name',
                'billing_name',
                'billing_address',
                'shipping_address',
                'store_currency_code',
                'order_currency_code',
                'base_currency_code',
                'global_currency_code',
                // 'base_grand_total',
                // 'grand_total',
                'created_at',
                'state'
            ]
        );

        return $this;
    }
}
