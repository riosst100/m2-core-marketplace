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

use Lof\Quickrfq\Controller\FileProcessor;
use Lof\Quickrfq\Helper\Data;
use Lof\Quickrfq\Model\Attachment\UploadHandler;
use Lof\Quickrfq\Model\AttachmentFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\View\Result\PageFactory;
use Lof\Quickrfq\Model\QuickrfqFactory;
use Lof\Quickrfq\Model\MessageFactory;
use Magento\Catalog\Model\ProductRepository;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Customer;

/**
 * Class Save
 *
 * @package Lof\Quickrfq\Controller\Quickrfq
 */
class Save extends Action
{
    /**
     * Recipient email config path
     */
    const XML_PATH_EMAIL_RECIPIENT = 'quickrfq/email/recipient';

    /**
     *
     */
    const XML_PATH_EMAIL_TEMPLATE_CUSTOMER = 'quickrfq/email/template_customer';

    /**
     *
     */
    const XML_PATH_EMAIL_TEMPLATE_ADMIN = 'quickrfq/email/template';
    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var Escaper
     */
    protected $_pageFactory;

    /**
     * @var QuickrfqFactory
     */
    protected $_quickRfqFactory;

    /**
     * @var \Lof\Quickrfq\Controller\FileProcessor
     */
    private $_fileProcessor;

    /**
     * @var \Lof\Quickrfq\Model\AttachmentFactory
     */
    private $_attachmentFactory;

    /**
     * @var UploadHandler
     */
    private $_uploadHandler;
    /**
     * @var ProductRepository|mixed
     */
    private $productRepository;
    /**
     * @var MessageFactory
     */
    private $_messageFactory;

    /**
     * @var \Lof\Quickrfq\Helper\Data
     */
    private $_helper;

    /**
     * @var Session
     */
    private $_customerSession;

    /**
     * @var Customer
     */
    private $customer;

    /**
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * Save constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     * @param \Lof\Quickrfq\Model\QuickrfqFactory $_quickRfqFactory
     * @param \Lof\Quickrfq\Model\AttachmentFactory $attachmentFactory
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Lof\Quickrfq\Controller\FileProcessor $fileProcessor
     * @param \Lof\Quickrfq\Helper\Data $helper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Lof\Quickrfq\Model\Attachment\UploadHandler $uploadHandler
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Lof\Quickrfq\Model\MessageFactory $messageFactory
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Magento\Customer\Model\Session $session
     * @param Validator $formKeyValidator
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        QuickrfqFactory $_quickRfqFactory,
        AttachmentFactory $attachmentFactory,
        StateInterface $inlineTranslation,
        FileProcessor $fileProcessor,
        Data $helper,
        ScopeConfigInterface $scopeConfig,
        ProductRepository $productRepository,
        UploadHandler $uploadHandler,
        StoreManagerInterface $storeManager,
        MessageFactory $messageFactory,
        Customer $customer,
        Session $session,
        Validator $formKeyValidator
    ) {
        $this->_pageFactory       = $pageFactory;
        $this->_quickRfqFactory   = $_quickRfqFactory;
        $this->_helper            = $helper;
        $this->_fileProcessor     = $fileProcessor;
        $this->_attachmentFactory = $attachmentFactory;
        $this->inlineTranslation  = $inlineTranslation;
        $this->scopeConfig        = $scopeConfig;
        $this->storeManager       = $storeManager;
        $this->_uploadHandler     = $uploadHandler;
        $this->productRepository  = $productRepository ?: ObjectManager::getInstance()->create(ProductRepository::class);
        $this->_messageFactory    = $messageFactory;
        $this->_customerSession   = $session;
        $this->customer           = $customer;
        $this->formKeyValidator   = $formKeyValidator;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $data           = $this->getRequest()->getParams();
        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());

        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $this->inlineTranslation->suspend();
        $configRequiredCustomerLogin = $this->_helper->getConfig('option/required_customer_login');
        $validFormKey                = $this->formKeyValidator->validate($this->getRequest());
        try {
            if ($configRequiredCustomerLogin && ! $this->_customerSession->isLoggedIn()) {
                $this->_customerSession->setAfterAuthUrl($this->_redirect->getRefererUrl());
                $this->_customerSession->authenticate();

                throw new \Exception(__('You must login to create a request for quote.'));
            }

            if (! $validFormKey || ! $this->getRequest()->isPost() || ! $data) {
                throw new \Exception(__('Somethings went wrong while create this quote.'));
            }

            if (! $this->productExists($this->getProductId())) {
                throw new \Exception(__('The product does not exist!'));
            }

            if (! $this->_customerSession->isLoggedIn()) {
                if (! ( filter_var($data['customer_email'], FILTER_VALIDATE_EMAIL) )) {
                    throw new \Exception(__('Please enter the correct email.'));
                }
            }

            if (! isset($data['unit'])) {
                $data['unit'] = '';
            }
            if (! $this->_customerSession->isLoggedIn()) {
                if (! empty($data['customer_name'])) {
                    $data['customer_name'] = preg_replace([ '#[\\s-]+#', '#[^A-Za-z0-9. -]+#' ], '', $data['customer_name']);
                    $data['customer_name'] = trim($data['customer_name']);
                }

                if (! isset($data['customer_email'])) {
                    $data['customer_email'] = '';
                }
            } else {
                $data['customer_name'] = $this->_customerSession->getCustomer()->getName();
                $data['customer_email'] = $this->_customerSession->getCustomer()->getEmail();
            }

            if ($this->_customerSession->isLoggedIn()) {
                $data['customer_id'] = $this->_customerSession->getCustomerId();
            } else {
                $customer = $this->customer->setWebsiteId(1)->loadByEmail($data['customer_email']);
                if ($customer->getData()) {
                    $data['customer_id'] = $customer->getId();
                } else {
                    $data['customer_id'] = 0;
                }
            }
            $model = $this->_quickRfqFactory->create();
            $product = $model->getProduct((int)$data['product_id']);
            $data['comment'] = strip_tags($data['comment']);
            $data['comment'] = $this->_helper->xss_clean($data['comment']);
            $model->setData([
                'contact_name'      => $data['customer_name'],
                'email'             => $data['customer_email'],
                'phone'             => $data['customer_phone'],
                'product_id'        => $data['product_id'],
                'quantity'          => $data['quantity'],
                'date_need_quote'   => $data['date_need_quote'],
                'comment'           => $data['comment'],
                'customer_id'       => $data['customer_id'],
                'price_per_product' => $data['price_per_product'],
                'store_id'          => $this->storeManager->getStore()->getId(),
                'store_currency_code' => $this->storeManager->getStore()->getCurrentCurrency()->getCode(),
                'product_sku'       => $product?$product->getSku():null,
                'attributes'        => isset($data["attributes"])?$data["attributes"]:null,
                'info_buy_request'  => isset($data["info_buy_request"])?$data["info_buy_request"]:null,
            ])->save();

            $modelMessage = $this->_messageFactory->create();
            $messageData  = [
                'quickrfq_id' => $model->getQuickrfqId(),
                'message'     => $data['comment'],
                'customer_id' => $data['customer_id'],
                'is_main'     => 1,
            ];

            $modelMessage->setData($messageData);
            $modelMessage->save();
            $files   = $this->_fileProcessor->getFiles();
            $quoteId = $model->getQuickrfqId();
            if ($files) {
                foreach ($files as $file) {
                    $this->_uploadHandler->process($file, $quoteId);
                }
            }
            $templateForAdmin    = $this->scopeConfig->getValue(self::XML_PATH_EMAIL_TEMPLATE_ADMIN, $storeScope);
            $templateForCustomer = $this->scopeConfig->getValue(self::XML_PATH_EMAIL_TEMPLATE_CUSTOMER, $storeScope);
            $emailRecipientCustomer = $data['customer_email'];

            if (!$this->getCurrentProduct($this->getProductId())->getSellerId()) {
                //send email to customer
                $emailRecipientAdmin = $this->scopeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT, $storeScope);
                $data['is_admin']       = false;
                $data['product_name']   = $model->getProductName();
                $data['receiver_name']  = $emailRecipientAdmin;
                $this->_helper->sendEmail($data, $emailRecipientCustomer, $templateForCustomer);

                //send email to admin
                $data['is_admin']       = true;
                $data['receiver_name']  = $model->getContactName();
                $this->_helper->sendEmail($data, $emailRecipientAdmin, $templateForAdmin);
            } else {
                $this->_eventManager->dispatch('lof_quickrfq_save_after', [ 'data' => $data, 'controller' => $this, 'template_admin' => $templateForAdmin, 'template_customer' => $templateForCustomer, 'model' => $model ]);
            }

            $this->messageManager->addSuccessMessage(__('Your request has been sent!'));

            return $resultRedirect;
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());

            return $resultRedirect;
        }
    }


    /**
     * Check product exists
     *
     * @param int $productId
     * @return bool
     */
    private function productExists($productId)
    {
        $product = $this->getCurrentProduct($productId);

        return $product && ! empty($product->getId());
    }

    /**
     * Product ID
     *
     * @return int
     */
    public function getProductId()
    {
        return intval($this->getRequest()->getParam('product_id'));
    }

    /**
     * @param $productId
     * @return bool|\Magento\Catalog\Api\Data\ProductInterface|mixed|null
     */
    public function getCurrentProduct($productId)
    {
        try {
            $product = $this->productRepository->getById($productId);

            return ! empty($product->getId()) ? $product : null;
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }
}
