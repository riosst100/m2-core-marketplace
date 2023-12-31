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

namespace Lof\Quickrfq\Controller\Quickrfq;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\RequestInterface;

/**
 * Class View
 * @package Lof\Quickrfq\Controller\Quickrfq
 */
class View extends Action implements HttpGetActionInterface, HttpPostActionInterface
{

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $_urlInterface;
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $_session;
    /**
     * @var PageFactory
     */
    private $resultPageFactory;
    /**
     * @var \Lof\Quickrfq\Helper\Data
     */
    private $_helperConfig;
    /**
     * @var \Lof\Quickrfq\Model\QuickrfqFactory
     */
    private $_quoteFactory;
    /**
     * @var Registry
     */
    private $_coreRegistry;
    /**
     * @var mixed|LoggerInterface|null
     */
    private $logger;

    /**
     * View constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param \Lof\Quickrfq\Model\QuickrfqFactory $quoteFactory
     * @param \Magento\Customer\Model\Session $session
     * @param \Lof\Quickrfq\Helper\Data $helperConfig
     * @param \Magento\Framework\UrlInterface $urlInterface
     * @param Registry $coreRegistry
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Lof\Quickrfq\Model\QuickrfqFactory $quoteFactory,
        \Magento\Customer\Model\Session $session,
        \Lof\Quickrfq\Helper\Data $helperConfig,
        \Magento\Framework\UrlInterface $urlInterface,
        Registry $coreRegistry,
        LoggerInterface $logger = null
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_quoteFactory     = $quoteFactory;
        $this->_session          = $session;
        $this->_helperConfig     = $helperConfig;
        $this->_urlInterface     = $urlInterface;
        $this->_coreRegistry     = $coreRegistry;
        $this->logger            = $logger ?: ObjectManager::getInstance()
            ->get(LoggerInterface::class);
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $quote = $this->_initQuote();
        $resultRedirect = $this->resultRedirectFactory->create();
        try {

            if (!$quote) {
                throw new \Exception(__('The quote does not exist!'));
            }

            if ($quote->getCustomerId() != $this->_session->getCustomerId()) {
                throw new \Exception(__('You cannot view this quote.'));
            }

            $resultPage = $this->resultPageFactory->create();
            $resultPage->getConfig()->getTitle()->set(__('My Request Quote Detail - SKU: %1', $quote->getProductSku()));

        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());

            return $resultRedirect->setPath('*/*/listquote');
        }

        return $this->resultPageFactory->create();
    }

    /**
     * @return bool
     */
    protected function _initQuote()
    {
        $quoteId = $this->getRequest()->getParam('quickrfq_id');
        if (! $quoteId) {
            return false;
        }
        $quote = $this->_quoteFactory->create()->load($quoteId);

        if (empty($quote->getId())) {
            return false;
        }
        $this->_coreRegistry->register('current_quote', $quote);
        return $quote;
    }

    /**
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface|void|null
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        if (! $this->isEnabled()) {
            $norouteUrl = $this->_urlInterface->getUrl('noroute');
            return $this->getResponse()->setRedirect($norouteUrl);
        }


        $customerSession = $this->_session;
        if (! $customerSession->isLoggedIn()) {
            $customerSession->setAfterAuthUrl($this->_urlInterface->getCurrentUrl());
            $customerSession->authenticate();
            return;
        }
        return parent::dispatch($request); // TODO: Change the autogenerated stub
    }


    /**
     * Is enabled?
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->_helperConfig->getConfig('option/enabled_module');
    }
}
