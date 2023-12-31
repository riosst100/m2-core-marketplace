<?php

namespace Lofmp\Faq\Controller\Marketplace\FaqQuestion;

class Delete extends \Magento\Framework\App\Action\Action
{

    protected $_coreRegistry = null;
    protected $_session;
    protected $_frontendUrl;
    protected $_helper;
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
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_session       = $customerSession;
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
            $currentQuestionId = $currentQuestion->getId();
            $sellerId = $this->_helper->getSellerId();
            $currentQuestionSellerId = $currentQuestion->getSellerId();
            if (!$currentQuestionId || $currentQuestionSellerId != $sellerId) {
                $this->messageManager->addError(__('This question no longer exists.'));
                return $this->_redirect('catalog/faqquestion/index');
            } else {
                $currentQuestion->delete();
                $this->messageManager->addSuccess(__('Question deleted successfully.'));
                return $this->_redirect('catalog/faqquestion/index');
            }
        } else {
            return $this->_redirect('catalog/faqquestion/index');
        }
    }
}
