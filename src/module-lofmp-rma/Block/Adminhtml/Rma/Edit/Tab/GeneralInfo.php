<?php
/**
 * LandOfCoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://www.landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   LandOfCoder
 * @package    Lofmp_Rma
 * @copyright  Copyright (c) 2020 Landofcoder (http://www.LandOfCoder.com/)
 * @license    http://www.LandOfCoder.com/LICENSE-1.0.html
 */

namespace Lofmp\Rma\Block\Adminhtml\Rma\Edit\Tab;

use Magento\Backend\Block\Widget\Form;

class GeneralInfo extends Form
{
    /**
     * @var \Lof\MarketPlace\Model\SellerFactory
     */
    protected $sellerFactory;

    /**
     * GeneralInfo constructor.
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Sales\Api\CreditmemoRepositoryInterface $creditMemoRepository
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\Convert\DataObject $convertDataObject
     * @param \Lofmp\Rma\Model\ResourceModel\Address\Collection $addressCollection
     * @param \Lofmp\Rma\Helper\Help $Helper
     * @param \Lofmp\Rma\Helper\Data $dataHelper
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\Model\Url $backendUrlManager
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Api\CreditmemoRepositoryInterface $creditMemoRepository,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Convert\DataObject $convertDataObject,
        \Lofmp\Rma\Model\ResourceModel\Address\Collection $addressCollection,
        \Lofmp\Rma\Helper\Help $Helper,
        \Lofmp\Rma\Helper\Data $dataHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Model\Url $backendUrlManager,
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        array $data = []
    ) {
        $this->addressCollection = $addressCollection;
        $this->datahelper = $dataHelper;
        $this->helper = $Helper;
        $this->orderRepository = $orderRepository;
        $this->creditMemoRepository = $creditMemoRepository;
        $this->formFactory = $formFactory;
        $this->registry = $registry;
        $this->request = $context->getRequest();
        $this->backendUrlManager = $backendUrlManager;
        $this->convertDataObject = $convertDataObject;
        $this->orderFactory = $orderFactory;
        $this->sellerFactory = $sellerFactory;

        parent::__construct($context, $data);
    }

    /**
     * General information form
     *
     * @param \Lofmp\Rma\Api\Data\RmaInterface $rma
     *
     * @return string
     */
    public function _prepareForm()
    {
        $form = $this->formFactory->create();
        $this->setForm($form);
        /** @var \Lofmp\Rma\Model\Rma $rma */
        $rma = $this->registry->registry('current_rma');
        /** @var \Magento\Framework\Data\Form\Element\Fieldset $fieldset */
        $fieldset = $form->addFieldset('edit_fieldset', ['legend' => __('General Information')]);

        if ($this->_isAllowedAction('Lofmp_Rma::rma_rma')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        if ($this->hasData('is_valid') && $this->hasData('local_valid') && !$this->getData('is_valid') && !$this->getData('local_valid')) {
            $isElementDisabled = true;

        }

        if ($rma->getId()) {
            $fieldset->addField('rma_id', 'hidden', [
                'name' => 'rma_id',
                'value' => $rma->getId(),
            ]);
        }

        $fieldset->addField('order_id', 'hidden', [
            'name' => 'order_id',
            'value' => $this->getOrderId(),
        ]);

        if ($rma->getCustomerId()) {
            $fieldset->addField('customer', 'link', [
                'label' => __('Customer'),
                'name' => 'customer',
                'value' => $this->getOrder()->getCustomerName(),
                'href' => $this->backendUrlManager->getUrl('customer/index/edit', ['id' => $rma->getCustomerId()]),
            ]);
        }

        $fieldset->addField('Customer Email', 'label', [
            'label' => __('Customer Email'),
            'name' => 'customer_email',
            'value' => $this->getOrder()->getCustomerEmail(),
            'disabled' => $isElementDisabled
        ]);

        if ($seller_id = $rma->getSellerId()) {
            $seller = $this->sellerFactory->create()->load($seller_id, 'seller_id');
            if ($seller) {
                $fieldset->addField('seller', 'link', [
                    'label' => __('Seller'),
                    'name' => 'seller',
                    'value' => $seller->getName(),
                    'href' => $this->backendUrlManager->getUrl(
                        'lofmarketplace/seller/edit',
                        ['seller_id' => $seller_id]
                    ),
                ]);
            }
        }
        $rma_text = __(" (Parent RMA)");
        $fieldset->addField('order_link', 'link', [
            'label' => __('Order #'),
            'name' => 'order_id',
            'value' => '#' . $this->getOrder()->getIncrementId() . $rma_text,
            //'href'  => $this->getUrl('sales/order/view', ['order_id' => $rma->getOrderId()]),
            'href' => $this->getUrl('sales/order/view', ['order_id' => $this->getOrder()->getId()]),
        ]);

        $child_orders = $this->getChildRmaOrders();
        if ($child_orders) {
            $i = 1;
            $child_rma_list = $this->getChildRma();
            foreach ($child_orders as $_order) {
                $order_id = $_order->getId();
                $_rma = isset($child_rma_list[$order_id]) ? $child_rma_list[$order_id] : false;
                $rma_text = "";
                if ($_rma) {
                    $rma_text = " - (" . __("Child RMA Id #%1", $_rma->getIncrementId()) . ")";
                }
                $fieldset->addField('order_link_' . $i, 'link', [
                    'label' => __('Order #'),
                    'name' => 'child_order_id',
                    'value' => '#' . $_order->getIncrementId() . $rma_text,
                    'href' => $this->getUrl('sales/order/view', ['order_id' => $_order->getId()]),
                ]);
                $i++;
            }
        }

        $fieldset->addField('user_id', 'select', [
            'label' => __('Rma Manager'),
            'name' => 'user_id',
            'value' => $rma->getUserId(),
            'values' => $this->datahelper->getAdminOptionArray(true),
            'disabled' => $isElementDisabled
        ]);

        $fieldset->addField('status_id', 'select', [
            'label' => __('Status'),
            'name' => 'status_id',
            'value' => $rma->getStatusId(),
            'values' => $this->convertDataObject->toOptionArray($this->datahelper->getStatusList(), "id", "name"),
            'disabled' => $isElementDisabled
        ]);
        $attachments = [];
        if ($rma->getId()) {
            $attachments = $this->datahelper->getAttachments('return_label', $rma->getId());
        }

        $fieldset->addField('return_label', 'Lofmp\Rma\Block\Adminhtml\Rma\Edit\Tab\Element\File', [
            'label' => __('Return Label'),
            'name' => 'return_label',
            'attachment' => array_shift($attachments),
        ]);

        if ($this->datahelper->getExchangeOrderIds($rma->getId())) {

            $links = [];
            foreach ($this->datahelper->getExchangeOrderIds($rma->getId()) as $id) {
                $exchageOrder = $this->orderRepository->get($id);
                $links[] = "<a href='" . $this->getUrl(
                        'sales/order/view',
                        ['order_id' => $id]
                    ) . "'>#" . $exchageOrder->getIncrementId() . '</a>';
            }
            $fieldset->addField('exchangeorder', 'note', [
                'label' => __('Exchage Order'),
                'text' => implode(', ', $links),
            ]);
        }
        if ($this->datahelper->getCreditMemoIds($rma->getId())) {
            $links = [];
            foreach ($this->datahelper->getCreditMemoIds($rma->getId()) as $id) {

                $creditmemo = $this->creditMemoRepository->get($id);
                $links[] = "<a href='" . $this->getUrl(
                        'sales/creditmemo/view',
                        ['creditmemo_id' => $id]
                    ) . "'>#" . $creditmemo->getIncrementId() . '</a>';
            }
            $fieldset->addField('credit_memo_id', 'note', [
                'label' => __('Credit Memo'),
                'text' => implode(', ', $links),
            ]);
        }

        $defaultAddress = $this->helper->getConfig($rma->getStoreId(), 'rma/general/return_address');

        // $fieldset->addField('return_address', 'select', [
        //     'label'  => __('Return Address'),
        //     'name'   => 'return_address',
        //     //'value'  => $rma->getaddress()?$rma->getaddress():'DefaultAddress',
        //     'value'  => $rma->getReturnAddress()?$rma->getReturnAddress():'DefaultAddress',
        //     'values' => $this->addressCollection->toOptionArray(true, $defaultAddress),
        //     'disabled' => $isElementDisabled
        // ]);

        $fieldset->addField('return_address', 'text', [
            'label' => __('Return Address'),
            'name' => 'return_address',
            'value' => $rma->getReturnAddress(),
        ]);

        return parent::_prepareForm();
    }

    /**
     * @param int $order_id
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder($order_id = 0)
    {
        $order_id = $order_id ? (int)$order_id : $this->getOrderId();
        $order = $this->orderFactory->create()->load($order_id);
        return $order;
    }

    /**
     * @return int
     */
    public function getOrderId()
    {
        $orderId = 0;
        if ($this->getCurrentRma()) {
            $orderId = $this->getCurrentRma()->getOrderId();
        }
        if ($this->registry->registry('current_rma')) {
            $orderId = $this->registry->registry('current_rma')->getOrderId();
        }
        if (!$orderId) {
            $path = trim($this->request->getPathInfo(), '/');
            $params = explode('/', $path);
            $orderId = end($params);
        }
        return (int)$orderId;
    }

    /**
     * @return mixed|null
     */
    public function getCurrentRma()
    {
        $rma = $this->registry->registry('current_rma');
        return $rma;
    }

    /**
     * @return array
     */
    public function getChildRma()
    {
        $rma = $this->getCurrentRma();
        if (!isset($this->_rma_list)) {
            $this->_rma_list = [];
        }
        if ($rma->getId() && (0 == $rma->getParentRmaId())) {
            $this->_rma_list = $rma->getListChildRma($rma->getId());
            if ($this->_rma_list) {
                $tmp_list = [];
                foreach ($this->_rma_list as $_rma) {
                    $order_id = $_rma->getOrderId();
                    $tmp_list[$order_id] = $_rma;
                }
                $this->_rma_list = $tmp_list;
            }
        }
        return $this->_rma_list;
    }

    /**
     * @return bool
     */
    public function getChildRmaOrders()
    {
        $child_rma = $this->getChildRma();
        if ($child_rma) {
            if (!isset($this->_child_orders)) {
                foreach ($child_rma as $_rma) {
                    $order_id = $_rma->getOrderId();
                    $this->_child_orders[$order_id] = $this->orderFactory->create()->load($order_id);
                }
            }
            return $this->_child_orders;
        }
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
