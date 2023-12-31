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
 * @copyright  Copyright (c) 2022 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Controller\Adminhtml\Amounttransaction;


use Lof\MarketPlace\Model\AmountFactory;
use Lof\MarketPlace\Model\ResourceModel\Amounttransaction\CollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;

class MassApprove extends \Magento\Backend\App\Action {
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        AmountFactory $sellerAmountFactory,
        \Lof\MarketPlace\Model\AmounttransactionFactory $amountTransactionFactory
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->sellerAmountFactory = $sellerAmountFactory;
        $this->amountTransactionFactory = $amountTransactionFactory;
        parent::__construct($context);
    }

    /**
     * @throws LocalizedException
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $totalRecord = 0;
        foreach ($collection as $transaction) {
            if ($transaction->getStatus() == 0){
                $totalAmount = $this->updateSellerAmount($transaction->getSellerId(), $transaction->getAmount());
                $transaction->setStatus(1);
                $transaction->setBalance($totalAmount);
                $transaction->save();
                $totalRecord++;
            }
        }

        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been approved.', $totalRecord));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @param $sellerId
     * @param $amount
     * @return mixed
     * @throws \Exception
     */
    public function updateSellerAmount($sellerId, $amount)
    {
        $sellerAmountModel = $this->sellerAmountFactory->create()->load($sellerId, 'seller_id');
        $totalAmount = $sellerAmountModel->getAmount() + $amount;
        $sellerAmountModel->setAmount($totalAmount);
        $sellerAmountModel->save();
        return $totalAmount;
    }
}
