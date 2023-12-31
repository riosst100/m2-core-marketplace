<?php
/**
 * LandOfCoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   LandOfCoder
 * @package    Lofmp_Rma
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */



namespace Lofmp\Rma\Controller;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

abstract class Rma extends Action
{
    /**
     * List of actions that are allowed for not authorized users.
     *
     * @var string[]
     */
    protected $openActions = [
        'external',
        'postexternal',
        'print',
    ];

    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        Context $context
    ) {
        $this->customerSession = $customerSession;
        $this->context = $context;
        $this->resultFactory = $context->getResultFactory();

        parent::__construct($context);
    }

   

    /**
     * {@inheritdoc}
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {

        if (!$this->getRequest()->isDispatched()) {
            parent::dispatch($request);
        }
        
        $action = strtolower($this->getRequest()->getActionName());
        $pattern = '/^('.implode('|', $this->openActions).')$/i';

        if (!preg_match($pattern, $action)) {
            if (!$this->customerSession->authenticate()) {
                $this->_actionFlag->set('', 'no-dispatch', true);
            }
        } else {
            $this->customerSession->setNoReferer(true);
        }
        $result = parent::dispatch($request);
        $this->customerSession->unsNoReferer(false);

        return $result;
    }

    /**
     * @param \Magento\Framework\Controller\ResultInterface $resultPage
     * @return void
     */
    protected function initPage(\Magento\Framework\Controller\ResultInterface $resultPage)
    {
        if ($navigationBlock = $resultPage->getLayout()->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('rma');
        }
    }
}
