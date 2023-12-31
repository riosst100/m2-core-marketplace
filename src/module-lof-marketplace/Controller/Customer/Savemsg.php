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
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Controller\Customer;

use Lof\MarketPlace\Model\SellerFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\App\Action\Context;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Savemsg extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @var Magento\Framework\App\Action\Session
     */
    protected $session;

    /**
     * @var Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Lof\MarketPlace\Model\Sender
     */
    protected $sender;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $helper;

    /**
     * @var \Lof\MarketPlace\Model\SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var \Lof\MarketPlace\Model\SellerFactory
     */
    protected $messageFactory;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Lof\MarketPlace\Model\MessageDetailFactory
     */
    protected $messageDetailFactory;

    /**
     * Savemsg constructor.
     *
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lof\MarketPlace\Model\Sender $sender
     * @param \Lof\MarketPlace\Model\MessageFactory $messageFactory
     * @param SellerFactory $sellerFactory
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param CustomerFactory $customerFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Lof\MarketPlace\Model\MessageDetailFactory $messageDetailFactory
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Model\Sender $sender,
        \Lof\MarketPlace\Model\MessageFactory $messageFactory,
        SellerFactory $sellerFactory,
        \Lof\MarketPlace\Helper\Data $helper,
        CustomerFactory $customerFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Lof\MarketPlace\Model\MessageDetailFactory $messageDetailFactory
    ) {
        $this->helper = $helper;
        $this->messageFactory = $messageFactory;
        $this->sellerFactory = $sellerFactory;
        $this->sender = $sender;
        $this->session = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->customerFactory = $customerFactory;
        $this->messageDetailFactory = $messageDetailFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $customerSession = $this->session;
        $customerId = $customerSession->getCustomerId();
        $customer = $this->session->getCustomer();
        $data = $this->getRequest()->getPostValue();

        if ($customerSession->isLoggedIn()) {
            $id = $this->getRequest()->getParam('message_id');
            $sent = false;
            if (isset($data['seller_id']) && $data['seller_id']) {
                $seller = $this->getSellerByUrl($data['seller_id'], $customerId);
                $data = $this->helper->xss_clean_array($data);
                $message = $this->messageFactory->create();
                if ($id) {
                    $message->load($id);
                } else {
                    $message->setOwnerId($seller->getSellerId())
                        ->setSenderId($customerId)
                        ->setSenderEmail($customer->getEmail())
                        ->setSenderName($customer->getFirstname())
                        ->setSellerSend($seller->getSellerId())
                        ->setDescription($data['content'])
                        ->setReceiverId($seller->getSellerId())
                        ->setSubject(__('Customer send message'))
                        ->setStatus(1)
                        ->setIsRead(0)->save();
                }

                if ($data && $seller->getId() && $message->getId()) {
                    $messageDetailModel = $this->messageDetailFactory->create();
                    try {
                        $data['seller_send'] = 0;
                        $messageDetailModel
                            ->setMessageId($message->getId())
                            ->setSellerSend($seller->getSellerId())
                            ->setSenderId($customerId)
                            ->setSenderEmail($customer->getEmail())
                            ->setSenderName($customer['firstname'])
                            ->setReceiverId($seller->getSellerId())
                            ->setReceiverEmail($seller->getEmail())
                            ->setReceiverName($seller->getName())
                            ->setContent($data['content'])
                            ->setIsRead(1)
                            ->save();

                        $data['namestore'] = $customer['firstname'] . ' ' . $customer['lastname'];
                        $data['sender_name'] = $this->helper->getStoreName();
                        $data['receiver_email'] = $seller->getEmail();
                        $data['urllogin'] = $this->helper->getBaseStoreUrl().'customer/account/login';
                        $this->messageManager->addSuccessMessage(__('send contact success'));
                        if ($this->helper->getConfig('email_settings/enable_send_email')) {
                            $this->sender->replyMessage($data);
                        }
                        $id = $message->getId();
                        $sent = true;
                    } catch (\Magento\Framework\Exception\LocalizedException $e) {
                        $this->messageManager->addErrorMessage($e->getMessage());
                        $sent = false;
                    } catch (\RuntimeException $e) {
                        $this->messageManager->addErrorMessage($e->getMessage());
                        $sent = false;
                    } catch (\Exception $e) {
                        $this->messageManager->addExceptionMessage(
                            $e,
                            __('Something went wrong while saving the message.')
                        );
                        $sent = false;
                    }
                }
            }
            if ($sent && $id) {
                $this->_redirect('lofmarketplace/customer/viewmessage/message_id/' . $id);
            } else {
                $this->_redirect('lofmarketplace/customer/message');
            }
        } else {
            $this->messageManager->addNoticeMessage(__('You must have a account to access'));
            $this->_redirect('account/customer/login');
        }
    }

    /**
     * get seller by sellerUrl
     *
     * @param string $sellerUrl
     * @param int $customerId
     * @return \Lof\MarketPlace\Model\Seller
     */
    protected function getSellerByUrl(string $sellerUrl, int $customerId = 0)
    {
        $collection = $this->sellerFactory->create()->getCollection();
        if (is_numeric($sellerUrl)) {
            $collection->addFieldToFilter('seller_id', ['eq' => (int)$sellerUrl]);
        } else {
            $collection->addFieldToFilter('url_key', ['eq' => $sellerUrl]);
        }
        $seller = $collection->addFieldToFilter("status", \Lof\MarketPlace\Model\Seller::STATUS_ENABLED)
                    ->addFieldToFilter("customer_id", ["neq" => $customerId])
                    ->getFirstItem();
        return $seller;
    }
}
