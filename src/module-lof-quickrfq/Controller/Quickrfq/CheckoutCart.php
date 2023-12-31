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
use Lof\Quickrfq\Helper\ConvertQuote;
use Lof\Quickrfq\Model\QuickrfqFactory;
use Lof\Quickrfq\Model\Quickrfq;
use Lof\Quickrfq\Model\MessageFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Checkout\Model\Cart;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class CheckoutCart
 * @package Lof\Quickrfq\Controller\Quickrfq
 */
class CheckoutCart extends Action
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
     * @var ConvertQuote
     */
    protected $convertQuoteHelper;

    /**
     * @var QuickrfqFactory
     */
    protected $_quickRfqFactory;

    /**
     * @var ConvertQuote
     */
    protected $_convertQuoteHelper;

    /**
     * @var Cart
     */
    protected $mageCart;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var MessageFactory
     */
    private $message;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * CheckoutCart constructor.
     * @param Context $context
     * @param PageFactory $pageFactory
     * @param Data $data
     * @param Session $session
     * @param PageFactory $resultPageFactory
     * @param \Magento\Framework\UrlInterface $urlInterface
     * @param ConvertQuote $convertQuoteHelper
     * @param QuickrfqFactory $quickRfqFactory
     * @param Cart $mageCart
     * @param CheckoutSession $checkoutSession
     * @param MessageFactory $messageFactory
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        Data $data,
        Session $session,
        PageFactory $resultPageFactory,
        \Magento\Framework\UrlInterface $urlInterface,
        ConvertQuote $convertQuoteHelper,
        QuickrfqFactory $quickRfqFactory,
        Cart $mageCart,
        CheckoutSession $checkoutSession,
        MessageFactory $messageFactory,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->_pageFactory = $pageFactory;
        $this->_customerSession = $session;
        $this->_helper = $data;
        $this->resultPageFactory = $resultPageFactory;
        $this->_urlInterface     = $urlInterface;
        $this->_convertQuoteHelper = $convertQuoteHelper;
        $this->_quickRfqFactory   = $quickRfqFactory;
        $this->mageCart          = $mageCart;
        $this->checkoutSession = $checkoutSession;
        $this->message = $messageFactory;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $this->preInit();
        $quoteId = $this->getRequest()->getParam('quickrfq_id');
        if (! $quoteId) {
            return false;
        }
        //get cart id and convert to shopping cart and checkout at here.
        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        try {
            $quoteModel = $this->_quickRfqFactory->create()->load($quoteId);
            
            if (!$quoteModel->getId() || $this->_helper->isExpiryQuote($quoteModel)) {
                return false;
            }

            $mageQuote = $this->mageCart->getQuote();
            //if clear current cart if disable config Keep Old Cart Items
            if (!$this->_helper->getConfig("quote_process/keep_cart_item")) {  
                $items     = $mageQuote?$mageQuote->getAllItems():[];
                foreach ($items as $item) {
                    $mageQuote->removeItem($item->getId());
                }
            }
            //Process create Cart Record for customer at here
            $cartId = $this->_convertQuoteHelper->processCreateCart($quoteModel);
            if ($cartId) {
                // display success message
                $this->messageManager->addSuccess(__('The quote was converted to shopping cart sucessfully!'));
                $resultRedirect->setPath('checkout/cart');

                //save data into table lof_quickrfq_cart;
                $quoteModel->setData("status", Quickrfq::STATUS_DONE);
                $quoteModel->setData("expiry", null);
                $quoteModel->save();

                //Send message
                $model = $this->message->create();
                $data = [
                    'quickrfq_id' => $quoteId,
                    'customer_id' => $this->_customerSession->getCustomerId(),
                    'message' => __('The quote was converted to shopping cart sucessfully!')
                ];
                $model->setData($data);
                $model->save();

                //Notify new message to admin
                $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                $customer = $this->_customerSession->getCustomer();
                if (!$quoteModel->getProduct($quoteModel->getProductId())->getSellerId()) {
                    $dataReceiver = $data;
                    $dataReceiver['template'] = $this->_helper::EMAIL_TEMPLATE_NOTICE_RECEIVER;
                    $dataReceiver['receiver_email'] = $this->scopeConfig->getValue($this->_helper::XML_PATH_EMAIL_RECIPIENT, $storeScope);
                    $dataReceiver['sender_name'] = $customer->getName();
                    $this->_helper->sendMailNotice($dataReceiver);
                }

                // go to cart
                $this->_eventManager->dispatch(
                    'frontend_quickrfq_on_checkoutcart',
                    ['quote_id' => $quoteId, 'cart_id' => $cartId, 'data' => $data, 'model' => $quoteModel, 'status' => 'success']
                );
            } else {
                // display success message
                $this->messageManager->addSuccess(__('We can not convert quote to shopping cart.'));
                $resultRedirect->setPath('quickrfq/quickrfq/view', ['quickrfq_id' => $quoteId]);
                // go to quote view detail
                $this->_eventManager->dispatch(
                    'frontend_quickrfq_on_checkoutcart',
                    ['quote_id' => $quoteId, 'cart_id' => null, 'data' => [], 'model' => null, 'status' => 'fail']
                );
            }
        } catch (\Exception $e) {
            $this->_eventManager->dispatch(
                'frontend_quickrfq_on_checkoutcart',
                ['quote_id' => $quoteId, 'cart_id' => null, 'data' => [], 'model' => null, 'status' => 'fail']
            );
            // display error message
            $this->messageManager->addError($e->getMessage());

            $resultRedirect->setPath('quickrfq/quickrfq/view', ['quickrfq_id' => $quoteId]);
        }
        return $resultRedirect;
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
