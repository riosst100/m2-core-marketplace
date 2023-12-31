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
 * @package    Lof_PreOrder
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
namespace Lof\PreOrder\Controller\Preorder;

use Magento\Framework\App\Action\Context;

class Check extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Lof\Preorder\Helper\Data
     */
    protected $_preorderHelper;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * @param Context $context
     * @param \Lof\Preorder\Helper\Data $preorderHelper
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        \Lof\PreOrder\Helper\Data $preorderHelper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->_preorderHelper = $preorderHelper;
        $this->_resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $info = [];
        $helper = $this->_preorderHelper;
        $type = $this->getRequest()->getParam('type');
        $productId = $this->getRequest()->getParam('product_id');

        if ($type == 1) {
            $product = $this->_preorderHelper->getProduct($productId);
            $attributesInfo = $this->getRequest()->getParam('info');
            $productId = $helper->getAssociatedId($attributesInfo, $product);
        }

        if ($helper->isPreorder($productId)) {
            $payHtml = $helper->getPayPreOrderHtml();
            $msg = $helper->getPreOrderInfoBlock($productId);
            $info['preorder'] = 1;
            $info['msg'] = $msg;
            $info['payHtml'] = $payHtml;
        } else {
            $info['preorder'] = 0;
        }
        $result = $this->_resultJsonFactory->create();
        $result->setData($info);
        return $result;
    }
}
