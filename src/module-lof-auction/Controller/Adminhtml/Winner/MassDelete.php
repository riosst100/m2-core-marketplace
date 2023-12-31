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
namespace Lof\Auction\Controller\Adminhtml\Winner;

use Lof\Auction\Controller\Adminhtml\Bid;
use Lof\Auction\Model\ProductFactory;
use Lof\Auction\Model\ResourceModel\Product\Source\AuctionStatus;
use Lof\Auction\Model\ResourceModel\WinnerData\CollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class MassDelete
 * @package Lof\Auction\Controller\Adminhtml\Winner
 */
class MassDelete extends Bid
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Lof\Auction\Model\ResourceModel\AutoAuction\CollectionFactory
     */
    protected $autoauctionCollectionFactory;
    /**
     * @var \Lof\Auction\Model\ResourceModel\Amount\CollectionFactory
     */
    protected $amountCollectionFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param \Lof\Auction\Model\ResourceModel\Amount\CollectionFactory $amountCollectionFactory
     * @param \Lof\Auction\Model\ResourceModel\AutoAuction\CollectionFactory $autoauctionCollectionFactory
     * @param ProductFactory $productFactory
     * @param Registry $coreRegistry
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        \Lof\Auction\Model\ResourceModel\Amount\CollectionFactory $amountCollectionFactory,
        \Lof\Auction\Model\ResourceModel\AutoAuction\CollectionFactory $autoauctionCollectionFactory,
        ProductFactory $productFactory,
        Registry $coreRegistry
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->productFactory = $productFactory;
        $this->amountCollectionFactory = $amountCollectionFactory;
        $this->autoauctionCollectionFactory = $autoauctionCollectionFactory;
        parent::__construct($context, $coreRegistry);
    }
    /**
     * Delete action
     *
     * @return Redirect
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();
        foreach ($collection as $winner) {
            $auction_id = $winner->getAuctionId();
            $customer_id = $winner->getCustomerId();
            //Update auction product: reset winner data
            $product = $this->productFactory->create()->load((int)$auction_id);
            if ($product->getId()) {
                $product->setAuctionStatus(AuctionStatus::STATUS_PROCESS);
                $product->setData("customer_id", 0);
                $product->save();
            }
            //Update auction bid: reset winner data
            $manualCollection = $this->amountCollectionFactory->create()
                                    ->addFieldToFilter("auction_id", $auction_id)
                                    ->addFieldToFilter("customer_id", $customer_id)
                                    ->addFieldToFilter("winning_status", AuctionStatus::STATUS_PROCESS)
                                    ->addFieldToFilter("status", AuctionStatus::STATUS_TIME_END);

            if ($manualCollection->getSize()) {
                foreach ($manualCollection as $manualBid) {
                    $manualBid->setStatus(AuctionStatus::STATUS_PROCESS);
                    $manualBid->setWinningStatus(AuctionStatus::STATUS_TIME_END);
                    $manualBid->save();
                }
            } else {
                $autoCollection = $this->autoauctionCollectionFactory->create()
                                    ->addFieldToFilter("auction_id", $auction_id)
                                    ->addFieldToFilter("customer_id", $customer_id)
                                    ->addFieldToFilter("flag", AuctionStatus::STATUS_PROCESS)
                                    ->addFieldToFilter("status", AuctionStatus::STATUS_TIME_END);

                if ($autoCollection->getSize()) {
                    foreach ($autoCollection as $autoBid) {
                        $autoBid->setStatus(AuctionStatus::STATUS_PROCESS);
                        $autoBid->setFlag(AuctionStatus::STATUS_TIME_END);
                        $autoBid->setWinningPrice(0);
                        $autoBid->save();
                    }
                }
            }

            $winner->delete();
        }

        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $collectionSize));

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_Auction::winner_delete');
    }
}
