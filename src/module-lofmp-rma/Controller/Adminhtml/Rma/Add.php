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



namespace Lofmp\Rma\Controller\Adminhtml\Rma;

use Magento\Framework\Controller\ResultFactory;
use Lofmp\Rma\Controller\Adminhtml\Rma;

class Add extends Rma
{
    protected $helperData;

    public function __construct(
        \Lofmp\Rma\Model\RmaFactory $rmaFactory,
        \Lofmp\Rma\Helper\Data $helperData,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->rmaFactory = $rmaFactory;
        $this->registry = $registry;
        $this->helperData = $helperData;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $this->initPage($resultPage)->getConfig()->getTitle()->prepend(__('New RMA'));
        $data = $this->backendSession->getFormData(true);
        $rma = $this->rmaFactory->create();
        if (!empty($data)) {
            $rma->setData($data);
        }
        $orderId = $this->getRequest()->getParam('order_id');
        $sellerId = $this->getRequest()->getParam('seller_id');
        if ($orderId) {
            $rma->setOrderId((int)$orderId);
        }
        if ($sellerId) {
            $rma->setSellerId((int)$sellerId);
        }
        if (!$rma->getId() && $sellerId) {
            $sellerAddress = $this->helperData->getSellerAddress(($sellerId));
            if (!$sellerAddress) {
                $sellerAddress = $sellerAddress?$sellerAddress:$this->helperData->getConfig("general/return_address");
            }
            $rma->setReturnAddress($sellerAddress);
        }
        $this->registry->register('current_rma', $rma);
        if ($orderId) {
            $this->_addContent($resultPage->getLayout()->createBlock('\Lofmp\Rma\Block\Adminhtml\Rma\Edit'))->_addLeft($resultPage->getLayout()->createBlock('\Lofmp\Rma\Block\Adminhtml\Rma\Edit\Tabs'));
        }
        return $resultPage;
    }
}
