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

namespace Lof\MarketPlace\Controller\Seller;

use Magento\Framework\App\Action\Context;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Savemessage extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Lof\MarketPlace\Model\SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var \Lof\MarketPlace\Model\Sender
     */
    protected $sender;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_fileSystem;

    /**
     * Savemessage constructor.
     *
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Lof\MarketPlace\Model\Sender $sender
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Lof\MarketPlace\Model\Sender $sender,
        \Lof\MarketPlace\Helper\Data $helper,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->helper = $helper;
        $this->sender = $sender;
        $this->sellerFactory = $sellerFactory;
        $this->session = $customerSession;
        $this->_fileSystem = $filesystem;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $postData = $this->getRequest()->getPostValue();
        $sellerUrl = isset($postData["owner_id"]) ? $postData["owner_id"] : "";

        if ($postData && $sellerUrl) {
            $customer = $this->session->getCustomer();
            $customerId = 0;
            if ($customer && $customer->getId()) {
                $customerId = $customer->getId();
            }
            $seller = $this->getSellerByUrl($sellerUrl, $customerId);
            $data = [
                "sender_name" => isset($postData["sender_name"]) ? $postData["sender_name"] : "",
                "sender_email" => isset($postData["sender_email"]) ? $postData["sender_email"] : "",
                "sender_id" => isset($postData["sender_id"]) ? $postData["sender_id"] : "",
                "subject" => isset($postData["subject"]) ? strip_tags($postData["subject"]) : "",
                "description" => isset($postData["description"]) ? strip_tags($postData["description"]) : "",
                "currUrl" => isset($postData["currUrl"]) ? $postData["currUrl"] : "",
                "owner_id" => $seller ? $seller->getId() : 0
            ];
            $data['status'] = 1;
            $data['is_read'] = 0;

            if ($customer && $customer->getId()) {
                $data["sender_id"] = $customer->getId();
                $data["sender_name"] = $customer->getName();
                $data["sender_email"] = $customer->getEmail();
            }

            $this->_eventManager->dispatch(
                'marketplace_controller_before_save_message',
                [
                    'data' => $data,
                    'request' => $this
                ]
            );

            try {
                if ($seller && $seller->getSellerId()) {
                    $data = $this->helper->xss_clean_array($data);
                    $messageModel = $objectManager->get(\Lof\MarketPlace\Model\Message::class);
                    $messageModel->setData($data);
                    $messageModel->save();

                    $data['namestore'] = $this->helper->getStoreName();
                    $data['urllogin'] = $this->helper->getBaseStoreUrl().'customer/account/login';

                    $this->messageManager->addSuccessMessage('Thanks you for contact us!. Your message was sent successfully. We will reply you quickly.');

                    if ($this->helper->getConfig('email_settings/enable_send_email')) {
                        $this->sender->newMessage($data);
                    }

                    $this->_eventManager->dispatch(
                        'marketplace_controller_after_save_message',
                        [
                            'data' => $data,
                            'message' => $messageModel,
                            'request' => $this
                        ]
                    );

                } else {
                    $this->messageManager->addErrorMessage(__("Not found seller to send message."));
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the seller.'));
            }
            $this->_redirect($postData['currUrl']);
            return;
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('/');
        return $resultRedirect;
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
