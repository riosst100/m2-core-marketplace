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

namespace Lofmp\Rma\Block\Adminhtml\Rma\Edit\Tab;

class Items extends \Magento\Backend\Block\Template
{
    /**
     * Items constructor.
     * @param \Lofmp\Rma\Helper\Data $dataHelper
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
        \Lofmp\Rma\Helper\Data $dataHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->request = $context->getRequest();
        $this->dataHelper = $dataHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return \Lofmp\Rma\Model\Rma
     */
    public function getCurrentRma()
    {
        if ($this->registry->registry('current_rma')) {
            return $this->registry->registry('current_rma');
        }
        return null;
    }

    /**
     * @return mixed
     */
    public function getOrder()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $objectManager->get('Magento\Sales\Model\Order')->load($this->getOrderId());
        return $order;
    }

    /**
     * Get Order Id of current rma or from url
     * @return int $orderId
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
     * @param $productid
     * @return mixed
     */
    public function getSellerIdByProductId($productid)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $product = $objectManager->create('Magento\Catalog\Model\Product')->load($productid, 'entity_id');
        return $product->getSellerId();
    }

    /**
     * @param $item
     * @return array|int[]
     */
    public function getRmaItemData($item)
    {
        if ($this->getCurrentRma() && ($rma_id = $this->getCurrentRma()->getId())) {
            return $this->dataHelper->getRmaItemData($item, $rma_id);
        }
        return [
            "qty_requested" => 1,
            "item_id" => 0,
            "reason_id" => 0,
            "condition_id" => 0,
            "resolution_id" => 0,
            "admin_commission" => 0,
            "seller_commission" => 0
        ];
    }

    /**
     * @return \Lofmp\Rma\Api\Data\ReturnInterface[]
     */
    public function getConditions()
    {
        return $this->dataHelper->getConditions();
    }

    /**
     * @return \Lofmp\Rma\Api\Data\ReturnInterface[]
     */
    public function getResolutions()
    {
        return $this->dataHelper->getResolutions();
    }

    /**
     * @return \Lofmp\Rma\Api\Data\ReturnInterface[]
     */
    public function getReasons()
    {
        return $this->dataHelper->getReasons();
    }

    /**
     * @param $item
     * @return int
     */
    public function getQtyAvailable($item)
    {
        return $this->dataHelper->getItemQuantityAvaiable($item);
    }

    /**
     * @param $item
     * @return int
     */
    public function getQtyRequest($item)
    {
        if ($this->getCurrentRma() && ($rma_id = $this->getCurrentRma()->getId())) {
            return $this->dataHelper->getQtyReturnedRma($item, $rma_id);
        }
        return 1;
    }

    /**
     * @param $Uid
     */
    public function getAttachmentUrl($Uid)
    {
        $this->context->getUrlBuilder()->getUrl('rma/attachment/download', ['uid' => $Uid]);
    }
}
