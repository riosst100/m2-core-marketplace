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
 * @package    Lofmp_TableRateShipping
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\TableRateShipping\Controller\Adminhtml\Shipping;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var \Lofmp\TableRateShipping\Model\ShippingFactory
     */
    protected $shippingFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Lofmp\TableRateShipping\Model\ShippingFactory $shippingFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Lofmp\TableRateShipping\Model\ShippingFactory $shippingFactory
    ) {
        parent::__construct($context);
        $this->shippingFactory = $shippingFactory;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('lofmpshipping_id');
        if ($id) {
            try {
                $model = $this->shippingFactory->create();
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccessMessage(__('You deleted the table rate.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a table rate to delete.'));
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lofmp_TableRateShipping::shipping');
    }
}
