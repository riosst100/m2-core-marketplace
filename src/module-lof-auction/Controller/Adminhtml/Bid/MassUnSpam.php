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

namespace Lof\Auction\Controller\Adminhtml\Bid;

use Lof\Auction\Controller\Adminhtml\Bid;
use Lof\Auction\Model\ResourceModel\Amount\CollectionFactory;
use Lof\Auction\Model\ResourceModel\MixAmount\CollectionFactory as MixAmountCollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;
use Magento\Ui\Component\MassAction\Filter;
use Lof\Auction\Model\MixAmountFactory;

/**
 * Class MassUnSpam
 * @package Lof\Auction\Controller\Adminhtml\Bid
 */
class MassUnSpam extends Bid
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
     * @var MixAmountFactory
     */
    private $mixAmountFactory;

    /**
     * @var MixAmountCollectionFactory
     */
    private $mixCollectionFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param Registry $coreRegistry
     * @param MixAmountFactory $mixAmountFactory
     * @param MixAmountCollectionFactory $mixCollectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        Registry $coreRegistry,
        MixAmountFactory $mixAmountFactory,
        MixAmountCollectionFactory $mixCollectionFactory
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->mixAmountFactory = $mixAmountFactory;
        $this->mixCollectionFactory = $mixCollectionFactory;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Delete action
     *
     * @return Redirect
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();
        foreach ($collection as $bid) {
            $bid->setIsSpam(0);
            $bid->save();

            $this->updateMixAmount($bid, 0);
        }

        $this->messageManager->addSuccess(__('A total of %1 record(s) have been un spam.', $collectionSize));

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * update mix amount for bid
     *
     * @param \Lof\Auction\Model\AutoAuction
     * @param int $status
     * @return void
     */
    protected function updateMixAmount($bid, $status = 0)
    {
        try {
            $foundItem = $this->mixCollectionFactory->create()
                            ->addFieldToFilter("bid_id", (int)$bid->getId())
                            ->getFirstItem();
            if ($foundItem && $foundItem->getId()) {
                $mixAmount = $this->mixAmountFactory->create()->load($foundItem->getEntityId());
                $mixAmount->setIsSpam((int)$status);
                $mixAmount->save();
            }
        } catch (\Exception $e) {
            //
        }
    }
    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_Auction::bid_save');
    }
}
