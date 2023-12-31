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


namespace Lofmp\Rma\Model\ResourceModel\Item;

/**
 * @method \Lofmp\Rma\Model\Item getFirstItem()
 * @method \Lofmp\Rma\Model\Item getLastItem()
 * @method \Lofmp\Rma\Model\ResourceModel\Item\Collection|\Lofmp\Rma\Model\Item[] addFieldToFilter
 * @method \Lofmp\Rma\Model\ResourceModel\Item\Collection|\Lofmp\Rma\Model\Item[] setOrder
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->entityFactory = $entityFactory;
        $this->logger = $logger;
        $this->fetchStrategy = $fetchStrategy;
        $this->eventManager = $eventManager;
        $this->storeManager = $storeManager;
         $this->_localeDate = $localeDate;
        $this->connection = $connection;
        $this->resource = $resource;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('Lofmp\Rma\Model\Item', 'Lofmp\Rma\Model\ResourceModel\Item');
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray($emptyOption = false)
    {
        $arr = [];
        if ($emptyOption) {
            $arr[0] = ['value' => 0, 'label' => __('-- Please Select --')];
        }
        /** @var \Lofmp\Rma\Model\Item $item */
        foreach ($this as $item) {
            $arr[] = ['value' => $item->getId(), 'label' => $item->getName()];
        }

        return $arr;
    }

    /**
     * @param string|false $emptyOption
     *
     * @return array
     */
    public function getOptionArray($emptyOption = false)
    {
        $arr = [];
        if ($emptyOption) {
            $arr[0] = __('-- Please Select --');
        }
        /** @var \Lofmp\Rma\Model\Item $item */
        foreach ($this as $item) {
            $arr[$item->getId()] = $item->getName();
        }

        return $arr;
    }
    public function setDateColumnFilter($column_name = '')
    {
        if ($column_name) {
            $this->_date_column_filter = $column_name;
        }
        return $this;
    }

    public function getDateColumnFilter()
    {
        return $this->_date_column_filter;
    }
    /**
     * Set status filter
     *
     * @param string $orderStatus
     * @return Mage_Sales_Model_Resource_Report_Collection_Abstract
     */
    public function addDateFromFilter($from = null)
    {
        $this->_from_date_filter = $from;
        return $this;
    }

    /**
     * Set status filter
     *
     * @param string $orderStatus
     * @return Mage_Sales_Model_Resource_Report_Collection_Abstract
     */
    public function addDateToFilter($to = null)
    {
        $this->_to_date_filter = $to;
        return $this;
    }

    public function setPeriodType($period_type = "")
    {
        $this->_period_type = $period_type;
        return $this;
    }


     /**
      * @return array
      */
    public function _getSelectedColumns()
    {
        if ($this->_period_type) {
            switch ($this->_period_type) {
                case "year":
                    $this->periodFormat = 'YEAR(main_table.'.$this->getDateColumnFilter().')';
                    break;
                case "quarter":
                    $this->periodFormat = 'CONCAT(QUARTER(main_table.'.$this->getDateColumnFilter().'),"/",YEAR(main_table.'.$this->getDateColumnFilter().'))';
                    break;
                case "week":
                    $this->periodFormat = 'CONCAT(YEAR(main_table.'.$this->getDateColumnFilter().'),"", WEEK(main_table.'.$this->getDateColumnFilter().'))';
                    break;
                case "day":
                    $this->periodFormat = 'DATE(main_table.'.$this->getDateColumnFilter().')';
                    break;
                case "hour":
                     $this->periodFormat =  "DATE_FORMAT(main_table.".$this->getDateColumnFilter().", '%H:00')";
                    break;
                case "weekday":
                    $this->periodFormat =  'WEEKDAY(main_table.'.$this->getDateColumnFilter().')';
                    break;
                case "month":
                default:
                    $this->periodFormat = 'CONCAT(MONTH(main_table.'.$this->getDateColumnFilter().'),"/",YEAR(main_table.'.$this->getDateColumnFilter().'))';
                    break;
            }
        }

        $this->selectedColumns = [
                'total_rma_cnt'     =>  'count(distinct rma_id)',
                'total_requested_cnt' => 'sum(qty_requested)',
                 'total_returned_cnt' => 'sum(qty_returned)',
                'time' => $this->periodFormat,
                'product_name' => 'product.value'
                /*'total_product_cnt' => 'SUM(rma_item.qty_requested)',*/
            ];
      /*  $statusCollection = $this->statusCollectionFactory->create()->addActiveFilter();

        foreach ($statusCollection as $status) {
            $this->selectedColumns["{$status->getId()}_cnt"] = "SUM(if (main_table.status_id = {$status->getId()}, 1, 0))";
        }*/
      /*  if ($this->reportType == 'by_product') {
            $this->selectedColumns['product_id'] = 'rma_item.product_id';
            ->where('main_table.created_at between '.$this->_from_date_filter.' AND '.$this->_to_date_filter)
        }*/

        if ($this->selectedColumns) {

            $this->getSelect()->joinLeft(
                ['product' => $this->getTable('catalog_product_entity_varchar')],
                'main_table.product_id = product.entity_id'
            )->reset(\Zend_Db_Select::COLUMNS)->columns($this->selectedColumns)->where('product.attribute_id=73')->group($this->periodFormat)->group('main_table.product_id');
        }
        // sql theo filter date
        if ($this->_to_date_filter && $this->_from_date_filter) {

            // kiem tra lai doan convert ngay thang nay !
            
            $dateStart = $this->_localeDate->convertConfigTimeToUtc($this->_from_date_filter, 'Y-m-d 00:00:00');
            $endStart = $this->_localeDate->convertConfigTimeToUtc($this->_to_date_filter, 'Y-m-d 23:59:59');
            $dateRange = ['from' => $dateStart, 'to' => $endStart , 'datetime' => true];

            $this->addFieldToFilter('main_table.'.$this->getDateColumnFilter(), $dateRange);
        }

        return $this;
    }
    
      /**
       * @return array
       */
    public function _getReasonSelectedColumns($product)
    {
        

        $this->selectedColumns = [
                'total_rma_cnt'     =>  'count(distinct rma_id)',
                'total_requested_cnt' => 'sum(qty_requested)',
                 'total_returned_cnt' => 'sum(qty_returned)',
                'reason_name' => 'reason.name'
                /*'total_product_cnt' => 'SUM(rma_item.qty_requested)',*/
            ];
        if ($product) {
            $this->selectedColumns['product_name'] = 'product.value';
        }
      /*  $statusCollection = $this->statusCollectionFactory->create()->addActiveFilter();

        foreach ($statusCollection as $status) {
            $this->selectedColumns["{$status->getId()}_cnt"] = "SUM(if (main_table.status_id = {$status->getId()}, 1, 0))";
        }*/
      /*  if ($this->reportType == 'by_product') {
            $this->selectedColumns['product_id'] = 'rma_item.product_id';
            ->where('main_table.created_at between '.$this->_from_date_filter.' AND '.$this->_to_date_filter)
        }*/

        if ($this->selectedColumns) {
            $select =  $this->getSelect();
            $select->joinLeft(
                ['product' => $this->getTable('catalog_product_entity_varchar')],
                'main_table.product_id = product.entity_id'
            )->reset(\Zend_Db_Select::COLUMNS)->columns($this->selectedColumns)->where('product.attribute_id=73')->group('main_table.reason_id');
            if ($product) {
                $select->group('main_table.product_id');
            }
        }
        // sql theo filter date
        if ($this->_to_date_filter && $this->_from_date_filter) {

            // kiem tra lai doan convert ngay thang nay !
            
            $dateStart = $this->_localeDate->convertConfigTimeToUtc($this->_from_date_filter, 'Y-m-d 00:00:00');
            $endStart = $this->_localeDate->convertConfigTimeToUtc($this->_to_date_filter, 'Y-m-d 23:59:59');
            $dateRange = ['from' => $dateStart, 'to' => $endStart , 'datetime' => true];

            $this->addFieldToFilter('main_table.'.$this->getDateColumnFilter(), $dateRange);
        }

        return $this;
    }
    /**
     *
     */
    protected function initFields()
    {
        $select = $this->getSelect();
        $select->joinLeft(
            ['reason' => $this->getTable('lofmp_rma_reason')],
            'main_table.reason_id = reason.reason_id',
            ['reason_name' => 'reason.name']
        );
        $select->joinLeft(
            ['resolution' => $this->getTable('lofmp_rma_resolution')],
            'main_table.resolution_id = resolution.resolution_id',
            ['resolution_name' => 'resolution.name']
        );
        $select->joinLeft(
            ['condition' => $this->getTable('lofmp_rma_condition')],
            'main_table.condition_id = condition.condition_id',
            ['condition_name' => 'condition.name']
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->initFields();
    }

    /**
     * {@inheritdoc}
     */
    public function joinRmaTable(){
        $select = $this->getSelect();
        $select->joinLeft(
            ['rma' => $this->getTable('lofmp_rma_rma')],
            'main_table.rma_id = rma.rma_id',
            ['customer_id' => 'rma.customer_id']
        );
        return $this;
    }

     /************************/
}
