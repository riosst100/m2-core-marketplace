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

use Lof\Quickrfq\Helper\Data;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class ListQuote
 * @package Lof\Quickrfq\Controller\Quickrfq
 */
class ListQuote extends Action
{
    /**
     * @var PageFactory
     */
    protected $_pageFactory;
    /**
     * @var Session
     */
    protected $_customerSession;
    /**
     * @var Data
     */
    protected $_helper;
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $_urlInterface;

    /**
     * ListQuote constructor.
     * @param Context $context
     * @param PageFactory $pageFactory
     * @param Data $data
     * @param Session $session
     * @param PageFactory $resultPageFactory
     * @param \Magento\Framework\UrlInterface $urlInterface
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        Data $data,
        Session $session,
        PageFactory $resultPageFactory,
        \Magento\Framework\UrlInterface $urlInterface
    ) {
        $this->_pageFactory = $pageFactory;
        $this->_customerSession = $session;
        $this->_helper = $data;
        $this->resultPageFactory = $resultPageFactory;
        $this->_urlInterface     = $urlInterface;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $this->preInit();
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('All Requested Quotes'));
        return $resultPage;
    }

    /**
     * @return mixed
     */
    public function preInit()
    {
        if (! $this->_helper->isEnabled()) {
            $norouteUrl = $this->_url->getUrl('noroute');
            return $this->getResponse()->setRedirect($norouteUrl);
        }
        $customerSession = $this->_customerSession;
        if (! $customerSession->isLoggedIn()) {
            $customerSession->setAfterAuthUrl($this->_urlInterface->getCurrentUrl());
            $customerSession->authenticate();
        }
    }
}
