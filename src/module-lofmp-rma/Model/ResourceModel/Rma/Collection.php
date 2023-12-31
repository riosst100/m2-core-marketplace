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



namespace Lofmp\Rma\Model\ResourceModel\Rma;

/**
 * @method \Lofmp\Rma\Model\Rma getFirstItem()
 * @method \Lofmp\Rma\Model\Rma getLastItem()
 * @method \Lofmp\Rma\Model\ResourceModel\Rma\Collection|\Lofmp\Rma\Model\Rma[] addFieldToFilter
 * @method \Lofmp\Rma\Model\ResourceModel\Rma\Collection|\Lofmp\Rma\Model\Rma[] setOrder
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

     protected $_date_column_filter = "main_table.created_at";
     protected $_period_type = "";
     
    /**
     * {@inheritdoc}
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Lofmp\Rma\Model\ResourceModel\Status\CollectionFactory $statusCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->entityFactory = $entityFactory;
        $this->logger = $logger;
        $this->fetchStrategy = $fetchStrategy;
        $this->eventManager = $eventManager;
        $this->statusCollectionFactory = $statusCollectionFactory;
        $this->storeManager = $storeManager;
         $this->_localeDate = $localeDate;
        $this->connection = $connection;
        $this->resource = $resource;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }


    protected function _construct()
    {
        $this->_init('Lofmp\Rma\Model\Rma', 'Lofmp\Rma\Model\ResourceModel\Rma');
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
     * Set status filter
     *
     * @param string $orderStatus
     * @return Mage_Sales_Model_Resource_Report_Collection_Abstract
     */
    public function addProductIdFilter($product_id = 0)
    {
        $this->_product_id_filter = $product_id;
        return $this;
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
        /** @var \Lofmp\Rma\Model\Rma $item */
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
        /** @var \Lofmp\Rma\Model\Rma $item */
        foreach ($this as $item) {
            $arr[$item->getId()] = $item->getName();
        }

        return $arr;
    }

    /**
     * @param string $storeId
     *
     * @return $this
     */
    public function addStoreIdFilter($storeId)
    {
        $this->getSelect()
            ->where("EXISTS (SELECT * FROM `{$this->getTable('lofmp_rma_rma_store')}`
                AS `rma_store_table`
                WHERE main_table.rma_id = rma_store_table.rs_rma_id
                AND rma_store_table.rs_store_id in (?))", [0, $storeId]);

        return $this;
    }

    /**
     * @param string $exchangeOrderId
     *
     * @return $this
     */
    public function addExchangeOrderFilter($exchangeOrderId)
    {
        $this->getSelect()
            ->where("EXISTS (SELECT * FROM `{$this->getTable('lofmp_rma_rma_order')}`
                AS `rma_order_table`
                WHERE main_table.rma_id = rma_order_table.re_rma_id
                AND rma_order_table.re_exchange_order_id in (?))", [-1, $exchangeOrderId]);

        return $this;
    }

    /**
     * @param string $creditMemoId
     *
     * @return $this
     */
    public function addCreditMemoFilter($creditMemoId)
    {
        $this->getSelect()
            ->where("EXISTS (SELECT * FROM `{$this->getTable('lofmp_rma_rma_creditmemo')}`
                AS `rma_creditmemo_table`
                WHERE main_table.rma_id = rma_creditmemo_table.rc_rma_id
                AND rma_creditmemo_table.rc_credit_memo_id in (?))", [-1, $creditMemoId]);

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
                'total_rma_cnt' => 'COUNT(*)',
                'time' => $this->periodFormat,
                
                /*'total_product_cnt' => 'SUM(rma_item.qty_requested)',*/
            ];
        $statusCollection = $this->statusCollectionFactory->create()->addActiveFilter();
        
        foreach ($statusCollection as $status) {
            $this->selectedColumns["{$status->getId()}_cnt"] = "SUM(if (main_table.status_id = {$status->getId()}, 1, 0))";
        }
      /*  if ($this->reportType == 'by_product') {
            $this->selectedColumns['product_id'] = 'rma_item.product_id';
            ->where('main_table.created_at between '.$this->_from_date_filter.' AND '.$this->_to_date_filter)
        }*/

        if ($this->selectedColumns) {
            $this->getSelect()->reset(\Zend_Db_Select::COLUMNS)->columns($this->selectedColumns)->group($this->periodFormat);
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
        /* @noinspection PhpUnusedLocalVariableInspection */
        $select = $this->getSelect();
        $select->joinLeft(
            ['order' => $this->getTable('sales_order')],
            'main_table.order_id = order.entity_id',
            ['order_increment_id' => 'order.increment_id']
        );
        $select->joinLeft(
            ['customer' => $this->getTable('customer_entity')],
            'main_table.customer_id = customer.entity_id',
            ['customer_firstname' => 'customer.firstname',
            'customer_lastname' => 'customer.lastname',
            'customer_email' => 'customer.email'
            ]
        );
        $select->joinLeft(
            ['status' => $this->getTable('lofmp_rma_status')],
            'main_table.status_id = status.status_id',
            ['status_name' => 'status.name']
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

     /************************/
}
