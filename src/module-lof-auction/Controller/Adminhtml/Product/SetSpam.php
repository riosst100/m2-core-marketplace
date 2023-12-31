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
 * @package    Lof_Auction
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\Auction\Controller\Adminhtml\Product;

use Lof\Auction\Helper\Data;
use Lof\Auction\Model\AmountFactory;
use Lof\Auction\Model\AutoAuctionFactory;
use Lof\Auction\Model\HistoryFactory;
use Lof\Auction\Model\MixAmountFactory;
use Lof\Auction\Model\ProductFactory;
use Lof\Auction\Model\ResourceModel\Amount\CollectionFactory;
use Lof\Auction\Model\ResourceModel\AutoAuction\CollectionFactory as AutoCollection;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class SetSpam
 * @package Lof\Auction\Controller\Adminhtml\Product
 */
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
        Data $helper
    ) {
        $this->historyFactory = $historyFactory;
        $this->amountCollection = $amountCollection;
        $this->productFactory = $productFactory;
        $this->amoutFactory = $amountFactory;
        $this->amountAutoFactory = $amountAuto;
        $this->autoCollection = $autoCollection;
        $this->mixAmountFactory = $mixAmountFactory;
        $this->helperData = $helper;
        parent::__construct($context);
    }


    /**
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $status = $this->getRequest()->getParam('status');
        if ($id) {
            try {
                $model = $this->historyFactory->create()->load($id);
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
                    //$amount->setIsSpam(0);
                    $amount->setIsSpam(1);
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
                            ->setIsSpam(1);
                            //->setIsSpam(0);
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
            } catch (\Exception $e) {
                // display error message
                // go back to edit form
            }
        }
    }


    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_Auction::set_spam');
    }
}
