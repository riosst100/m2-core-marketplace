<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Landofcoder
 * @package     Lofmp_Quickrfq
 * @copyright   Copyright (c) 2020 Landofcoder (https://landofcoder.com/)
 * @license     https://landofcoder.com/LICENSE.txt
 */
namespace Lofmp\Quickrfq\Controller\Marketplace\Quickrfq;

/**
 * Class Done
 * @package Lofmp\Quickrfq\Controller\Marketplace\Quickrfq
 */
class Done extends \Magento\Framework\App\Action\Action
{

    /**
     * Done action
     *
     */
    public function execute()
    {
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('quickrfq_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            $contact_name = "";
            try {
                // init model and done
                $model = $this->_objectManager->create('Lof\Quickrfq\Model\Quickrfq');
                $model->load($id);
                $model->setStatus(\Lof\Quickrfq\Model\Quickrfq::STATUS_DONE);
                // display success message
                $this->messageManager->addSuccess(__('The quote has been done.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/');
            }
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find a record to change status.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
