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
namespace Lof\Auction\Controller\Adminhtml\Autobid;

use Lof\Auction\Controller\Adminhtml\Autobid;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Lof\Auction\Model\ResourceModel\AutoAuction\CollectionFactory;
use Lof\Auction\Model\ResourceModel\MixAmount\CollectionFactory as MixCollectionFactory;
use Magento\Framework\Registry;

/**
 * Class MassDelete
 * @package Lof\Auction\Controller\Adminhtml\Autobid
 */
class MassDelete extends Autobid
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
     * @var MixCollectionFactory
     */
    protected $mixCollectionFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param Registry $coreRegistry
     * @param MixCollectionFactory $mixCollectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        Registry $coreRegistry,
        MixCollectionFactory $mixCollectionFactory
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->mixCollectionFactory = $mixCollectionFactory;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Delete action
     *
     * @return Redirect
     * @throws LocalizedException
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();
        $bidIds = [];

        foreach ($collection as $autobid) {
            $bidIds[] = $autobid->getEntityId();
            $autobid->delete();
        }
        /** also delete mix bids */
        $mixCollection = $this->mixCollectionFactory->create()
                                ->addFieldToFilter("is_auto", 1)
                                ->addFieldToFilter("bid_id", ["in" => $bidIds]);

        foreach ($mixCollection as $mixbid) {
            $mixbid->delete();
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
        return $this->_authorization->isAllowed('Lof_Auction::autobid_delete');
    }
}
