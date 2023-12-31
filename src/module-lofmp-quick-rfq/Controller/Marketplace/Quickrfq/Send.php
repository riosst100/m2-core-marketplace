<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Landofcoder
 * @package     Lofmp_Quickrfq
 * @copyright   Copyright (c) 2020 Landofcoder (https://landofcoder.com/)
 * @license     https://landofcoder.com/LICENSE.txt
 */

namespace Lofmp\Quickrfq\Controller\Marketplace\Quickrfq;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Lof\Quickrfq\Model\QuickrfqFactory;
use Lof\Quickrfq\Model\MessageFactory;
use Lof\Quickrfq\Helper\Data;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\UrlInterface;

/**
 * Class Send
 * @package Lofmp\Quickrfq\Controller\Marketplace\Quickrfq
 */
class Send extends \Magento\Framework\App\Action\Action
{
    const FLAG_IS_URLS_CHECKED = 'check_url_settings';
    /**
     * @var Session
     */
    private $session;
    /**
     * @var QuickrfqFactory
     */
    private $quickrfq;
    /**
     * @var MessageFactory
     */
    private $message;
    /**
     * @var Data
     */
    private $helper;
    /**
     * @var UrlInterface
     */
    private $_urlInterface;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    private $helperSeller;

    /**
     * @var \Magento\Framework\Url
     */
    protected $_frontendUrl;

    /**
     * Send constructor.
     * @param Context $context
     * @param Session $session
     * @param QuickrfqFactory $quickrfq
     * @param Data $data
     * @param MessageFactory $messageFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param UrlInterface $urlInterface
     * @param \Magento\Framework\Url $frontendUrl
     */
    public function __construct(
        Context $context,
        Session $session,
        QuickrfqFactory $quickrfq,
        Data $data,
        MessageFactory $messageFactory,
        ScopeConfigInterface $scopeConfig,
        \Lof\MarketPlace\Helper\Data $helper,
        UrlInterface $urlInterface,
        \Magento\Framework\Url $frontendUrl
    ) {
        $this->session = $session;
        $this->helper = $data;
        $this->quickrfq = $quickrfq;
        $this->scopeConfig = $scopeConfig;
        $this->message = $messageFactory;
        $this->helperSeller = $helper;
        $this->_urlInterface     = $urlInterface;
        $this->_frontendUrl = $frontendUrl;
        $this->_actionFlag =  $context->getActionFlag();
        parent::__construct($context);
    }


    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('*/*/index');
        $id = $this->getRequest()->getParam('quickrfq_id');
        $sellerId = $this->helperSeller->getSellerId();
        try {
            if (!$this->isQuoteExist($id)) {
                $this->messageManager->addError(__('This Quote no longer exists.'));
                $this->_redirectUrl($this->getFrontendUrl('marketplace/quickrfq/quickrfq'));
            }

            if (!$this->getRequest()->isPost() || !$data) {
                $this->messageManager->addError(__('Somethings went wrong while send this message.'));
                $this->_redirectUrl($this->getFrontendUrl('marketplace/quickrfq/quickrfq'));
            }

            if ($this->getQuote($id)->getSellerId() != $sellerId) {
                $this->messageManager->addError(__('You cannot send the message for this quote.'));
                $this->_redirectUrl($this->getFrontendUrl('marketplace/quickrfq/quickrfq'));
            }

            if (!isset($data['message']) && strlen($data['message']) >= 3) {
                $this->messageManager->addError(__('Please fill message box to send this message.'));
                $this->_redirectUrl($this->getFrontendUrl('marketplace/quickrfq/quickrfq').'view/quickrfq_id/'.$id);
            }

            $model = $this->message->create();
            $data['message'] = strip_tags($data['message']);
            $data['message'] = $this->helper->xss_clean($data['message']);
            $data['quickrfq_id'] = $id;

            $model->setData($data);
            $model->save();

            $quote = $this->getQuote($id);
            if ($quote->getStatus() == \Lof\Quickrfq\Model\Quickrfq::STATUS_NEW) {
                $quote->setStatus(\Lof\Quickrfq\Model\Quickrfq::STATUS_PROCESSING);
                $quote->save();
            }
            $customer = $this->session->getCustomer();

            $dataSender = $data;
            $dataSender['template'] = $this->helper::EMAIL_TEMPLATE_NOTICE_SENDER;
            $dataSender['receiver_email'] = $customer->getEmail();
            $dataSender['receiver'] = $quote->getContactName();
            $this->helper->sendMailNotice($dataSender);

            $dataReceiver = $data;
            $dataReceiver['template'] = $this->helper::EMAIL_TEMPLATE_NOTICE_RECEIVER;
            $dataReceiver['receiver_email'] = $quote->getEmail();
            $dataReceiver['sender_name'] = $customer->getName();
            $this->helper->sendMailNotice($dataReceiver);

            $this->messageManager->addSuccessMessage(__('Send message successfully'));

            $this->_redirectUrl($this->getFrontendUrl('marketplace/quickrfq/quickrfq').'view/quickrfq_id/'.$id);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());

            $this->_redirectUrl($this->getFrontendUrl('marketplace/quickrfq/quickrfq'));
        }
    }

    /**
     * @param $quoteId
     * @return mixed
     */
    public function getQuote($quoteId)
    {
        return $this->quickrfq->create()->load($quoteId);
    }

    /**
     * @param $quoteId
     * @return bool
     */
    public function isQuoteExist($quoteId)
    {
        return $this->getQuote($quoteId)->getData() ? true : false;
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return ResponseInterface|void|null
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        if (! $this->helper->isEnabled()) {
            $norouteUrl = $this->_url->getUrl('noroute');
            return $this->getResponse()->setRedirect($norouteUrl);
        }
        $customerSession = $this->session;
        if (! $customerSession->isLoggedIn()) {
            $customerSession->setAfterAuthUrl($this->_urlInterface->getCurrentUrl());
            $customerSession->authenticate();
            return;
        }
        return parent::dispatch($request);
    }

    /**
     * @param string $route
     * @param array $params
     * @return mixed
     */
    public function getFrontendUrl($route = '', $params = [])
    {
        return $this->_frontendUrl->getUrl($route, $params);
    }

    /**
     * @param $url
     * @return ResponseInterface
     */
    protected function _redirectUrl($url)
    {
        $this->getResponse()->setRedirect($url);
        $this->session->setIsUrlNotice($this->_actionFlag->get('', self::FLAG_IS_URLS_CHECKED));
        return $this->getResponse();
    }
}
