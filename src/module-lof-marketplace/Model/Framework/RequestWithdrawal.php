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

namespace Lof\MarketPlace\Model\Framework;

class RequestWithdrawal
{

    /**
     * @var \Lof\MarketPlace\Model\SellerFactory
     */
    protected $sellerModelFactory;

    /**
     * @var \Lof\MarketPlace\Model\ResourceModel\SellerFactory
     */
    protected $resourceFactory;

    /**
     * @var \Lof\MarketPlace\Model\ResourceModel\Amount\CollectionFactory
     */
    protected $amountcollectionFactory;

    /**
     * @var \Lof\MarketPlace\Model\ResourceModel\Withdrawal
     */
    protected $withdrawalResource;

    /**
     * @var \Lof\MarketPlace\Model\WithdrawalFactory
     */
    protected $withdrawalFactory;

    /**
     * @var \Lof\MarketPlace\Model\ResourceModel\Payment
     */
    protected $paymentResource;

    /**
     * @var \Lof\MarketPlace\Model\PaymentFactory
     */
    protected $paymentFactory;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $helperData;

    /**
     * @var int|float|null
     */
    protected $_sellerBalance = null;

    /**
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerModelFactory
     * @param \Lof\MarketPlace\Model\ResourceModel\SellerFactory $resourceFactory
     * @param \Lof\MarketPlace\Model\ResourceModel\Amount\CollectionFactory $amountcollectionFactory
     * @param \Lof\MarketPlace\Model\ResourceModel\Withdrawal $withdrawalResource
     * @param \Lof\MarketPlace\Model\WithdrawalFactory $withdrawalFactory
     * @param \Lof\MarketPlace\Model\ResourceModel\Payment $paymentResource
     * @param \Lof\MarketPlace\Model\PaymentFactory $paymentFactory
     * @param \Lof\MarketPlace\Helper\Data $helperData
     */
    public function __construct(
        \Lof\MarketPlace\Model\SellerFactory $sellerModelFactory,
        \Lof\MarketPlace\Model\ResourceModel\SellerFactory $resourceFactory,
        \Lof\MarketPlace\Model\ResourceModel\Amount\CollectionFactory $amountcollectionFactory,
        \Lof\MarketPlace\Model\ResourceModel\Withdrawal $withdrawalResource,
        \Lof\MarketPlace\Model\WithdrawalFactory $withdrawalFactory,
        \Lof\MarketPlace\Model\ResourceModel\Payment $paymentResource,
        \Lof\MarketPlace\Model\PaymentFactory $paymentFactory,
        \Lof\MarketPlace\Helper\Data $helperData
    ) {
        $this->sellerModelFactory = $sellerModelFactory;
        $this->resourceFactory = $resourceFactory;
        $this->amountcollectionFactory = $amountcollectionFactory;
        $this->paymentFactory = $paymentFactory;
        $this->paymentResource = $paymentResource;
        $this->withdrawalFactory = $withdrawalFactory;
        $this->withdrawalResource = $withdrawalResource;
        $this->helperData = $helperData;
    }

    /**
     * execute request withdrawl
     *
     * @param array|mixed $data
     * @param int $withdrawl_id
     * @return \Lof\MarketPlace\Model\Withdrawal|mixed|array|bool
     * @throws \Exception
     */
    public function execute($data, $withdrawl_id = 0)
    {
        if ($data) {
            $model = $this->withdrawalFactory->create();
            if ($withdrawl_id) {
                $this->withdrawalResource->load($model, $withdrawl_id);
            }
            $payment_id = isset($data["payment_id"]) ? $data["payment_id"] : 0;
            $seller_id = isset($data["seller_id"]) ? $data["seller_id"] : 0;
            $amount = isset($data["amount"]) ? (float)$data["amount"] : 0;
            $comment = isset($data["comment"]) ? $data["comment"] : "";
            $admin_comment = isset($data["admin_comment"]) ? $data["admin_comment"] :"";

            if (!$payment_id || !$seller_id || $amount <= 0) {
                throw new \Exception(__('Something went wrong while saving withdrawl request.'));
            }
            $seller = $this->sellerModelFactory->create();
            $this->resourceFactory->create()->load($seller, (int)$seller_id);
            if (!$seller->getId() || (int)$seller->getStatus() != \Lof\MarketPlace\Model\Seller::STATUS_ENABLED) {
                throw new \Exception(__('Can not create withdrawl request because seller is no longer exists. Seller ID: %1', $seller_id));
            }
            $payment = $this->paymentFactory->create();
            $this->paymentResource->load($payment, $payment_id);

            if (!$payment->getId()) {
                throw new \Exception(__('Can not create withdrawl request because payment method is no longer exists. Payment ID: %1', $payment_id));
            }

            $sellerAmount = $this->getSellerAmount($seller_id);

            if ((float)$sellerAmount <= 0 || (float)$sellerAmount < (float)$payment->getMinAmount()) {
                throw new \Exception(__('Can not create withdrawl request because seller amount is not available. Seller ID: %1', $seller_id));
            }

            if ((float)$amount <= 0 || (float)$amount < (float)$payment->getMinAmount() || (float)$amount > (float)$sellerAmount) {
                throw new \Exception(__('Can not create withdrawl request because request, because wrong request amount %1, seller available amount = %2, payment min amount = %3, payment max amount = %4', $amount, $sellerAmount, $payment->getMinAmount(), $payment->getMaxAmount()));
            }
            
            if ($payment->getMaxAmount() >= $amount) {
                $withdrawal = $amount;
            } else {
                $withdrawal = $payment->getMaxAmount();
            }

            $updateData = [
                "payment_id" => $payment_id,
                "seller_id" => $seller_id,
                "amount" => $withdrawal,
                "comment" => $comment,
                "admin_message" => $admin_comment,
                "status" => 0
            ];
            $updateData["fee_percent"] = (float)$payment->getFeePercent();
            $updateData["fee"] = (float)$payment->getFee();
            $updateData["fee_by"] = $payment->getFeeBy();
            if ($updateData['fee_by'] == 'all') {
                $updateData['fee'] = (float)$updateData['fee'] + (float)$updateData['amount'] * (float)$updateData['fee_percent'] / 100;
            } elseif ($updateData['fee_by'] == 'by_fixed') {
                $updateData["fee"] = (float)$payment->getFee();
            } else {
                $updateData['fee'] = (float)$updateData['amount'] * (float)$updateData['fee_percent'] / 100;
            }
            $updateData['net_amount'] = (float)$updateData['amount'] - (float)$updateData['fee'];

            try {
                $model->setData($updateData);
                $model->save();
                return $model;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                throw new \Exception($e->getMessage());
            } catch (\RuntimeException $e) {
                throw new Exception($e->getMessage());
            } catch (\Exception $e) {
                throw new Exception(__('Something went wrong while saving the withdrawl request.'));
            }
        }
        return false;
    }

    /**
     * get seller amount
     * @param int $seller_id
     * @return int|float
     */
    public function getSellerAmount($seller_id = 0)
    {
        if (!$seller_id) {
            return 0;
        }
        if ($this->_sellerBalance == null) {
            $balance = 0;
            $collection = $this->amountcollectionFactory->create()->addFieldToFilter("seller_id", $seller_id);
            //$collection->getSelect()->sort("main_table.updated_at DESC");
            $foundItem = $collection->getFirstItem();
            $balance = $foundItem ? (float)$foundItem->getAmount() : 0;
            $this->_sellerBalance = $balance;
        }
        return $this->_sellerBalance;
    }

}
