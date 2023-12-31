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

class NewAction extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    protected $session;

    protected $_productCollection;

    protected $_coreRegistry = null;


    /**
     *
     * @var Magento\Framework\View\Result\PageFactory
     */

    const FLAG_IS_URLS_CHECKED = 'check_url_settings';

    protected $_frontendUrl;

    /**
     * @var \Magento\Framework\App\ActionFlag
     */
    protected $_actionFlag;
    /**
     *
     * @var \Lof\MarketPlace\Model\SellerFactory
     */
    protected $sellerFactory;

    protected $_helper;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Lof\MarketPlace\Helper\Data $helper,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collection,
        \Magento\Framework\Url $frontendUrl
    ) {
        $this->_actionFlag          = $context->getActionFlag();
        $this->_coreRegistry        = $coreRegistry;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->session              = $customerSession;
        $this->sellerFactory        = $sellerFactory;
        $this->_helper              = $helper;
        $this->_productCollection   = $collection->create();
        $this->_frontendUrl         = $frontendUrl;
        parent::__construct($context);
    }

    public function getFrontendUrl($route = '', $params = []){
        return $this->_frontendUrl->getUrl($route,$params);
    }

    protected function _redirectUrl($url){
        $this->getResponse()->setRedirect($url);
        $this->session->setIsUrlNotice($this->_actionFlag->get('', self::FLAG_IS_URLS_CHECKED));
        return $this->getResponse();
    }

    /**
     * Create new Faq Question
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $customerSession = $this->session;
        if(!$customerSession->isLoggedIn()) {
            $this->messageManager->addNotice(__( 'You must have a seller account to access' ) );
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/login'));
        }

        $model = $this->_objectManager->create('Lofmp\Faq\Model\Question');
        $this->_coreRegistry->register('mpfaq_question', $model);

        $sellerId = $this->_helper->getSellerId();
        $productCollection = $this->_productCollection
                                  ->addAttributeToFilter('seller_id', ['eq' => $sellerId])
                                  ->load();
        $productList = [];
        foreach($productCollection as $item){
            $product = $this->_objectManager->create('\Magento\Catalog\Model\Product');
            $productList[] = $product->load($item->getEntityId());
        };
        $this->_coreRegistry->register('mpfaq_productList', $productList);
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
