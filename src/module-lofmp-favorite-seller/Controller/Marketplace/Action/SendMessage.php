<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://www.landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_FavoriteSeller
 * @copyright  Copyright (c) 2018 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\FavoriteSeller\Controller\Marketplace\Action;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class SendMessage extends Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Lof\MarketPlace\Model\SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $marketplaceHelper;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Lofmp\FavoriteSeller\Helper\ConfigData $configData
     */
    protected $configData;

    /**
     * @var \Lofmp\FavoriteSeller\Helper\Sender $sender
     */
    protected $sender;

    /**
     * @var \Magento\Customer\Model\CustomerFactory $customerFactory
     */
    protected $customerFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerFactory
     * @param \Lof\MarketPlace\Helper\Data $marketplaceHelper
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Lofmp\FavoriteSeller\Helper\ConfigData $configData
     * @param \Lofmp\FavoriteSeller\Helper\Sender $sender
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Lof\MarketPlace\Helper\Data $marketplaceHelper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Lofmp\FavoriteSeller\Helper\ConfigData $configData,
        \Lofmp\FavoriteSeller\Helper\Sender $sender,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->sellerFactory = $sellerFactory;
        $this->marketplaceHelper = $marketplaceHelper;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->configData = $configData;
        $this->sender = $sender;
        $this->customerFactory = $customerFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if ($this->configData->isEnable() && $this->configData->getEmailConfig("enable")) {
            $sellerSession = $this->customerSession;
            $sellerId = $this->marketplaceHelper->getSellerId();

            $seller = $this->sellerFactory->create()->load($sellerId,'seller_id');
            $status = $seller?$seller->getStatus():0;
            $result = $this->resultJsonFactory->create();
            if ($sellerSession->isLoggedIn() && $status == 1) {
                $data = $this->getRequest()->getPostValue();
                $data = $this->configData->xss_clean_array($data);
                $emailAddresses = $data['email_addresses'];
                $subject = isset($data['subject'])?$data['subject']:"";
                $content = isset($data['content'])?$data['content']:"";
                if($this->getRequest()->isAjax() && $data && isset($data['email_addresses']) && $data['email_addresses'] && $content) {
                    foreach($emailAddresses as $emailAddress){
                        $customer = $this->getCustomerByEmail($emailAddress);
                        if ($customer) {
                            $sendData = [
                                "customer_name" => $customer->getFirstname()." ".$customer->getLastname(),
                                "subject" => $subject,
                                "message" => $content
                            ];
                            $this->sender->sendEmailToSubscriber($emailAddress, $seller, $sendData);
                        }
                    }

                    $responseMessage = ['status' => 'success' ,'Message' => __("Success"), 'message' => __("Success")];
                    return $result->setData($responseMessage);
                }
            }
            return $result->setData(['status' => 'error', 'error' => __("You dont have permission to access this feature.")]);
        } else {
            $this->messageManager->addNotice(__('You dont have permission to access this feature.'));
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/catalog/dashboard/'));
        }
    }

    /**
     * Get customer by email
     * 
     * @param string $email
     * @return Object|null
     */
    public function getCustomerByEmail($email)
    {
        $customerFactory = $this->customerFactory->create();
        $customerData    = $customerFactory->getCollection()->addFieldToFilter("email", $email)->getFirstItem();
        if ($customerData) {
            return $customerData;
        }
        return null;
    }
}
