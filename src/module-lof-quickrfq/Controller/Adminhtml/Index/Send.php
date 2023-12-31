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
namespace Lof\Quickrfq\Controller\Adminhtml\Index;

use Lof\Quickrfq\Helper\Data;
use Magento\Backend\App\Action;
use Lof\Quickrfq\Model\QuickrfqFactory;
use Lof\Quickrfq\Model\MessageFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Send
 * @package Lof\Quickrfq\Controller\Adminhtml\Index
 */
class Send extends \Magento\Backend\App\Action
{
    /**
     * @var PostDataProcessor
     */
    protected $dataProcessor;
    /**
     * @var QuickrfqFactory
     */
    private $quickrfq;
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
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;

    /**
     * @param Action\Context $context
     * @param PostDataProcessor $dataProcessor
     * @param ScopeConfigInterface $scopeConfig
     * @param QuickrfqFactory $quickrfq
     * @param Data $data
     * @param Customer $customer
     * @param MessageFactory $messageFactory
     * @param \Magento\Backend\Model\Auth\Session $authSession
     */
    public function __construct(
        Action\Context $context,
        PostDataProcessor $dataProcessor,
        ScopeConfigInterface $scopeConfig,
        QuickrfqFactory $quickrfq,
        Data $data,
        MessageFactory $messageFactory,
        \Magento\Backend\Model\Auth\Session $authSession
    ) {
        $this->dataProcessor = $dataProcessor;
        $this->quickrfq = $quickrfq;
        $this->helper = $data;
        $this->scopeConfig = $scopeConfig;
        $this->message = $messageFactory;
        $this->authSession = $authSession;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_Quickrfq::send');
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('quickrfq_id');
        $resultRedirect->setPath('*/*/index');

        try {
            if (!$this->isQuoteExist($id)) {
                throw new \Exception(__('The quote does not exist.'));
            }


            if (!isset($data['message'])) {
                throw new \Exception(__('Please fill message box to send this message.'));
            }

            if (!$this->getRequest()->isPost() || !$data) {
                throw new \Exception(__('Somethings went wrong while send this message.'));
            }

            $model = $this->message->create();
            $data['message'] = strip_tags($data['message']);
            $data['message'] = $this->helper->xss_clean($data['message']);
            $data['quickrfq_id'] = $id;

            $model->setData($data);
            $model->save();

            $quote = $this->getQuote($id);
            if ($quote->getStatus() == \Lof\Quickrfq\Model\Quickrfq::STATUS_NEW || $quoteModel->getStatus() == \Lof\Quickrfq\Model\Quickrfq::STATUS_RE_NEW) {
                $quote->setStatus(\Lof\Quickrfq\Model\Quickrfq::STATUS_PROCESSING);
                $user = $this->authSession->getUser();
                $user_name = $user->getFirstname() . ' ' . $user->getLastname();
                $quote->setData("user_id", $user->getUserId());
                $quote->setData("user_name", $user_name);
                $quote->save();
            }
            $dataReceiver = $data;

            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

            $dataReceiver['template'] = $this->helper::EMAIL_TEMPLATE_NOTICE_RECEIVER;
            $dataReceiver['receiver_email'] = $quote->getEmail();

            $this->helper->sendMailNotice($dataReceiver);

            $dataSender = $data;
            $dataSender['receiver'] = $quote->getContactName();
            $dataSender['template'] = $this->helper::EMAIL_TEMPLATE_NOTICE_SENDER;
            $dataSender['receiver_email'] = $this->scopeConfig->getValue($this->helper::XML_PATH_EMAIL_RECIPIENT, $storeScope);

            $this->helper->sendMailNotice($dataSender);

            $this->messageManager->addSuccessMessage(__('Send message successfully'));

            return $resultRedirect->setPath('*/*/edit', ['quickrfq_id' => $id]);
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
}
