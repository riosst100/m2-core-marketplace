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
 * @package    Lof_Quickrfq
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
namespace Lof\Quickrfq\Controller\Adminhtml\Index;

use Lof\Quickrfq\Model\QuickrfqFactory;
use Magento\Backend\App\Action;
use Magento\Backend\Model\Session;
use Magento\Backend\Model\View\Result\Page;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Edit
 * @package Lof\Quickrfq\Controller\Adminhtml\Index
 */
class Edit extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var QuickrfqFactory
     */
    private $quickrfq;
    /**
     * @var Session
     */
    private $session;

    /**
     * @param Action\Context $context
     * @param PageFactory $resultPageFactory
     * @param QuickrfqFactory $quickrfq
     * @param Session $session
     * @param Registry $registry
     */
    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory,
        QuickrfqFactory $quickrfq,
        Session $session,
        Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->quickrfq = $quickrfq;
        $this->session = $session;
        $this->_coreRegistry = $registry;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_Quickrfq::quickrfq_view');
    }

    /**
     * Init actions
     *
     * @return Page
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Lof_Quickrfq::quickrfq')
            ->addBreadcrumb(__('RFQ'), __('RFQ'))
            ->addBreadcrumb(__('Manage RFQ'), __('Manage RFQ'));
        return $resultPage;
    }

    /**
     * Edit page
     *
     * @return Page|Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('quickrfq_id');
        $model = $this->quickrfq->create();

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This record no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        // 3. Set entered data if was error when we do save
        $data = $this->session->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in blocks
        $this->_coreRegistry->register('quickrfq', $model);

        // 5. Build edit form
        /** @var Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit RFQ') : __('New RFQ'),
            $id ? __('Edit RFQ') : __('New RFQ')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('RFQ'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? __('Quote #%1: Product "%2" from "%3"', $model->getQuickrfqId(), $model->getProductName(), $model->getContactName()) : __('New RFQ'));

        return $resultPage;
    }
}
