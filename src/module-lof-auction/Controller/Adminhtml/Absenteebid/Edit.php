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
namespace Lof\Auction\Controller\Adminhtml\Absenteebid;

use Lof\Auction\Controller\Adminhtml\Absenteebid;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Edit
 * @package Lof\Auction\Controller\Adminhtml\Absenteebid
 */
class Edit extends Absenteebid
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    protected $dataPersistor;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_Auction::absenteebid');
    }

    /**
     * Edit Auction Form
     *
     * @return ResultInterface
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {

        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('entity_id');
        $model = $this->_objectManager->create(\Lof\Auction\Model\MaxAbsenteeBid::class);

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This Absentee Bid no longer exists.'));
                /** @var Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
            $this->dataPersistor->set('lofauction_absenteebid', $model->getData());
        } else {
            // 3. Set entered data if was error when we do save
            $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
        }
        
        // 4. Register model to use later in forms
        $this->_coreRegistry->register('current_model', $model);
        $this->_coreRegistry->register('lofauction_absenteebid', $model);
        $this->_coreRegistry->register('lofauction_absenteebid_form', $model);
        $resultPage = $this->resultPageFactory->create();
        // 5. Build edit form
        $this->initPage($resultPage)->addBreadcrumb(
            __('New Absentee Bid'),
            __('New Absentee Bid')
        );
        if ($id) {
            $resultPage->getConfig()->getTitle()->prepend(__('Auction'));
            $resultPage->getConfig()->getTitle()->prepend(__('Edit Absentee Bid for %1', $model->getId()));
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('Auction'));
            $resultPage->getConfig()->getTitle()->prepend(__('New Absentee Bid'));
        }
        return $resultPage;
    }
}
