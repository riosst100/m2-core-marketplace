<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_SmtpEmail
 * @copyright  Copyright (c) 2016 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\SmtpEmail\Controller\Adminhtml\Blacklist;

class Delete extends \Lof\SmtpEmail\Controller\Adminhtml\Blacklist
{
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_SmtpEmail::blacklist_delete');
    }
    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('blacklist_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create('Lof\SmtpEmail\Model\Blacklist');
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccess(__('You deleted the blacklist.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['blacklist_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find a blacklist to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
