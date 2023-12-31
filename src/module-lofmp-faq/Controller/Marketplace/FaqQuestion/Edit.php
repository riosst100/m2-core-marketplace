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

class Edit extends \Magento\Framework\App\Action\Action
{

    protected $_coreRegistry = null;
    protected $_session;
    protected $_frontendUrl;
    protected $_helper;
    protected $_productCollection;
    const FLAG_IS_URLS_CHECKED = 'check_url_settings';
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Url $frontendUrl,
        \Lof\MarketPlace\Helper\Data $helper,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collection,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_session       = $customerSession;
        $this->_productCollection   = $collection->create();
        $this->_frontendUrl  = $frontendUrl;
        $this->_helper = $helper;
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

    public function execute()
    {
        $customerSession = $this->_session;
        if(!$customerSession->isLoggedIn()) {
            $this->messageManager->addNotice(__( 'You must have a seller account to access' ) );
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/login'));
        }

        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Lofmp\Faq\Model\Question');

        if ($id) {
            $currentQuestion = $model->load($id);
            $currentQuestionId = $currentQuestion->getQuestionId();
            $sellerId = $this->_helper->getSellerId();
            $currentQuestionSellerId = $currentQuestion->getSellerId();

            if (!$currentQuestionId || $currentQuestionSellerId != $sellerId) {
                $this->messageManager->addError(__('This question no longer exists.'));
                return $this->_redirect('catalog/faqquestion/index');
            } else {
                $this->_coreRegistry->register('mpfaq_currentQuestion', $currentQuestion);

                $sellerId = $this->_helper->getSellerId();

                $productCollection = $this->_productCollection->addAttributeToSelect('*')
                                          ->addAttributeToFilter('seller_id', ['eq' => $sellerId])
                                          ->load();

                $this->_coreRegistry->register('mpfaq_productList', $productCollection);

                $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
                if (!empty($data)) {
                    $model->setData($data);
                }

                $this->_view->loadLayout();
                $this->_view->renderLayout();
            }
        } else {
            return $this->_redirect('catalog/faqquestion/index');
        }
    }
}
