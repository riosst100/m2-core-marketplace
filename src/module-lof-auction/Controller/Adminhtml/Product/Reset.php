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
 * Do not edit or add to this file if ayou wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_Auction
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\Auction\Controller\Adminhtml\Product;

use Magento\Backend\Model\View\Result\Redirect;

/**
 * Class Reset
 * @package Lof\Auction\Controller\Adminhtml\Product
 */
class Reset extends \Lof\Auction\Controller\Adminhtml\Product
{
    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_Auction::product_delete');
    }

    /**
     * Delete action
     *
     * @return Redirect
     */
    public function execute()
    {
        // check if we know what should be reset
        $id = $this->getRequest()->getParam('id');
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create('Lof\Auction\Model\Product');
                $model->load($id);

                if ($model->getId()) {
                    $history = $this->_objectManager->create('Lof\Auction\Model\History');
                    $mixBid = $this->_objectManager->create('Lof\Auction\Model\MixAmount');
                    $manualBid = $this->_objectManager->create('Lof\Auction\Model\Amount');
                    $autoBid = $this->_objectManager->create('Lof\Auction\Model\AutoAuction');

                    //Reset History
                    $historyCollection = $history->getCollection()->addFieldToFilter("auction_id", $id);
                    foreach ($historyCollection as $_item) {
                        $_item->delete();
                    }
                    //Reset Manual Bid
                    $manualCollection = $manualBid->getCollection()->addFieldToFilter("auction_id", $id);
                    foreach ($manualCollection as $_item) {
                        $_item->delete();
                    }
                    //Reset Auto Bid
                    $autoCollection = $autoBid->getCollection()->addFieldToFilter("auction_id", $id);
                    foreach ($autoCollection as $_item) {
                        $_item->delete();
                    }
                    //Reset Mix Bid
                    $mixBidCollection = $mixBid->getCollection()->addFieldToFilter("auction_id", $id);
                    foreach ($mixBidCollection as $_item) {
                        $_item->delete();
                    }


                    $model->setCustomerId(0);
                    $model->setMinAmount(0);
                    $model->save();
                }
                // display success message
                $this->messageManager->addSuccess(__('The product has been reset.'));
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find a product to reset.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
