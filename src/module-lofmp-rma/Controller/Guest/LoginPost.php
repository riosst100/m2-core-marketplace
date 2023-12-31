<?php
/**
 * LandOfCoder
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
 * @category   LandOfCoder
 * @package    Lofmp_Rma
 * @copyright  Copyright (c) 2020 Landofcoder (http://www.LandOfCoder.com/)
 * @license    http://www.LandOfCoder.com/LICENSE-1.0.html
 */



namespace Lofmp\Rma\Controller\Guest;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;

class LoginPost extends \Lofmp\Rma\Controller\Guest
{
    public function __construct(
        \Lofmp\Rma\Helper\Data                                    $datahelper,
        \Lofmp\Rma\Helper\Help                                    $helper,
        \Magento\Sales\Model\OrderFactory                       $orderFactory,
        \Magento\Framework\Event\ManagerInterface               $eventManager,
        \Magento\Framework\Registry                             $registry,
        \Magento\Customer\Model\Session                         $customerSession,
        \Magento\Framework\Session\SessionManagerInterface $sessionObj,
        Context                                                 $context
    ) {
        $this->customerSession      = $customerSession;
        $this->datahelper           = $datahelper;
        $this->helper               = $helper;
        $this->orderFactory         = $orderFactory;
        $this->eventManager         = $eventManager;
        $this->registry             = $registry;

        parent::__construct($sessionObj, $customerSession, $context);
    }


    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $data = $this->getRequest()->getParams();

        if (!$this->validate($data)) {
            return $resultRedirect->setPath('*/*/login');
        }

        try {
            /** @var \Magento\Sales\Model\Order $order */
            $order = $this->orderFactory->create()->loadByIncrementId($data['order_id']);
            if (!$order) {
                $this->messageManager->addErrorMessage('Error when login.');
                return $resultRedirect->setPath('*/*/login');
            }
            if ($order->getCustomerId()) {
                $this->messageManager->addErrorMessage('Could login as guest. Please login with your customer account before.');
                return $resultRedirect->setPath('*/*/login');
            } elseif (!$order->getCustomerId() && $order->getCustomerEmail() != $data['email']) {
                $this->messageManager->addErrorMessage('Could login as guest. Email address is not match.');
                return $resultRedirect->setPath('*/*/login');
            } else {
                $this->setSessionOrder($data['order_id']);
                $this->setSessionEmail($data['email']);
                $this->messageManager->addSuccessMessage(__('Login Successfully'));
                return $resultRedirect->setPath('*/*/rmalist');
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $resultRedirect->setPath('*/*/login');
        }
    }

    protected function validate($data)
    {
        if (!isset($data['order_id']) || !$data['order_id']) {
            return false;
        }
        if (!isset($data['email']) || !$data['email']) {
            return false;
        }
        $currentcustomer = $this->customerSession->getCustomer();
        if ($currentcustomer && $currentcustomer->getId()) {
            return false;
        }
        if ($this->isLoggedIn()) {
            return false;
        }
        return true;
    }
}
