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

use Lof\Auction\Controller\Adminhtml\Product;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Edit
 * @package Lof\Auction\Controller\Adminhtml\Product
 */
class Edit extends Product
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param ProductRepository $productRepository
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        ProductRepository $productRepository
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->productRepository = $productRepository;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_Auction::product_edit');
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
        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Lof\Auction\Model\Product');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This Auction no longer exists.'));
                /** @var Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        } else {
            // 3. Set entered data if was error when we do save
            $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
        }

        // 4. Register model to use later in forms
        $this->_coreRegistry->register('lofauction_product', $model);
        $resultPage = $this->resultPageFactory->create();
        // 5. Build edit form
        $this->initPage($resultPage)->addBreadcrumb(
            __('New Auction'),
            __('New Auction')
        );
        if ($id) {
            $product = $this->productRepository->getById($model->getProductId());

            $resultPage->getConfig()->getTitle()->prepend(__('Auction'));
            $resultPage->getConfig()->getTitle()->prepend(__('Edit Auction for %1', $product->getName()));
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('Auction'));
            $resultPage->getConfig()->getTitle()->prepend(__('New Auction'));
        }
        return $resultPage;
    }
}
