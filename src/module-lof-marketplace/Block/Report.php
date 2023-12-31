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

namespace Lof\MarketPlace\Block;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class Report extends \Magento\Framework\View\Element\Html\Link
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Lof\MarketPlace\Model\Seller
     */
    protected $_sellerFactory;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Lof\MarketPlace\Model\Amount
     */
    protected $amount;

    /**
     * @var \Lof\MarketPlace\Model\Amounttransaction
     */
    protected $amounttransaction;

    /**
     * @var \Lof\MarketPlace\Model\Orderitems
     */
    protected $orderitems;

    /**
     * @var \Lof\MarketPlace\Model\Order
     */
    protected $order;

    /**
     * @var
     */
    protected $_order;

    /**
     * @var \Lof\MarketPlace\Model\SellerProduct
     */
    protected $product;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_timezoneInterface;

    /**
     * @var \Magento\Framework\Locale\ListsInterface
     */
    protected $_localList;

    /**
     * @var string
     */
    protected $_columnDate = 'main_table.created_at';

    /**
     * @var \Magento\Reports\Model\ResourceModel\Report\Collection\Factory
     */
    protected $_resourceFactory;

    /**
     * @var \Magento\Reports\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Lof\MarketPlace\Helper\Report
     */
    protected $_helperReport;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Lof\MarketPlace\Model\Seller $sellerFactory
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param \Lof\MarketPlace\Model\Amount $amount
     * @param \Lof\MarketPlace\Model\Amounttransaction $amounttransaction
     * @param \Lof\MarketPlace\Model\Orderitems $orderitems
     * @param \Lof\MarketPlace\Model\Order $order
     * @param \Magento\Sales\Model\Order $_order
     * @param \Lof\MarketPlace\Model\SellerProduct $product
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface
     * @param \Magento\Framework\Locale\ListsInterface $localeLists
     * @param \Magento\Reports\Model\ResourceModel\Report\Collection\Factory $resourceFactory
     * @param \Magento\Reports\Model\ResourceModel\Product\CollectionFactory $collectionFactory
     * @param \Lof\MarketPlace\Helper\Report $helperReport
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Lof\MarketPlace\Model\Seller $sellerFactory,
        \Lof\MarketPlace\Helper\Data $helper,
        \Lof\MarketPlace\Model\Amount $amount,
        \Lof\MarketPlace\Model\Amounttransaction $amounttransaction,
        \Lof\MarketPlace\Model\Orderitems $orderitems,
        \Lof\MarketPlace\Model\Order $order,
        \Magento\Sales\Model\Order $_order,
        \Lof\MarketPlace\Model\SellerProduct $product,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
        \Magento\Framework\Locale\ListsInterface $localeLists,
        \Magento\Reports\Model\ResourceModel\Report\Collection\Factory $resourceFactory,
        \Magento\Reports\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        \Lof\MarketPlace\Helper\Report $helperReport,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_resourceFactory = $resourceFactory;
        $this->_order = $_order;
        $this->amounttransaction = $amounttransaction;
        $this->product = $product;
        $this->orderitems = $orderitems;
        $this->order = $order;
        $this->session = $customerSession;
        $this->amount = $amount;
        $this->_helper = $helper;
        $this->_coreRegistry = $registry;
        $this->_sellerFactory = $sellerFactory;
        $this->_resource = $resource;
        $this->_date = $date;
        $this->_timezoneInterface = $timezoneInterface;
        $this->_localList = $localeLists;
        $this->_helperReport = $helperReport;
        parent::__construct($context);
    }

    /**
     * @param $dateTime
     * @return string
     */
    public function getTimezoneDateTime($dateTime = "today")
    {
        if ($dateTime === "today" || !$dateTime) {
            $dateTime = $this->_date->gmtDate();
        }
        $today = $this->_timezoneInterface->date(new \DateTime($dateTime))->format('Y-m-d H:i:s');

        return $today;
    }

    /**
     * @return string
     */
    public function getResourceCollectionName()
    {
        return \Lof\MarketPlace\Model\ResourceModel\Sales\Collection::class;
    }

    /**
     * @param $country_code
     * @return string
     */
    public function getCountry($country_code)
    {
        $country_name = $this->_localList->getCountryTranslation($country_code);
        return ($country_name ? $country_name : $country_code);
    }

    /**
     * @return array
     */
    public function getDataCountry()
    {
        $data = [];
        $data['country'] = $data['amount'] = 0;
        $country = $this->getTopCountries();
        foreach ($country as $_country) {
            $data['country'] = $data['country'] + 1;
            $data['amount'] = $data['amount'] + $_country['seller_amount'];
        }

        return $data;
    }

    /**
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSellerCollection()
    {
        $sellerCollection = $this->_sellerFactory->getCollection();
        return $sellerCollection;
    }

    /**
     * @return mixed|string
     */
    public function getSellerId()
    {
        $seller_id = '';
        $seller = $this->_sellerFactory->getCollection()
            ->addFieldToFilter('customer_id', $this->session->getId())
            ->getData();
        foreach ($seller as $_seller) {
            $seller_id = $_seller['seller_id'];
        }

        return $seller_id;
    }

    /**
     * @return Report
     */
    public function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Advanced Report'));
        return parent::_prepareLayout();
    }

    /**
     * @return int|string
     */
    public function getCreditAmount()
    {
        $credit = 0;
        $amount = $this->amount->getCollection()->addFieldToFilter('seller_id', $this->getSellerId());
        foreach ($amount as $_amount) {
            $credit = $this->_helper->getPriceFomat($_amount->getAmount());
        }

        return $credit;
    }

    /**
     * @param $price
     * @return string
     */
    public function getPriceFomat($price)
    {
        return $this->_helper->getPriceFomat($price);
    }

    /**
     * @return mixed
     */
    public function getTopCountries()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resourceCollection = $objectManager->create($this->getResourceCollectionName())
            ->prepareByCountryCollection()
            ->setMainTableId("country_id");

        $resourceCollection->applyCustomFilter();
        return $resourceCollection->getData();
    }

    /**
     * @return false|string
     */
    public function getSalesReport()
    {
        $data = [];
        $dates = [];
        $dateT = date('t');
        for ($i = 1; $i <= $dateT; $i++) {
            $dates[] = date('Y') . "-" . date('m') . "-" . str_pad($i, 2, '0', STR_PAD_LEFT);
        }

        $data[] = [];
        foreach ($dates as $key => $date) {
            $credit = $i = 0;

            $orderitems = $this->orderitems->getCollection()
                ->addFieldToFilter('seller_id', $this->getSellerId())
                ->setDateColumnFilter($this->_columnDate)
                ->addDateFromFilter($date, null)->addDateToFilter($date, null);
            $orderitems->applyCustomFilter();

            foreach ($orderitems as $_orderitems) {
                $credit = $credit + $_orderitems->getSellerCommission() - $_orderitems->getSellerCommissionRefund();
                $i = $i + $_orderitems->getQtyInvoiced() - $_orderitems->getQtyRefunded();
            }

            $data[$key]['earn'] = $credit;
            $data[$key]['sales'] = $i;
            $data[$key]['period'] = substr($date, 5);
        }

        return json_encode($data);
    }

    /**
     * @param $month
     * @return int
     */
    public function daysInMonth($month)
    {
        return $this->getDaysInMonth($month, date("Y"));
    }

    /**
     * @param $month
     * @param $year
     * @return int
     */
    public function getDaysInMonth($month, $year)
    {
        // phpcs:disable Generic.Files.LineLength.TooLong
        return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
    }

    /**
     * @return false|string
     */
    public function getSalesReportYear()
    {
        $data = [];
        $dates = [];
        $dateY = date('Y');
        for ($i = $dateY - 10; $i <= $dateY; $i++) {
            $dates[] = $i;
        }

        $data[] = [];
        foreach ($dates as $key => $date) {
            $credit = $i = 0;

            $orderitems = $this->orderitems->getCollection()
                ->addFieldToFilter('seller_id', $this->getSellerId())
                ->setDateColumnFilter($this->_columnDate)
                ->addDateFromFilter($date . "-1-1", null)
                ->addDateToFilter($date . "-12-31", null);
            $orderitems->applyCustomFilter();

            foreach ($orderitems as $_orderitems) {
                $credit = $credit + $_orderitems->getSellerCommission() - $_orderitems->getSellerCommissionRefund();
                $i = $i + $_orderitems->getQtyInvoiced() - $_orderitems->getQtyRefunded();
            }

            $data[$key]['earn'] = $credit;
            $data[$key]['sales'] = $i;
            $data[$key]['period'] = $date;
        }

        return json_encode($data);
    }

    /**
     * @return false|string
     */
    public function getSalesReportMonth()
    {
        $data = [];
        $dates = [];
        for ($i = 1; $i <= 12; $i++) {
            $dates[] = date('Y') . "-" . $i;
        }

        $data[] = [];
        foreach ($dates as $key => $date) {
            $credit = $i = 0;

            $orderitems = $this->orderitems->getCollection()
                ->addFieldToFilter('seller_id', $this->getSellerId())
                ->setDateColumnFilter($this->_columnDate)
                ->addDateFromFilter($date, null)
                ->addDateToFilter($date . "-" . $this->daysInMonth($key + 1), null);
            $orderitems->applyCustomFilter();

            foreach ($orderitems as $_orderitems) {
                $credit = $credit + $_orderitems->getSellerCommission() - $_orderitems->getSellerCommissionRefund();
                $i = $i + $_orderitems->getQtyInvoiced() - $_orderitems->getQtyRefunded();
            }

            $data[$key]['earn'] = $credit;
            $data[$key]['sales'] = $i;
            $data[$key]['period'] = $date;
        }

        return json_encode($data);
    }

    /**
     * @return string
     */
    public function getEarningsToDay()
    {
        $credit = 0;
        $amount = $this->amounttransaction->getCollection()
            ->addFieldToFilter('seller_id', $this->getSellerId())
            ->setDateColumnFilter($this->_columnDate)
            ->addDateFromFilter($this->getTimezoneDateTime(), null)
            ->addDateToFilter($this->getTimezoneDateTime(), null);
        $amount->applyCustomFilter();

        foreach ($amount as $_amount) {
            $credit = $credit + $_amount->getAmount();
        }

        return $this->_helper->getPriceFomat($credit);
    }

    /**
     * @return string
     */
    public function getEarningsToMonth()
    {
        $credit = 0;
        $date = $this->getTimezoneDateTime();
        $first_day = date('Y-m-01', strtotime($date));
        $last_day = date('Y-m-t', strtotime($date));
        $amount = $this->amounttransaction->getCollection()
            ->addFieldToFilter('seller_id', $this->getSellerId())
            ->setDateColumnFilter($this->_columnDate)
            ->addDateFromFilter($first_day, null)->addDateToFilter($last_day, null);
        $amount->applyCustomFilter();

        foreach ($amount as $_amount) {
            $credit = $credit + $_amount->getAmount();
        }

        return $this->_helper->getPriceFomat($credit);
    }

    /**
     * @return int
     */
    public function getTotalSales()
    {
        $total = 0;
        $orderitems = $this->orderitems->getCollection()
            ->addFieldToFilter('seller_id', $this->getSellerId())
            ->addFieldToFilter('status', 'complete');

        foreach ($orderitems as $_orderitems) {
            $total = $total + $_orderitems->getQtyInvoiced() - $_orderitems->getQtyRefunded();
        }

        return $total;
    }

    /**
     * @param $orderid
     * @return mixed
     */
    public function getOrder($orderid)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        return $objectManager->get(\Magento\Sales\Model\Order::class)->load($orderid, 'entity_id');
    }

    /**
     * @return int
     */
    public function getTotalSalesDay()
    {
        $total = 0;
        $orderitems = $this->orderitems->getCollection()
            ->addFieldToFilter('seller_id', $this->getSellerId())
            ->setDateColumnFilter($this->_columnDate)
            ->addDateFromFilter($this->getTimezoneDateTime(), null)
            ->addDateToFilter($this->getTimezoneDateTime(), null);

        $orderitems->applyCustomFilter();

        foreach ($orderitems as $_orderitems) {
            $total = $total + $_orderitems->getQtyInvoiced() - $_orderitems->getQtyRefunded();
        }

        return $total;
    }

    /**
     * @return int
     */
    public function getTotalSalesMonth()
    {
        $total = 0;
        $date = $this->getTimezoneDateTime();
        $first_day = date('Y-m-01', strtotime($date));
        $last_day = date('Y-m-t', strtotime($date));
        $orderitems = $this->orderitems->getCollection()
            ->addFieldToFilter('seller_id', $this->getSellerId())
            ->setDateColumnFilter($this->_columnDate)
            ->addDateFromFilter($first_day, null)
            ->addDateToFilter($last_day, null);
        $orderitems->applyCustomFilter();

        foreach ($orderitems as $_orderitems) {
            $total = $total + $_orderitems->getProductQty();
        }

        return $total;
    }

    /**
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    public function getOrderSeller()
    {
        return $this->order->getCollection()
            ->addFieldToFilter('seller_id', $this->getSellerId())
            ->setOrder('id', 'desc');
    }

    /**
     * @return \Magento\Reports\Model\ResourceModel\Product\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBestSeller()
    {
        $collection = $this->_collectionFactory->create();
        $connection = $collection->getConnection();

        $collection->addAttributeToSelect('name')
            ->addAttributeToSelect('price')
            ->addAttributeToFilter('seller_id', $this->getSellerId());
        $resource = $collection->getResource();
        $collection->joinTable(
            ['order_items' => $resource->getTable('sales_order_item')],
            'product_id = entity_id',
            ['qty_ordered' => 'SUM(order_items.qty_ordered)'],
            null,
            'left'
        );

        $orderJoinCondition = [
            'order.entity_id = order_items.order_id',
            $connection->quoteInto("order.state <> ?", \Magento\Sales\Model\Order::STATE_CANCELED),
        ];

        $collection->getSelect()
            ->joinInner(
                ['order' => $resource->getTable('sales_order')],
                implode(' AND ', $orderJoinCondition),
                []
            )->where(
                'parent_item_id IS NULL'
            )->group(
                'order_items.product_id'
            )->order(
                'qty_ordered DESC'
            );

        return $collection;
    }

    /**
     * @return \Magento\Reports\Model\ResourceModel\Product\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getMostView()
    {
        $collection = $this->_collectionFactory->create();
        $collection->addAttributeToSelect('name')
            ->addAttributeToSelect('price')
            ->addAttributeToFilter('seller_id', $this->getSellerId());
        $resource = $collection->getResource();
        $collection->joinTable(
            ['report_table_views' => $resource->getTable('report_event')],
            'object_id = entity_id',
            ['views' => 'COUNT(report_table_views.event_id)'],
            null,
            'right'
        );

        $collection->getSelect()->group(
            'e.entity_id'
        )->order(
            'views DESC'
        );

        return $collection;
    }

    /**
     * @return int
     */
    public function getTotalOrder()
    {
        return $this->_helperReport->getTotalOrders($this->getSellerId());
    }

    /**
     * @return int
     */
    public function getTotalProduct()
    {
        return $this->_helperReport->getTotalProduct($this->getSellerId());
    }
}
