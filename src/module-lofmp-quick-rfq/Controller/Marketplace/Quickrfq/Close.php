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
namespace Lofmp\Quickrfq\Controller\Marketplace\Quickrfq;

use Lof\Quickrfq\Helper\Data;
use Lof\Quickrfq\Model\QuickrfqFactory;
use Lof\Quickrfq\Model\MessageFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Action\Context;

/**
 * Class Close
 * @package Lof\Quickrfq\Controller\Adminhtml\Index
 */
class Close extends \Magento\Framework\App\Action\Action
{
    /**
     * @var Data
     */
    private $helper;
    /**
     * @var MessageFactory
     */
    private $message;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    const FLAG_IS_URLS_CHECKED = 'check_url_settings';

    /**
     * @var \Magento\Framework\Url
     */
    protected $_frontendUrl;

    /**
     * @var mixed|string|int|bool|null
     */
    protected $_actionFlag;

    /**
     * @var \Lof\MarketPlace\Model\SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $sellerHelper;

    /**
     * @param Context $context
     * @param PostDataProcessor $dataProcessor
     * @param ScopeConfigInterface $scopeConfig
     * @param QuickrfqFactory $quickrfq
     * @param Data $data
     * @param Customer $customer
     * @param MessageFactory $messageFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerFactory
     * @param \Magento\Framework\Url $frontendUrl
     * @param \Lof\MarketPlace\Helper\Data $sellerHelper
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PostDataProcessor $dataProcessor,
        ScopeConfigInterface $scopeConfig,
        QuickrfqFactory $quickrfq,
        Data $data,
        MessageFactory $messageFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Magento\Framework\Url $frontendUrl,
        \Lof\MarketPlace\Helper\Data $sellerHelper,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->dataProcessor = $dataProcessor;
        $this->quickrfq = $quickrfq;
        $this->helper = $data;
        $this->scopeConfig = $scopeConfig;
        $this->message = $messageFactory;

        $this->_frontendUrl = $frontendUrl;
        $this->_actionFlag =  $context->getActionFlag();
        $this->sellerFactory     = $sellerFactory;
        $this->session           = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->sellerHelper           = $sellerHelper;

        parent::__construct($context);
    }
    /**
     * Delete action
     *
     */
    public function execute()
    {
        $customerSession = $this->session;
        $customerId = $customerSession->getId();
        $seller = $this->sellerFactory->create()->load($customerId, 'customer_id');
        $status = $seller?$seller->getStatus():0;
        if ($customerSession->isLoggedIn() && $status == 1 && $seller) {
            $this->processControllerAction($seller);
        } elseif ($customerSession->isLoggedIn() && $status == 0) {
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/becomeseller'));
        } else {
            $this->messageManager->addNotice(__('You must have a seller account to access'));
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/login'));
        }
    }

    /**
     * processControllerAction
     * 
     * @param Object|null $seller
     * 
     */
    protected function processControllerAction($seller = null) 
    {
        // check if we know what should be close
        $id = $this->getRequest()->getParam('quickrfq_id');
        if ($id) {
            $contact_name = "";
            try {
                // init model and delete
                $quoteModel = $this->getQuote($id);
                $contact_name = $quoteModel->getContactName();
                $quoteModel->setData("status", \Lof\Quickrfq\Model\Quickrfq::STATUS_CLOSE);
                $quoteModel->setData("expiry", null);
                $quoteModel->save();
                //Process create Cart Record for customer at here
                $variableData = $quoteModel->getData();
                //Process send notification message
                $data = [
                    'message' => $this->helper->getCloseQuoteNotifyText($variableData),
                    'quickrfq_id' => $id
                ];
                if ($data['message']) {
                    $messageModel = $this->message->create();
                    $messageModel->setData($data);
                    //Save Message Data
                    $messageModel->save();

                    $dataReceiver = $data;
                    $dataReceiver['template'] = $this->helper::EMAIL_TEMPLATE_NOTICE_RECEIVER;
                    $dataReceiver['receiver_email'] = $quoteModel->getEmail();
                    //Send email notification to customer
                    $this->helper->sendMailNotice($dataReceiver);
                }
                // display success message
                $this->messageManager->addSuccess(__('The record has been closed.'));
                // go to grid
                $this->_eventManager->dispatch(
                    'seller_quickrfq_on_close',
                    ['contact_name' => $contact_name, 'quote' => $quoteModel, 'status' => 'success']
                );
                $this->_redirectUrl($this->getFrontendUrl('marketplace/quickrfq/quickrfq').'view/quickrfq_id/'.$id);
                return true;
            } catch (\Exception $e) {
                $this->_eventManager->dispatch(
                    'seller_quickrfq_on_close',
                    ['contact_name' => $contact_name, 'quote' => null, 'status' => 'fail']
                );
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                $this->_redirectUrl($this->getFrontendUrl('marketplace/quickrfq/quickrfq').'view/quickrfq_id/'.$id);
                return false;
            }
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find a record to closed.'));
        // go to grid
        $this->_redirectUrl($this->getFrontendUrl('marketplace/quickrfq/quickrfq'));
        return false;
    }
    /**
     * @param $quoteId
     * @return mixed
     */
    public function getQuote($quoteId)
    {

        return $this->quickrfq->create()->load($quoteId);
    }

    public function getFrontendUrl($route = '', $params = [])
    {
        return $this->_frontendUrl->getUrl($route, $params);
    }

    protected function _redirectUrl($url)
    {
        $this->getResponse()->setRedirect($url);
        $this->session->setIsUrlNotice($this->_actionFlag->get('', self::FLAG_IS_URLS_CHECKED));
        return $this->getResponse();
    }
}
