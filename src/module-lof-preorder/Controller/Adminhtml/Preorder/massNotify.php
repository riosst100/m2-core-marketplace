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
 * @package    Lof_PreOrder
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\PreOrder\Controller\Adminhtml\Preorder;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\View\Result\PageFactory;
use Lof\PreOrder\Model\ResourceModel\PreOrder\CollectionFactory;

class massNotify extends \Lof\PreOrder\Controller\Adminhtml\Preorder
{
     /**
      * @var \Lof\PreOrder\Helper\Data
      */
    protected $_preorderHelper;

    /**
     * @var CollectionFactory
     */
    protected $_preorderCollection;

    /**
     * @param Action\Context $context
     * @param \Lof\PreOrder\Helper\Data $preorderHelper
     * @param CollectionFactory $preorderCollection
     */
    public function __construct(
        Action\Context $context,
        \Lof\PreOrder\Helper\Data $preorderHelper,
        Filter $filter,
        CollectionFactory $preorderCollection
    ) {
        $this->filter = $filter;
        $this->_preorderHelper = $preorderHelper;
        $this->_preorderCollection = $preorderCollection;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_PreOrder::preorder_notify');
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $helper = $this->_preorderHelper;
        $info = $emailIds = [];

        $collection = $this->filter->getCollection($this->_preorderCollection->create());

        foreach ($collection as $item) {
            $info[] = $item->getProductId();
            $emailIds[] = $item->getCustomerEmail();
        }
        $collectionCount = count($collection);
        if ($collectionCount >= 1) {
            for ($i = 0; $i < $collectionCount; $i++) {
                $stockDetails = $helper->getStockDetails($info[$i]);
                $emailId = [];
                if ($stockDetails['is_in_stock'] == 1) {
                    $emailId[] = $emailIds[$i];
                    $helper->sendNotifyEmail($emailId, $stockDetails['name']);
                    $this->messageManager->addSuccess(__('Email sent succesfully.'));
                }
            }
        }

        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*/');
    }
}
