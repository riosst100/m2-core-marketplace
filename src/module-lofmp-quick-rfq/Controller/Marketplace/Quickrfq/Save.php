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
use Magento\Backend\App\Action;
use Lof\Quickrfq\Model\QuickrfqFactory;
use Lof\Quickrfq\Model\MessageFactory;
use Lof\Quickrfq\Model\Quickrfq;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Action\Context;
/**
 * Class Save
 * @package Lof\Quickrfq\Controller\Adminhtml\Index
 */
class Save extends \Magento\Framework\App\Action\Action
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
     * @var PostDataProcessor
     */
    protected $dataProcessor;

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
        // check if we know what should be save
        $data = $this->getRequest()->getPostValue();
        $id = isset($data['quickrfq_id'])?(int)$data['quickrfq_id']:0;
        if ($id) {
            $contact_name = "";
            try {
                // init model and delete
                $quoteModel = $this->getQuote($id);
                $admin_price = isset($data['admin_price'])?(float)$data['admin_price']:0;
                $admin_quantity = isset($data['admin_quantity'])?(int)$data['admin_quantity']:0;
                $coupon_code = isset($data['coupon_code'])?strip_tags($data['coupon_code']):"";

                $old_admin_price = $quoteModel->getAdminPrice();
                $old_admin_quantity = $quoteModel->getAdminQuantity();
                $old_coupon_code = $quoteModel->getCouponCode();

                $is_update = false;
                if ($quoteModel->getId() && ($admin_price || $admin_quantity) && ($admin_price !=  $old_admin_price || $admin_quantity != $old_admin_quantity || $coupon_code != $old_coupon_code)) {
                    $is_update = true;
                }
                if ($is_update) {
                    $contact_name = $quoteModel->getContactName();
                    if ($admin_price) {
                        $quoteModel->setData("admin_price", $admin_price);
                    }
                    if ($admin_quantity) {
                        $quoteModel->setData("admin_quantity", $admin_quantity);
                    }
                    $quoteModel->setData("coupon_code", $coupon_code);
                    if ($quoteModel->getStatus() == Quickrfq::STATUS_NEW || $quoteModel->getStatus() == Quickrfq::STATUS_RE_NEW) {
                        $quoteModel->setStatus(Quickrfq::STATUS_PROCESSING);
                    }
                    $quoteModel->setData("user_id", null);
                    $quoteModel->setData("user_name", null);

                    $product = $quoteModel->getProduct($quoteModel->getProductId());

                    if (!$quoteModel->getProductSku()) {
                        $product_sku = $product?$product->getSku():null;
                        $quoteModel->setData("product_sku", $product_sku);
                    }
                    //Save Quote Model Data
                    $quoteModel->save();
                    $variableData = $quoteModel->getData();
                    //Process send notification message
                    $data = [
                        'message' => $this->helper->getUpdateQuoteNotifyText($variableData),
                        'quickrfq_id' => $id
                    ];
                    if ($data['message'] && $quoteModel->getStatus() !== Quickrfq::STATUS_CLOSE) {
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
                    $this->messageManager->addSuccess(__('The record has been saved.'));
                    // go to grid
                    $this->_eventManager->dispatch(
                        'adminhtml_quickrfq_on_save',
                        ['contact_name' => $contact_name, 'quote' => $quoteModel, 'status' => 'success']
                    );
                }
                $this->_redirectUrl($this->getFrontendUrl('marketplace/quickrfq/quickrfq').'view/quickrfq_id/'.$id);
                return true;
            } catch (\Exception $e) {
                $this->_eventManager->dispatch(
                    'adminhtml_quickrfq_on_save',
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
        $this->messageManager->addError(__('We can\'t find a record to update or not have any changes.'));
        // go to grid
        $this->_redirectUrl($this->getFrontendUrl('marketplace/quickrfq/quickrfq'));
        return true;
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
