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

namespace Lofmp\Rma\Block\Adminhtml\Rma\Create\Order;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * Grid constructor.
     * @param \Magento\Sales\Model\Order\Address\Renderer $addressRenderer
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Lofmp\Rma\Helper\Help $help
     * @param \Lofmp\Rma\Helper\Data $rmaHelper
     * @param \Lof\MarketPlace\Model\Order $orders
     * @param \Lof\MarketPlace\Model\SellerFactory $seller
     * @param \Lof\MarketPlace\Model\Orderitems $orderitems
     * @param array $data
     */
    public function __construct(
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Lofmp\Rma\Helper\Help $help,
        \Lofmp\Rma\Helper\Data $rmaHelper,
        \Lof\MarketPlace\Model\Order $orders,
        \Lof\MarketPlace\Model\SellerFactory $seller,
        \Lof\MarketPlace\Model\Orderitems $orderitems,
        array $data = []
    ) {
        $this->addressRenderer = $addressRenderer;
        $this->context = $context;
        $this->request = $context->getRequest();
        $this->helper = $help;
        $this->rmaHelper = $rmaHelper;
        $this->seller = $seller;
        $this->orders = $orders;
        $this->customerRepository = $customerRepository;
        $this->orderitems = $orderitems;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('rma_rma_create_order_grid');
        $this->setDefaultSort('order_id');
        $this->setDefaultDir('DESC');
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareCollection()
    {
        $allowedStatuses = $this->helper->getConfig(null, 'rma/policy/allow_in_statuses');
        $allowedStatuses = explode(',', $allowedStatuses);
        $allowedOrderId = $this->rmaHelper->getAllowOrderId();
        $collection = $this->orders->getCollection()
            ->addFieldToFilter('status', ['in' => $allowedStatuses])
            ->addFieldToFilter('order_id', ['in' => $allowedOrderId]);
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {
        $this->addColumn('increment_id', [
            'header' => __('Order #'),
            'width' => '80px',
            'type' => 'text',
            'index' => 'increment_id',
            'filter_index' => 'main_table.increment_id',
        ]);

        $this->addColumn('customer_email', [
            'header' => __('Customer email'),
            'width' => '80px',
            'type' => 'text',
            'frame_callback' => [$this, 'getCustomerEmail'],
            'filter' => false,
        ]);

        $this->addColumn('seller', [
            'header' => __('Seller '),
            'width' => '80px',
            'type' => 'text',
            'frame_callback' => [$this, 'getSellerName'],
            'filter' => false,
        ]);

        $this->addColumn('created_at', [
            'header' => __('Purchased On'),
            'frame_callback' => [$this, 'getcreatedat'],
            'filter' => false,
        ]);

        $this->addColumn('status', [
            'header' => __('Status'),
            'index' => 'status',
            'type' => 'text',
        ]);
        $this->addColumn('items', [
            'header' => __('Items'),
            'index' => 'shipping_address_id',
            'frame_callback' => [$this, 'callback_items'],
            'filter' => false,
        ]);
        return parent::_prepareColumns();
    }

    public function callback_items($value, $sellerorder, $column, $isExport = false)
    {
        $items = '';
        $orderitems = $this->orderitems->getCollection()->addFieldToFilter('seller_id',
            (int)$sellerorder->getSellerId())->addFieldToFilter('order_id', $sellerorder->getOrderId());
        if ($orderitems->count()) {
            foreach ($orderitems as $orderitem) {
                $items .= $orderitem->getProductName() . '(qty : ' . (int)$orderitem['product_qty'] . '), ';
            }
        } else {
            $items = $value;
        }
        return $items;
    }

    public function getCreatedat($value, $sellerorder, $column, $isExport = false)
    {
        $order = $sellerorder->getOrder();
        return $order->getData('created_at');
    }

    public function getCustomerEmail($value, $sellerorder, $column, $isExport)
    {
        $customerId = $sellerorder->getCustomerId();
        $customer = $this->customerRepository->getById($customerId);
        $email = $customer->getEmail();
        return $email;
    }

    public function getSellerName($value, $sellerorder, $column, $isExport)
    {
        $sellerId = $sellerorder->getSellerId();
        $seller = $this->seller->create()->load($sellerId);
        $sellername = $seller->getName();
        return $sellername;
    }

    /**
     * {@inheritdoc}
     */
    public function getRowUrl($row)
    {
        return $this->getUrl(
            '*/*/add',
            [
                'seller_id' => $row->getSellerId(),
                'order_id' => $row->getOrderId()
            ]
        );
    }
}
