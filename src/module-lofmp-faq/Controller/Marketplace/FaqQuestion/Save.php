<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_Faq
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\Faq\Controller\Marketplace\FaqQuestion;

class Save extends \Magento\Framework\App\Action\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    protected $_session = null;

    protected $_sellerFactory = null;

    protected $_helper = null;

    protected $_storeManager = null;

    const FLAG_IS_URLS_CHECKED = 'check_url_settings';

    protected $_frontendUrl;


    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Customer\Model\Session $sellerSession,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Lof\MarketPlace\Helper\Data $helper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Url $frontendUrl
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_session = $sellerSession;
        $this->_helper = $helper;
        $this->_sellerFactory = $sellerFactory;
        $this->_frontendUrl = $frontendUrl;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    public function getFrontendUrl($route = '', $params = [])
    {
        return $this->_frontendUrl->getUrl($route, $params);
    }

    protected function _redirectUrl($url)
    {
        $this->getResponse()->setRedirect($url);
        $this->_session->setIsUrlNotice($this->_actionFlag->get('', self::FLAG_IS_URLS_CHECKED));
        return $this->getResponse();
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $customerSession = $this->_session;
        if (!$customerSession->isLoggedIn()) {
            $this->messageManager->addNotice(__('You must have a seller account to access'));
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/login'));
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $sellerId = $this->_session->getId();
        $status = $this->_sellerFactory->create()->load($sellerId, 'customer_id')->getStatus();

        if ($this->_session->isLoggedIn() && $status == 1) {
            $this->_view->loadLayout();
            $this->_view->renderLayout();
        } elseif ($this->_session->isLoggedIn() && $status == 0) {
            $this->_redirect('lofmarketplace/seller/becomeseller');
        } else {
            $this->messageManager->addNotice(__('You must have a seller account to access'));
            $this->_redirect('lofmarketplace/seller/login');
        }

        $data = $this->getRequest()->getPostValue();

        if ($data) {
            if (!isset($data['question_id'])) {
                if (!isset($data['category_id']) || !$data['category_id']) {
                    $this->messageManager->addError(__('Please choose or create a new catetory.'));
                    $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData($data);
                    return $this->_redirect('catalog/faqquestion/new');
                }
                $model = $this->_objectManager->create('Lofmp\Faq\Model\Question');
                $store_id = $this->_storeManager->getStore()->getStoreId();
                $seller_id = $this->_helper->getSellerId();
                $data['store_id'] = $store_id;
                $data['seller_id'] = $seller_id;
                $this->_coreRegistry->register('seller_id', $seller_id);
                $model->setData($data);
            } else {
                $id = $data['question_id'];
                if (!isset($data['category_id']) || !$data['category_id']) {
                    $this->messageManager->addError(__('Please choose or create a new catetory.'));
                    $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData($data);
                    return $this->_redirect('catalog/faqquestion/edit', ['id' => $id]);
                }
                $model = $this->_objectManager->create('Lofmp\Faq\Model\Question')->load($id);
                $sellerId = $this->_helper->getSellerId();
                if (!$model->getId() || $model->getSellerId() != $sellerId) {
                    $this->messageManager->addError(__('This question no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
                $model->setData($data);
            }
            try {
                $model->save();
                $this->messageManager->addSuccess(__('You saved the question.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                return $this->_redirect('catalog/faqquestion/index');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData($data);
                return $this->_redirect('catalog/faqquestion/new');
            }
        }
        return $resultRedirect->setPath('catalog/faqquestion/index');
    }
}
