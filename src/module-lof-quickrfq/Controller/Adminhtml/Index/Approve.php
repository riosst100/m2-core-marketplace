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
use Lof\Quickrfq\Helper\ConvertQuote;
use Magento\Backend\App\Action;
use Lof\Quickrfq\Model\QuickrfqFactory;
use Lof\Quickrfq\Model\MessageFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Approve
 * @package Lof\Quickrfq\Controller\Adminhtml\Index
 */
class Approve extends \Magento\Backend\App\Action
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
        return $this->_authorization->isAllowed('Lof_Quickrfq::quickrfq_approve');
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
                //Process create Cart Record for customer at here
                //$cartId = $this->convertQuoteHelper->processCreateCart($quoteModel);
                //if ($cartId) {
                    
                    $expiryNumberDays = $this->helper->getConfig("quote_process/expiry_day");
                    $today = $this->helper->getTimezoneDateTime();
                    $expiryTime = strtotime($today."+".$expiryNumberDays." days");
                    $expiryDate = date("Y-m-d H:i:s", $expiryTime);

                    $quoteModel->setData("status", \Lof\Quickrfq\Model\Quickrfq::STATUS_APPROVE);
                    $quoteModel->setData("expiry", $expiryDate);
                    $quoteModel->save();
                    
                    $variableData = $quoteModel->getData();
                    //Process send notification message
                    $data = [
                        'message' => $this->helper->getApproveQuoteNotifyText($variableData),
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
                    $this->messageManager->addSuccess(__('The record has been approved.'));
                    // go to grid
                    $this->_eventManager->dispatch(
                        'adminhtml_quickrfq_on_approve',
                        ['contact_name' => $contact_name, 'quote' => $quoteModel, 'status' => 'success']
                    );
                // } else {
                //     $this->_eventManager->dispatch(
                //         'adminhtml_quickrfq_on_approve',
                //         ['contact_name' => $contact_name, 'quote' => null, 'status' => 'fail']
                //     );
                //     // display error message
                //     $this->messageManager->addError(__('Can not create shopping cart for the quote at now. Please try again!'));
                // }
            } catch (\Exception $e) {
                $this->_eventManager->dispatch(
                    'adminhtml_quickrfq_on_approve',
                    ['contact_name' => $contact_name, 'quote' => null, 'status' => 'fail']
                );
                // display error message
                $this->messageManager->addError($e->getMessage());
            }
            // go back to edit form
            return $resultRedirect->setPath('*/*/edit', ['quickrfq_id' => $id]);
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find a record to approve.'));
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
