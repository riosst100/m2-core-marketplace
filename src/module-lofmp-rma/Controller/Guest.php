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



namespace Lofmp\Rma\Controller;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

abstract class Guest extends Action
{
    /**
     * List of actions that are allowed for not authorized users.
     *
     * @var string[]
     */
    protected $openActions = [
        'external',
        'postexternal',
        'newbundlerma',
        'print',
    ];

    public function __construct(
        \Magento\Framework\Session\SessionManagerInterface $sessionObj,
        \Magento\Customer\Model\Session $customerSession,
        Context $context
    ) {
        $this->customerSession = $customerSession;
        $this->sessionObj = $sessionObj;
        $this->context = $context;
        $this->resultFactory = $context->getResultFactory();

        parent::__construct($context);
    }

    public function setSessionOrder($orderID)
    {
        $this->sessionObj->start();
        $this->sessionObj->setGuestOrderId($orderID);
    }

    public function setSessionEmail($email)
    {
        $this->sessionObj->start();
        $this->sessionObj->setGuestEmail($email);
    }

    public function getSessionOrder()
    {
        $this->sessionObj->start();
        return $this->sessionObj->getGuestOrderId();
    }

    public function getSessionEmail()
    {
        $this->sessionObj->start();
        return $this->sessionObj->getGuestEmail();
    }
    public function unSetValue()
    {
        $this->sessionObj->start();
        return $this->sessionObj->unsGuestOrderId() && $this->sessionObj->unsGuestEmail();
    }
    public function isLoggedIn()
    {
        $orderId = $this->getSessionOrder();
        $email = $this->getSessionEmail();
        if ($orderId && $email) {
            return true;
        } else {
            return false;
        }
    }
}
