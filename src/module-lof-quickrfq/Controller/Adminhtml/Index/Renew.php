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
 * Class Renew
 * @package Lof\Quickrfq\Controller\Adminhtml\Index
 */
class Renew extends \Magento\Backend\App\Action
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
     * @param Action\Context $context
     * @param PostDataProcessor $dataProcessor
     * @param ScopeConfigInterface $scopeConfig
     * @param QuickrfqFactory $quickrfq
     * @param Data $data
     * @param Customer $customer
     * @param MessageFactory $messageFactory
     */
    public function __construct(
        Action\Context $context,
        PostDataProcessor $dataProcessor,
        ScopeConfigInterface $scopeConfig,
        QuickrfqFactory $quickrfq,
        Data $data,
        MessageFactory $messageFactory
    ) {
        $this->dataProcessor = $dataProcessor;
        $this->quickrfq = $quickrfq;
        $this->helper = $data;
        $this->scopeConfig = $scopeConfig;
        $this->message = $messageFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_Quickrfq::quickrfq_save');
    }

    /**
     * Delete action
     *
     */
    public function execute()
    {
        // check if we know what should be approved
        $id = $this->getRequest()->getParam('quickrfq_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            $contact_name = "";
            try {
                // init model and delete
                $quoteModel = $this->getQuote($id);
                $contact_name = $quoteModel->getContactName();
                $quoteModel->setData("status", \Lof\Quickrfq\Model\Quickrfq::STATUS_RE_NEW);
                $quoteModel->setData("expiry", null);
                $quoteModel->save();
                //Process create Cart Record for customer at here
                $variableData = $quoteModel->getData();
                //Process send notification message
                $data = [
                    'message' => $this->helper->getRenewQuoteNotifyText($variableData),
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
                $this->messageManager->addSuccess(__('The record has been re-new.'));
                // go to grid
                $this->_eventManager->dispatch(
                    'adminhtml_quickrfq_on_renew',
                    ['contact_name' => $contact_name, 'quote' => $quoteModel, 'status' => 'success']
                );
                return $resultRedirect->setPath('*/*/edit', ['quickrfq_id' => $id]);
            } catch (\Exception $e) {
                $this->_eventManager->dispatch(
                    'adminhtml_quickrfq_on_renew',
                    ['contact_name' => $contact_name, 'quote' => null, 'status' => 'fail']
                );
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['quickrfq_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find a record to re-new.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
    /**
     * @param $quoteId
     * @return mixed
     */
    public function getQuote($quoteId)
    {

        return $this->quickrfq->create()->load($quoteId);
    }
}
