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

namespace Lofmp\Faq\Controller\Marketplace\FaqCategory;

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

    protected $_sellerHelper = null;

    protected $_storeManager = null;

    protected $_frontendUrl;

    const FLAG_IS_URLS_CHECKED = 'check_url_settings';

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
    )
    {
        $this->_coreRegistry = $coreRegistry;
        $this->_session = $sellerSession;
        $this->_sellerHelper = $helper;
        $this->_sellerFactory = $sellerFactory;
        $this->_frontendUrl     = $frontendUrl;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    public function getFrontendUrl($route = '', $params = []){
        return $this->_frontendUrl->getUrl($route,$params);
    }

    protected function _redirectUrl($url){
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
        if(!$customerSession->isLoggedIn()) {
            $this->messageManager->addNotice(__( 'You must have a seller account to access' ) );
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/login'));
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $sellerId = $this->_session->getId();
        $status = $this->_sellerFactory->create()->load($sellerId,'customer_id')->getStatus();

        if ($this->_session->isLoggedIn() && $status == 1) {
            $this->_view->loadLayout();
            $this->_view->renderLayout();
        } elseif($this->_session->isLoggedIn() && $status == 0) {
            $this->_redirect( 'lofmarketplace/seller/becomeseller' );
        } else {
            $this->messageManager->addNotice(__( 'You must have a seller account to access' ) );
            $this->_redirect( 'lofmarketplace/seller/login' );
        }

        $data = $this->getRequest()->getPostValue();

        if ($data) {
            if(!isset($data['category_id'])) {
                $model = $this->_objectManager->create('Lofmp\Faq\Model\Category');
                $store_id = $this->_storeManager->getStore()->getStoreId();
                $seller_id = $this->_sellerHelper->getSellerId();
                $data['store_id'] = $store_id;
                $data['seller_id'] = $seller_id;
                $this->_coreRegistry->register('seller_id', $seller_id);
                $model->setData($data);
            } else {
                $id = $data['category_id'];
                $model = $this->_objectManager->create('Lofmp\Faq\Model\Category')->load($id);
                $sellerId = $this->_sellerHelper->getSellerId();
                if (!$model->getId() || $model->getSellerId() != $sellerId) {
                    $this->messageManager->addError(__('This category no longer exists.'));
                    return $this->_redirect('catalog/faqcategory/index');
                }
                $parentId = $data['parent_id'];
                $parentCategory = $this->_objectManager->create('Lofmp\Faq\Model\Category')->load($parentId);
                $categoryTitle = $model->getTitle();
                $parentCategoryTitle = $parentCategory->getTitle();

                if($parentCategory->getParentId() == $id){
                    $this->messageManager->addError(__("'" . $parentCategoryTitle . "' belong to '" . $categoryTitle . "'"));
                    return $this->_redirect('catalog/faqcategory/edit/id/' . $id);
                }

                $model->setData($data);
            }

           try {
                $model->save();
                $this->messageManager->addSuccess(__('You saved the category.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                return $this->_redirect('catalog/faqcategory/index');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData($data);
                return $this->_redirect('catalog/faqcategory/new');
            }
        }
        return $resultRedirect->setPath('catalog/faqcategory/index');
    }
}
