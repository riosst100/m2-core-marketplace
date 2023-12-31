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

use Magento\Backend\App\Action;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

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
     * @var int|float|null
     */
    protected $_sellerBalance = null;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerModelFactory
     * @param \Lof\MarketPlace\Model\ResourceModel\SellerFactory $resourceFactory
     * @param \Lof\MarketPlace\Model\ResourceModel\Amount\CollectionFactory $amountcollectionFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Lof\MarketPlace\Model\SellerFactory $sellerModelFactory,
        \Lof\MarketPlace\Model\ResourceModel\SellerFactory $resourceFactory,
        \Lof\MarketPlace\Model\ResourceModel\Amount\CollectionFactory $amountcollectionFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->sellerModelFactory = $sellerModelFactory;
        $this->resourceFactory = $resourceFactory;
        $this->amountcollectionFactory = $amountcollectionFactory;
        parent::__construct($context);
    }

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Lof_MarketPlace::withdrawal')
            ->addBreadcrumb(__('Withdrawal'), __('Withdrawal'))
            ->addBreadcrumb(__('Manage Withdrawals'), __('Manage Withdrawals'));
        return $resultPage;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('withdrawal_id');
        $seller_id = $this->getRequest()->getParam('seller_id');
        $model = $this->_objectManager->create(\Lof\MarketPlace\Model\Withdrawal::class);

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This withdrawal no longer exits.'));
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        } elseif ($seller_id) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $seller = $this->sellerModelFactory->create();
            $this->resourceFactory->create()->load($seller, (int)$seller_id);
            if (!$seller->getId() || (int)$seller->getStatus() != \Lof\MarketPlace\Model\Seller::STATUS_ENABLED) {
                $this->messageManager->addErrorMessage(__('Can not create withdrawl request because seller is no longer exists. Seller ID: %1', $seller_id));
                return $resultRedirect->setPath('*/*/');
            }
            $model->setSellerId($seller_id);
            $model->setEmail($seller->getEmail());
            $amount = $this->getSellerAmount($seller_id);
            if ((float)$amount <= 0) {
                $this->messageManager->addErrorMessage(__('Can not create withdrawl request because seller amount is not available. Seller ID: %1', $seller_id));
                return $resultRedirect->setPath('*/*/');
            }
            $model->setAmount($amount);
        }

        $data = $this->_objectManager->get(\Magento\Backend\Model\Session::class)->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $this->_coreRegistry->register('lof_marketplace_withdrawal', $model);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Withdrawal') : __('New Withdrawal'),
            $id ? __('Edit Withdrawal') : __('New Withdrawal')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Withdrawals'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? $model->getname() : __('New Withdrawal'));

        return $resultPage;
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

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_MarketPlace::withdrawal_edit');
    }
}
