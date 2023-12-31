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
namespace Lofmp\FavoriteSeller\Controller\Action;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class Add extends Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Lofmp\FavoriteSeller\Model\SubscriptionFactory
     */
    protected $subscriptionFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lofmp\FavoriteSeller\Model\SubscriptionFactory $subscriptionFactory
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Lofmp\FavoriteSeller\Model\SubscriptionFactory $subscriptionFactory
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->customerSession = $customerSession;
        $this->subscriptionFactory = $subscriptionFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if($this->customerSession->isLoggedIn()) {
            $result = $this->resultJsonFactory->create();
            if($this->getRequest()->isAjax()) {
                $data = $this->getRequest()->getPostValue();

                $customerId = $data['customer_id'];
                $sellerId = $data['seller_id'];

                $subscriptionModel = $this->subscriptionFactory->create();
                $subscriptionModel->setCustomerId($customerId);
                $subscriptionModel->setSellerId($sellerId);

                $subscriptionModel->save();


                $message = __(
                    'You added this seller to your favorite list.'
                );
                $this->messageManager->addSuccessMessage($message);

                $responseMessage = ['Message' => 'Success'];
                return $result->setData($responseMessage);
            }
            return $this->_redirect('customer/account/');
        }
    }
}
