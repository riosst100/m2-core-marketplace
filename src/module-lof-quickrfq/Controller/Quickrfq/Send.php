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

use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Lof\Quickrfq\Model\QuickrfqFactory;
use Lof\Quickrfq\Model\Quickrfq;
use Lof\Quickrfq\Model\MessageFactory;
use Lof\Quickrfq\Helper\Data;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;

/**
 * Class Send
 * @package Lof\Quickrfq\Controller\Quickrfq
 */
class Send extends Action
{
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
     * @var ProductFactory
     */
    private $productFactory;
    /**
     * @var Quickrfq[]
     */
    protected $_currentQuote = [];

    /**
     * Send constructor.
     * @param Context $context
     * @param Session $session
     * @param QuickrfqFactory $quickrfq
     * @param Data $data
     * @param MessageFactory $messageFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param ProductFactory $productFactory
     * @param ProductRepository $productRepository
     * @param UrlInterface $urlInterface
     */
    public function __construct(
        Context $context,
        Session $session,
        QuickrfqFactory $quickrfq,
        Data $data,
        MessageFactory $messageFactory,
        ScopeConfigInterface $scopeConfig,
        ProductFactory $productFactory,
        ProductRepository $productRepository,
        UrlInterface $urlInterface
    ) {
        $this->session = $session;
        $this->productFactory = $productFactory;
        $this->helper = $data;
        $this->quickrfq = $quickrfq;
        $this->scopeConfig = $scopeConfig;
        $this->message = $messageFactory;
        $this->_urlInterface     = $urlInterface;
        $this->productRepository  = $productRepository ?: ObjectManager::getInstance()->create(ProductRepository::class);

        parent::__construct($context);
    }


    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('*/*/listquote');
        $id = $this->getRequest()->getParam('quickrfq_id');

        try {
            if (!$this->isQuoteExist($id)) {
                throw new \Exception(__('The quote does not exist.'));
            }

            if (!$this->getRequest()->isPost() || !$data) {
                throw new \Exception(__('Somethings went wrong while send this message.'));
            }

            if ($this->getQuote($id)->getCustomerId() != $this->session->getCustomerId()) {
                throw new \Exception(__('You cannot send the message for this quote.'));
            }

            if (!isset($data['message'])) {
                throw new \Exception(__('Please fill message box to send this message.'));
            }

            if ($this->getQuote($id)->getStatus() == Quickrfq::STATUS_CLOSE || $this->getQuote($id)->getStatus() == Quickrfq::STATUS_DONE) {
                throw new \Exception(__('We can not send message when Quote was closed or done.'));
            }

            $model = $this->message->create();
            $data['message'] = strip_tags($data['message']);
            $data['message'] = $this->helper->xss_clean($data['message']);
            $data['quickrfq_id'] = $id;
            $data['customer_id'] = $this->session->getCustomerId();

            $model->setData($data);
            $model->save();

            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $customer = $this->session->getCustomer();
            if (!$this->getProduct($this->getQuote($id))->getSellerId()) {
                $dataSender = $data;
                $dataSender['template'] = $this->helper::EMAIL_TEMPLATE_NOTICE_SENDER;
                $dataSender['receiver_email'] = $customer->getEmail();
                $dataSender['receiver'] = $this->scopeConfig->getValue($this->helper::XML_PATH_EMAIL_RECIPIENT, $storeScope);
                $this->helper->sendMailNotice($dataSender);

                $dataReceiver = $data;
                $dataReceiver['template'] = $this->helper::EMAIL_TEMPLATE_NOTICE_RECEIVER;
                $dataReceiver['receiver_email'] = $this->scopeConfig->getValue($this->helper::XML_PATH_EMAIL_RECIPIENT, $storeScope);
                $dataReceiver['sender_name'] = $customer->getName();
                $this->helper->sendMailNotice($dataReceiver);
            } else {
                $data['product_id'] = $this->getQuote($id)->getProductId();
                $this->_eventManager->dispatch('lof_quickrfq_send_after', [ 'data' => $data, 'controller' => $this, 'model' => $this->getQuote($id)]);
            }

            $this->messageManager->addSuccessMessage(__('Send message successfully'));
            return $resultRedirect->setPath('*/*/view', ['quickrfq_id' => $id]);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());

            return $resultRedirect;
        }
    }

    /**
     * @param $quoteId
     * @return mixed
     */
    public function getQuote($quoteId)
    {
        if (!isset($this->_currentQuote[$quoteId])) {
            $this->_currentQuote[$quoteId] = $this->quickrfq->create()->load($quoteId);
        }
        return $this->_currentQuote[$quoteId];
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
     * @param $quote
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct($quote)
    {
        $productId = $quote->getProductId();
        try {
            $product = $this->productRepository->getById($productId);
            return ! empty($product->getId()) ? $product : null;
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }
}
