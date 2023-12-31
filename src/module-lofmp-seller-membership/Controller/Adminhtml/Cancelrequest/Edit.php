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
 * @package    Lof_CustomerMembership
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerMembership\Controller\Adminhtml\Cancelrequest;

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
     * @param Action\Context
     * @param \Magento\Framework\View\Result\PageFactory
     * @param \Magento\Framework\Registry
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_CustomerMembership::cancelrequest');
    }

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Lof_CustomerMembership::cancelrequest')
                ->addBreadcrumb(__('Cancel Request'), __('Cancel Request'))
                ->addBreadcrumb(__('Manage Cancel Request'), __('Manage Cancel Request'));
        return $resultPage;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $entity_id = $this->getRequest()->getParam('entity_id');
        $model = $this->_objectManager->create('Lofmp\SellerMembership\Model\Cancelrequest');

        if ($entity_id) {
            $model->load($entity_id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This Cancel Request no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $this->_coreRegistry->register('lof_customermembership_cancelrequest', $model);

        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $entity_id ? __('Edit Cancel Request') : __('New Cancel Request'),
            $entity_id ? __('Edit Cancel Request') : __('New Cancel Request')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Cancel Request'));
        $resultPage->getConfig()->getTitle()
                ->prepend($model->getId() ? __('Edit Cancel Request') . $model->getName() : __('New Cancel Request'));

        return $resultPage;
    }
}
