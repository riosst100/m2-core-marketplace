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



namespace Lofmp\Rma\Controller\Adminhtml\Reason;

use Magento\Framework\Controller\ResultFactory;

class Save extends \Lofmp\Rma\Controller\Adminhtml\Reason
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if ($data = $this->getRequest()->getParams()) {
            $reason = $this->_initReason();
            $reason->setName($data['name']);
            unset($data['name']);
            $reason->addData($data);
            try {
                $reason->save();

                $this->messageManager->addSuccessMessage(__('Reason was successfully saved'));
                $this->backendSession->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        ['id' => $reason->getId(), 'store' => $reason->getStoreId()]
                    );
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->backendSession->setFormData($data);

                return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            }
        }
        $this->messageManager->addErrorMessage(__('Unable to find Reason to save'));

        return $resultRedirect->setPath('*/*/');
    }
}
