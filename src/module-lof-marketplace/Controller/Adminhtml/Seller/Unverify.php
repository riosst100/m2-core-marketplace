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

namespace Lof\MarketPlace\Controller\Adminhtml\Seller;

class Unverify extends \Magento\Backend\App\Action
{
    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $helper;

    /**
     * @var \Lof\MarketPlace\Model\Sender
     */
    protected $sender;

    /**
     * Unverify constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param \Lof\MarketPlace\Model\Sender $sender
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Lof\MarketPlace\Helper\Data $helper,
        \Lof\MarketPlace\Model\Sender $sender
    ) {
        $this->sender = $sender;
        $this->helper = $helper;
        parent::__construct($context);
    }

    /**
     * Delete action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('seller_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $model = $this->_objectManager->create(\Lof\MarketPlace\Model\Seller::class);
                $model->load($id);
                $data = $model->getData();
                $data['url'] = $model->getUrl();
                $model->setVerifyStatus(0)->save();

                $this->messageManager->addSuccessMessage(__('The seller has been cancel verification.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $resultRedirect->setPath('*/*/edit', ['seller_id' => $id]);
            }
        }

        $this->messageManager->addErrorMessage(__('We can\'t find a seller to cancel verification.'));

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_MarketPlace::seller_unverify');
    }
}
