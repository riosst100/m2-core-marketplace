<?php
/**
 * Copyright © Landofcoder.com All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\Auction\Controller\Adminhtml\Absenteebid;

use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{

    protected $dataPersistor;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
    ) {
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('entity_id');
            $auction_id = $this->getRequest()->getParam('auction_id');
            $max_absentee_amount = (float)$this->getRequest()->getParam('max_absentee_amount');
            $auctionModel = $this->_objectManager->create(\Lof\Auction\Model\Product::class)->load($auction_id);
            if (!$auctionModel || ($auctionModel && !$auctionModel->getId())) {
                $this->messageManager->addErrorMessage(__('This Auction with ID %1 no longer exists.', $auction_id));
                return $resultRedirect->setPath('*/*/');
            }
            $current_bid = (float)$auctionModel->getMinAmount();
            if ($max_absentee_amount < $current_bid) {
                $this->messageManager->addErrorMessage(__('Max Absentee Bid should greater than Current Bid Amount. Current Bid Amount= %1.'. $current_bid));
                return $resultRedirect->setPath('*/*/');
            }
            $data['product_id'] = $auctionModel->getProductId();
            $model = $this->_objectManager->create(\Lof\Auction\Model\MaxAbsenteeBid::class)->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Absentee Bid no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            $model->setData($data);

            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the Absentee Bid.'));
                $this->dataPersistor->clear('lofauction_absenteebid');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['entity_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Absentee Bid.'));
            }

            $this->dataPersistor->set('lofauction_absenteebid', $data);
            return $resultRedirect->setPath('*/*/edit', ['entity_id' => $this->getRequest()->getParam('entity_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}

