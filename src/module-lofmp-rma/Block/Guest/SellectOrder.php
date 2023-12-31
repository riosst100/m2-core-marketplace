<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_Rma
 * @copyright  Copyright (c) 2020 Landofcoder (https://landofcoder.com.com/)
 * @license    https://landofcoder.com.com/LICENSE-1.0.html
 */

namespace Lofmp\Rma\Block\Guest;

class SellectOrder extends \Magento\Framework\View\Element\Html\Link
{
    protected $customerSession;
    protected $sortOrderBuilder;
    protected $orderCollectionFactory;
    protected $orderFactory;
    protected $marketplaceOrderFactory;
    protected $marketplaceOrderitemsFactory;
    protected $rmaHelper;
    protected $helper;
    protected $sellerFactory;
    protected $_sellers = [];
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Session\SessionManagerInterface $sessionObj,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Api\SearchCriteriaBuilder       $searchCriteriaBuilder,
        \Magento\Framework\Api\SortOrderBuilder            $sortOrderBuilder,
        \Magento\Sales\Api\OrderRepositoryInterface                   $orderRepository,
        \Lofmp\Rma\Helper\Data    $rmaHelper,
        \Lofmp\Rma\Helper\Help                                $Helper,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Lof\MarketPlace\Model\OrderFactory $marketplaceOrderFactory,
        \Lof\MarketPlace\Model\OrderitemsFactory $marketplaceOrderitemsFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        array $data = []
    ) {
        $this->sortOrderBuilder      = $sortOrderBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->orderRepository       = $orderRepository;
        $this->rmaHelper             = $rmaHelper;
        $this->helper                = $Helper;
        $this->customerSession        = $customerSession;
        $this->context                = $context;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->sessionObj = $sessionObj;
        $this->sellerFactory         = $sellerFactory;
        $this->marketplaceOrderFactory                = $marketplaceOrderFactory;
        $this->marketplaceOrderitemsFactory            = $marketplaceOrderitemsFactory;
        $this->customerSession       = $customerSession;
        $this->orderFactory = $orderFactory;
        parent::__construct($context, $data);
    }

    public function filterOrderByProductName($product_name, $allow_order_ids = [])
    {
        if ($allow_order_ids && (strlen($product_name) >=3)) {
            $collection = $this->_orderCollectionFactory->create();
            $collection->addAttributeToSelect('entity_id')
                        ->addFieldToFilter('entity_id', ['in' => $allow_order_ids]);

            $collection->getSelect()->join(
                ['order_item' => $collection->getTable('sales_order_item')],
                'order_item.order_id = main_table.entity_id',
                ['product_name' => 'order_item.name']
            )
                        ->group('main_table.entity_id');
            
            $collection->addFieldToFilter('order_item.name', ['like' => ('%'.$product_name.'%')]);
            
            if ($collection->count()) {
                $filter_order_ids = [];
                foreach ($collection as $_order) {
                    $filter_order_ids[] = $_order->getId();
                }
                return $filter_order_ids;
            }
        }
        return [];
    }

    public function getSessionOrder()
    {
        $this->sessionObj->start();
        return $this->sessionObj->getGuestOrderId();
    }

    public function getSessionEmail()
    {
        $this->sessionObj->start();
        return $this->sessionObj->getGuestEmail();
    }

    public function getOrderList()
    {
        if (!isset($this->_orderList)) {
            $customer_email = $this->getSessionEmail();
            $itemsperpage = 30;
            $currentPage = $this->getRequest()->getParam("p");
            $currentPage = $currentPage?(int)$currentPage:1;

            $order_id = $this->getRequest()->getParam('id');
            $product_name = $this->getRequest()->getParam('product');
            $order_date_from = $this->getRequest()->getParam('date_from');
            $order_date_to = $this->getRequest()->getParam('date_to');
            $sort = $this->getRequest()->getParam('sort');
            $sort_by_field = 'entity_id';
            $sort_direction = \Magento\Framework\Api\SortOrder::SORT_DESC;
            if ($sort) {
                $sort_array = explode(":", $sort);
                if (isset($sort_array[0]) && $sort_array[0]) {
                    $sort_by_field = $sort_array[0];
                }
                if (isset($sort_array[1]) && $sort_array[1]) {
                    $sort_direction = $sort_array[1];
                }
            }
            $allow_order_ids = $this->rmaHelper->getAllowOrderId();
            if ($product_name && $allow_order_ids && (strlen($product_name) >=3)) {
                $allow_order_ids = $this->filterOrderByProductName($product_name, $allow_order_ids);
            }
            if ($allow_order_ids) {
                $searchCriteria = $this->searchCriteriaBuilder
                    ->addFilter('customer_email', $customer_email)
                    ->addFilter('entity_id', $allow_order_ids, 'in');
                if ($order_id) {
                    $searchCriteria->addFilter('increment_id', $order_id);
                }
                if ($order_date_from) {
                    $order_date_from = date("Y-m-d H:i:s", strtotime($order_date_from));
                    $searchCriteria->addFilter('created_at', $order_date_from, 'gt');
                }
                if ($order_date_to) {
                    $order_date_to = date("Y-m-d H:i:s", strtotime($order_date_to));
                    $searchCriteria->addFilter('created_at', $order_date_to, 'lt');
                }

                $searchCriteria = $searchCriteria->setPageSize($itemsperpage)
                    ->setCurrentPage($currentPage)
                    ->addSortOrder($this->sortOrderBuilder->setField($sort_by_field)
                                                        ->setDirection($sort_direction)
                                                        ->create());

                $orders = $this->orderRepository->getList($searchCriteria->create());
                $toolbar = $this->getToolbarBlock();
                // set collection to toolbar and apply sort
                if ($toolbar) {
                    $toolbar->setData('_current_limit', $itemsperpage)->setCollection($orders);
                    $this->setChild('toolbar', $toolbar);
                }
                $this->_orderList = $orders->getItems();
            } else {
                $this->_orderList = [];
            }
        }
        return $this->_orderList;
    }
    /**
     * @param int $orderId
     */
    public function getSellerOrders($orderId)
    {
        $customer_id = $this->customerSession->create()->getCustomerId();
        $orders = $this->marketplaceOrderFactory->create()->getCollection()
                        ->addFieldToFilter('customer_id', (int)$customer_id)
                        ->addFieldToFilter('order_id', (int)$orderId);
                        
        return $orders;
    }
    /**
     * @param int $orderId
     */
    public function getOrderById($orderId)
    {
        $order = $this->orderFactory->create()->get('Magento\Sales\Model\Order')->load($orderId);
        return $order;
    }
    /**
     * @param int $orderId
     * @return string
     */
    public function getOrderUrl($orderId)
    {
        return  $this->context->getUrlBuilder()->getUrl('sales/order/view', ['order_id' => $orderId]);
    }
    /**
     * @param int $order_id
     * @param int $seller_id
     * @return string
     */
    public function getCreateRmaUrl($order_id, $seller_id = 0)
    {
        return $this->context->getUrlBuilder()->getUrl('returns/rma/new/', ['order_id' => $order_id, 'seller_id' => $seller_id]);
    }

    public function getIsUseBothType()
    {
        return $this->helper->getIsUseBothType();
    }

    /**
     * @return int
     */
    public function getReturnPeriod()
    {
        return $this->helper->getConfig($store = null, 'rma/policy/return_period');
    }
    public function getIsShowBundle()
    {
        return $this->helper->isShowBundleRmaFrontend();
    }
     /**
      * @return boolean
      */
    public function IsItemsQtyAvailable($order)
    {
        $items = $order->getAllItems();
        foreach ($items as $item) {
            if ($item->getData('base_row_total') <=0 || $item->getData('product_type') == 'bundle') {
                continue;
            }
            if ($this->rmaHelper->getItemQuantityAvaiable($item)>0) {
                return true;
            }
        }
        return false;
    }
   
    /**
     * Prepare layout for change buyer
     *
     * @return Object
     */
    public function _prepareLayout()
    {
        if ($this->getIsShowBundle()) {
            $this->pageConfig->getTitle()->set(__('Sellect One or Multi Orders'));
        } else {
            $this->pageConfig->getTitle()->set(__('Sellect Order'));
        }
        return parent::_prepareLayout();
    }

    public function _toHtml()
    {
        $template = $this->getTemplate();
        if ($this->getIsShowBundle()) {
            $template = "Lofmp_Rma::guest/select_multi_orders.phtml";
            $this->setTemplate($template);
        }
        return parent::_toHtml();
    }

    public function getToolbarBlock()
    {
        $block = $this->getLayout()->getBlock('rma_toolbar');
        if ($block) {
            return $block;
        }
    }

    /**
     * Get Item Info
     * @param int $order_id
     * @param int $seller_id
     * @return string
     */
    public function getItemInfo($order_id, $seller_id)
    {
        $items ='';
        $orderitems = $this->marketplaceOrderitemsFactory->create()->getCollection()->addFieldToFilter('seller_id', (int)$seller_id)->addFieldToFilter('order_id', $order_id);
        foreach ($orderitems as $orderitem) {
            $items .= $orderitem->getProductName().__('(qty : %1)', (int)$orderitem['product_qty']).', ';
        }
        return $items;
    }
     
    /**
     * @param int $seller_id
     * @return \Lof\MarketPlace\Model\Seller | null
     */
    public function getSeller($seller_id)
    {
        if (!isset($this->_sellers[$seller_id])) {
            $this->_sellers[$seller_id] = $this->sellerFactory->create()->load($seller_id);
        }
        return $this->_sellers[$seller_id];
    }
}
