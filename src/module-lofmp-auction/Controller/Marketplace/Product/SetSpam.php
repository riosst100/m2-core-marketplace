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
 * @package    Lofmp_Auction
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\Auction\Controller\Marketplace\Product;

use Exception;
use Lof\Auction\Helper\Data;
use Lof\Auction\Model\AmountFactory;
use Lof\Auction\Model\AutoAuctionFactory;
use Lof\Auction\Model\HistoryFactory;
use Lof\Auction\Model\MixAmountFactory;
use Lof\Auction\Model\ProductFactory;
use Lof\Auction\Model\ResourceModel\Amount\CollectionFactory;
use Lof\Auction\Model\ResourceModel\AutoAuction\CollectionFactory as AutoCollection;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class SetSpam extends Action
{
    /**
     * @var HistoryFactory
     */
    private $historyFactory;
    /**
     * @var CollectionFactory
     */
    private $amountCollection;
    /**
     * @var ProductFactory
     */
    private $productFactory;
    /**
     * @var AmountFactory
     */
    private $amoutFactory;
    /**
     * @var AutoAuctionFactory
     */
    private $amountAutoFactory;
    /**
     * @var AutoCollection
     */
    private $autoCollection;
    /**
     * @var MixAmountFactory
     */
    private $mixAmountFactory;
    /**
     * @var Data
     */
    private $helperData;
    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    private $marketHelper;

    /**
     * SetSpam constructor.
     * @param Context $context
     * @param CollectionFactory $amountCollection
     * @param HistoryFactory $historyFactory
     * @param ProductFactory $productFactory
     * @param AmountFactory $amountFactory
     * @param AutoAuctionFactory $amountAuto
     * @param AutoCollection $autoCollection
     * @param MixAmountFactory $mixAmountFactory
     * @param Data $helper
     * @param \Lof\MarketPlace\Helper\Data $marketHelper
     */
    public function __construct(
        Context $context,
        CollectionFactory $amountCollection,
        HistoryFactory $historyFactory,
        ProductFactory $productFactory,
        AmountFactory $amountFactory,
        AutoAuctionFactory $amountAuto,
        AutoCollection $autoCollection,
        MixAmountFactory $mixAmountFactory,
        Data $helper,
        \Lof\MarketPlace\Helper\Data $marketHelper
    ) {
        $this->historyFactory = $historyFactory;
        $this->amountCollection = $amountCollection;
        $this->productFactory = $productFactory;
        $this->amoutFactory = $amountFactory;
        $this->amountAutoFactory = $amountAuto;
        $this->autoCollection = $autoCollection;
        $this->mixAmountFactory = $mixAmountFactory;
        $this->helperData = $helper;
        $this->marketHelper = $marketHelper;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     * @throws Exception
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $status = $this->getRequest()->getParam('status');
        if ($id) {
            $model = $this->historyFactory->create()->load($id);
            if (!$model->getData() || !$this->validate($model->getAuctionId())) {
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                return $resultRedirect;
            }
            $model->setStatus($status)->save();
            $auctionId = $model->getAuctionId();
            $customerId = $model->getCustomerId();
            $isAuto = $model->getIsAuto();
            $historyCollection = $this->historyFactory->create()->getCollection()
                ->addFieldToFilter('status', 1)
                ->addFieldToFilter('auction_id', $auctionId)
                ->addFieldToFilter('customer_id', $customerId)
                ->setOrder('auction_amount');
            if (!$isAuto) {
                $amount = $this->amoutFactory->create()->load($model->getAmountId());
            } else {
                $amount = $this->amountAutoFactory->create()->load($model->getAmountId());
            }
            $history = $historyCollection->getFirstItem();

            $newAmount = $historyCollection->addFieldToFilter('is_auto', $isAuto)
                ->getFirstItem()->getAuctionAmount();
            $auctionAmount = $history->getAuctionAmount();

            $auction = $this->productFactory->create()->load($auctionId);
            $mixCollection = $this->mixAmountFactory->create()->getCollection();
            $mixCollection->addFieldToFilter('customer_id', $customerId)
                ->addFieldToFilter('auction_id', $auctionId);
            $mixAmount = $mixCollection->getFirstItem();
            if ($newAmount) {
                if (!$isAuto) {
                    $amount->setAuctionAmount($newAmount);
                } else {
                    $amount->setAmount($newAmount);
                }
                $amount->setCreatedAt($history->getCreatedAt());
                $amount->setIsSpam(0);
                $amount->save();
            } else {
                $amount->setAuctionAmount(0);
                $amount->setAmount(0);
                $amount->setIsSpam(1);
                $amount->save();
            }

            $bestAmountHistory = $this->helperData->getBestCurrentBid($auctionId);

            if ($auctionAmount) {
                if ($mixAmount->getData()) {
                    $mixAmount->setAmount($auctionAmount)
                        ->setIsAuto($history->getData('is_auto'))
                        ->setBidId($history->getData('amount_id'))
                        ->setIsSpam(0);
                    $mixAmount->save();
                }

                $auction->setMinAmount($bestAmountHistory->getAuctionAmount());
                $auction->setCustomerId($bestAmountHistory->getCustomerId());

                $auction->save();
            } else {
                if ($mixAmount->getData()) {
                    $mixAmount->setAmount(0)
                        ->setIsSpam(1);
                    $mixAmount->save();
                }

                if ($bestAmountHistory->getData()) {
                    $minAmount = $bestAmountHistory->getAuctionAmount();
                    $customerId = $bestAmountHistory->getCustomerId();
                } else {
                    $minAmount = $auction->getStartingPrice();
                    $customerId = 0;
                }
                $auction->setMinAmount($minAmount)
                    ->setCustomerId($customerId);
                $auction->save();
            }
        }
    }

    /**
     * @param $auctionId
     * @return bool
     */
    public function validate ($auctionId) {
        $auction = $this->productFactory->create()->load($auctionId);
        $sellerId = $this->marketHelper->getSellerId();
        if ($auction->getSellerId() != $sellerId) {
            $this->messageManager->addErrorMessage(__('You don\'t have permission to set spam this bid'));
            return false;
        } else {
            return true;
        }
    }

}
