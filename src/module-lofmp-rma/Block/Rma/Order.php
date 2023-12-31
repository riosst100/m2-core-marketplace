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



namespace Lofmp\Rma\Block\Rma;

class Order extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Lofmp\Rma\Helper\Data    $rmaHelper,
        \Lofmp\Rma\Api\Repository\RmaRepositoryInterface     $rmaRepository,
        \Lofmp\Rma\Model\Status $statusFactory,
        \Magento\Framework\Api\SearchCriteriaBuilder       $searchCriteriaBuilder,
        \Magento\Framework\Api\SortOrderBuilder            $sortOrderBuilder,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->registry        = $registry;
        $this->rmaHelper       = $rmaHelper;
        $this->rmaRepository = $rmaRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder      = $sortOrderBuilder;
        $this->status         = $statusFactory;
        $this->context         = $context;

        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Sales\Model\Order
     */
    public function getCurrentOrder()
    {
        return $this->registry->registry('current_order');
    }

    /**
     * @return bool
     */
    public function isOrderPage()
    {
        return is_object($this->getCurrentOrder());
    }

     /**
      * @return string
      */
    public function getOrderRmas()
    {
        $order = $this->getCurrentOrder();
        $sortOrder = $this->sortOrderBuilder
            ->setField('rma_id')
            ->setDirection(\Magento\Framework\Api\SortOrder::SORT_DESC)
            ->create();

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('main_table.order_id', $order->getId())
            ->addSortOrder($sortOrder);

        return $this->rmaRepository->getList($searchCriteria->create())->getItems();
    }

    /**
     * @return bool
     */
    public function isAllowToReturn()
    {
        if ($orderId = $this->getRequest()->getParam('order_id')) {
            return in_array($orderId, $this->rmaHelper->getAllowOrderId());
        }
    }

    /**
     * @return string
     */
    public function getStatusname($id)
    {
         $status =  $this->status->load($id);
         return $status->getName();
    }
}
