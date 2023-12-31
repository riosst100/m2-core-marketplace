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
 * @copyright  Copyright (c) 2020 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\Rma\Block\Adminhtml\Customer\Edit\Tab;

class Rma extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    protected $_status;

    /**
     * @var \Lofmp\Rma\Model\RmaFactory
     */
    protected $_rmaFactory;

    /**
     * Rma constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status $status
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Lofmp\Rma\Model\RmaFactory $rmaFactory
     * @param \Lofmp\Rma\Model\StatusFactory $statusFactory
     * @param \Lofmp\Rma\Helper\Help $Help
     * @param \Lofmp\Rma\Helper\Data $dataHelper
     * @param \Lofmp\Rma\Model\ItemRepository $itemRepository
     * @param \Lofmp\Rma\Model\ResourceModel\Item\CollectionFactory $itemCollectionFactory
     * @param \Magento\Sales\Api\OrderItemRepositoryInterface $orderItemRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $status,
        \Magento\Framework\Registry $coreRegistry,
        \Lofmp\Rma\Model\RmaFactory $rmaFactory,
        \Lofmp\Rma\Model\StatusFactory $statusFactory,
        \Lofmp\Rma\Helper\Help $Help,
        \Lofmp\Rma\Helper\Data $dataHelper,
        \Lofmp\Rma\Model\ItemRepository $itemRepository,
        \Lofmp\Rma\Model\ResourceModel\Item\CollectionFactory $itemCollectionFactory,
        \Magento\Sales\Api\OrderItemRepositoryInterface $orderItemRepository,
        array $data = []
    ) {
        $this->orderItemRepository = $orderItemRepository;
        $this->_status = $status;
        $this->_coreRegistry = $coreRegistry;
        $this->_rmaFactory = $rmaFactory;
        $this->rmaHelper = $Help;
        $this->statusFactory = $statusFactory;
        $this->dataHelper = $dataHelper;
        $this->itemRepository = $itemRepository;
        $this->itemCollectionFactory = $itemCollectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Set grid params
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('rma_rma_grid');
        $this->setDefaultSort('updated_at');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
    }

    /**
     * Retirve currently edited product model
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getCustomer()
    {
        return $this->_coreRegistry->registry('current_customer');
    }

    /**
     * Add filter
     *
     * @param object $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        parent::_addColumnFilterToCollection($column);
        return $this;
    }

    /**
     * Prepare collection
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareCollection()
    {
        $collection = $this->_rmaFactory->create()->getCollection();
        $customer = $this->getCustomer();
        $collection->addFieldToFilter('main_table.customer_id', $customer->getId());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'grma_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'rma_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'gincrement_id',
            [
                'header' => __('RMA #'),
                'index' => 'increment_id',
                'header_css_class' => 'col-increment_id',
                'column_css_class' => 'col-increment_id'
            ]
        );
        $this->addColumn(
            'gparent_rma_id',
            [
                'header' => __('Parent Id'),
                'index' => 'parent_rma_id',
                'style' => 'width:100px;',
                'header_css_class' => 'col-parent_rma_id',
                'column_css_class' => 'col-parent_rma_id'
            ]
        );
        $this->addColumn('gcustomer_name', [
            'header' => __('Customer Name'),
            'index' => ['customer_firstname', 'customer_lastname'],
            'type' => 'concat',
            'separator' => ' ',
            'header_css_class' => 'col-customer_name',
            'column_css_class' => 'col-customer_name',
            'filter_index' => new \Zend_Db_Expr("CONCAT(customer.firstname, ' ', customer.lastname)"),
        ]);
        $this->addColumn(
            'gorder_id',
            [
                'header' => __('Order Id'),
                'index' => 'order_id',
                'style' => 'width:100px;',
                'header_css_class' => 'col-order_id',
                'column_css_class' => 'col-order_id'
            ]
        );
        $this->addColumn('gemail', [
            'header' => __('Customer Email'),
            'index' => 'customer_email',
            'type' => 'text',
            'separator' => ' ',
            'filter_index' => 'customer.email',
            'header_css_class' => 'col-customer_email',
            'column_css_class' => 'col-customer_email'
        ]);
        $this->addColumn('guser_id', [
            'header' => __('Owner'),
            'index' => 'user_id',
            'filter_index' => 'main_table.user_id',
            'type' => 'options',
            'header_css_class' => 'col-user_id',
            'column_css_class' => 'col-user_id',
            'options' => $this->dataHelper->getAdminOptionArray(),
        ]);
        $this->addColumn('glast_reply_name', [
            'header' => __('Last Replier'),
            'index' => 'last_reply_name',
            'filter_index' => 'main_table.last_reply_name',
            'frame_callback' => [$this, '_lastReplyFormat'],
        ]);
        $this->addColumn(
            'glast_reply_name',
            [
                'header' => __('Last Reply'),
                'index' => 'last_reply_name',
                'style' => 'width:100px;',
                'header_css_class' => 'col-last_reply_name',
                'column_css_class' => 'col-last_reply_name'
            ]
        );
        $this->addColumn(
            'glast_reply_at',
            [
                'header' => __('Last Reply At'),
                'index' => 'last_reply_at',
                'style' => 'width:100px;',
                'header_css_class' => 'col-last_reply_at',
                'column_css_class' => 'col-last_reply_at'
            ]
        );
        $this->addColumn('gstatus_id', [
            'header' => __('Status'),
            'index' => 'status_id',
            'filter_index' => 'main_table.status_id',
            'type' => 'options',
            'options' => $this->statusFactory->create()->getCollection()->getOptionArray(),
        ]);
        $this->addColumn(
            'gcreated_at',
            [
                'header' => __('Created At'),
                'index' => 'created_at',
                'style' => 'width:100px;',
                'header_css_class' => 'col-created_at',
                'column_css_class' => 'col-created_at'
            ]
        );
        $this->addColumn('gstore_id', [
            'header' => __('Store'),
            'index' => 'store_id',
            'filter_index' => 'main_table.store_id',
            'type' => 'options',
            'header_css_class' => 'col-store_id',
            'column_css_class' => 'col-store_id',
            'options' => $this->rmaHelper->getCoreStoreOptionArray(),
        ]);
        $this->addColumn('gitems', [
            'header' => __('Items'),
            'column_css_class' => 'nowrap',
            'type' => 'text',
            'header_css_class' => 'col-items',
            'column_css_class' => 'col-items',
            'frame_callback' => [$this, 'itemsFormat']
        ]);
        $this->addColumn(
            'gaction',
            [
                'header' => __('Action'),
                'type' => 'action',
                'renderer' => 'Lofmp\Rma\Block\Adminhtml\Rma\Renderer\RmaAction',
            ]
        );
        return parent::_prepareColumns();
    }

    /**
     * @param \Lofmp\Rma\Block\Adminhtml\Rma\Grid $renderedValue
     * @param \Lofmp\Rma\Model\Rma $rma
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @param bool $isExport
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function itemsFormat($renderedValue, $rma, $column, $isExport)
    {
        $html = [];
        foreach ($this->dataHelper->getItems($rma) as $item) {
            $orderItem = $this->orderItemRepository->get($item->getOrderItemId());
            $s = '<b>' . $orderItem->getName() . '</b>';
            $s .= ' / ';
            $s .= $item->getReasonName() ? $item->getReasonName() : '-';
            $s .= ' /  ';
            $s .= $item->getConditionName() ? $item->getConditionName() : '-';
            $s .= ' / ';
            $s .= $item->getResolutionName() ? $item->getResolutionName() : '-';

            $html[] = $s;
        }

        return implode('<br>', $html);
    }

    /**
     * @param $renderedValue
     * @param $rma
     * @param $column
     * @param $isExport
     * @return string
     */
    public function getItemConditions($renderedValue, $rma, $column, $isExport)
    {
        $html = [];
        foreach ($this->dataHelper->getItems($rma) as $item) {
            $orderItem = $this->orderItemRepository->get($item->getOrderItemId());
            $s = '<b>' . $orderItem->getName() . '</b>';
            $s .= ' / ';
            $s .= $item->getReasonName() ? $item->getReasonName() : '-';
            $s .= ' /  ';
            $s .= $item->getConditionName() ? $item->getConditionName() : '-';
            $s .= ' / ';
            $s .= $item->getResolutionName() ? $item->getResolutionName() : '-';

            $html[] = $s;
        }

        return implode('<br>', $html);
    }

    /**
     * @param \Lofmp\Rma\Block\Adminhtml\Rma\Grid $renderedValue
     * @param \Lofmp\Rma\Model\Rma $rma
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @param bool $isExport
     *
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function _lastReplyFormat($renderedValue, $rma, $column, $isExport)
    {
        $name = $rma->getLastReplyName();
        // If last message is automated, assign Last Reply Name value to owner, if such exists
        $lastMessage = $this->dataHelper->getLastMessage($rma);
        if ($lastMessage && !$lastMessage->getUserId() && !$lastMessage->getCustomerId()) {
            $name = '';
        }

        if (!$rma->getIsAdminRead()) {
            $name .= ' <img src="' . $this->_assetRepo->getUrl('Lofmp_Rma::images/fam_newspaper.gif') . '">';
        }

        return $name;
    }
}
