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

namespace Lof\MarketPlace\Controller\Adminhtml\Withdrawal;

use Magento\Framework\Controller\ResultFactory;

class Submit extends \Lof\MarketPlace\Controller\Adminhtml\Withdrawal
{
    /**
     * Withdrawal list action
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu("Lof_MarketPlace::withdrawal");
        $resultPage->getConfig()->getTitle()->prepend(__('Withdrawals'));
        $resultPage->addBreadcrumb(__('Withdrawals'), __('Withdrawals'));
        $resultPage->addBreadcrumb(__('Manage Withdrawals'), __('Manage Withdrawals'));
        $data = $this->getRequest()->getParams();

        $collection = $this->_objectManager->create(\Lof\MarketPlace\Model\Withdrawal::class)
            ->load($data['withdrawal_id'], 'withdrawal_id');
        $collection->setStatus($data['withdrawal_status'])->setAdminMessage($data['note'])->save();
        if ($data['withdrawal_status'] == 1) {
            $amount = $this->_objectManager->create(\Lof\MarketPlace\Model\Amount::class);
            $amount->load($data['sellerid'], 'seller_id');
            $withdrawal = -$this->toInt($data['amount']);
            $description = __('	Withdraw Money : Amount ') . $data['amount']
                . ', Fee ' . $data['fee'] . ', Net Amount ' . $data['netamount'];

            $this->updateSellerAmount($data['sellerid'], $withdrawal, $description);
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @param $str
     * @return int
     */
    public function toInt($str)
    {
        return (float)preg_replace("/[^\d.-]/i", "", $str);
    }

    /**
     * Update seller amount
     *
     * @param int $updateSellerId
     * @param double $totalAmount
     *
     * @return void
     */
    public function updateSellerAmount($updateSellerId, $totalAmount, $description)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $sellerModel = $objectManager->get(\Lof\MarketPlace\Model\Amount::class);
        $amountTransaction = $objectManager->get(\Lof\MarketPlace\Model\Amounttransaction::class);
        $date = $objectManager->get(\Magento\Framework\Stdlib\DateTime\DateTime::class);
        $sellerDetails = $sellerModel->load($updateSellerId, 'seller_id');
        $remainingAmount = $sellerDetails->getAmount();
        $totalRemainingAmount = $remainingAmount + $totalAmount;
        $amountTransaction->setSellerId($updateSellerId)
            ->setAmount($totalAmount)
            ->setBalance($totalRemainingAmount)
            ->setDescription($description)
            ->setUpdatedAt($date->gmtDate());
        $sellerDetails->setSellerId($updateSellerId)->setAmount($totalRemainingAmount);
        $sellerDetails->save();
        $amountTransaction->save();
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_MarketPlace::withdrawal_submit');
    }
}
